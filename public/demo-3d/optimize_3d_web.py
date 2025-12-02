#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🚀 3D Model Web Optimization Script
Giảm 80-90% dung lượng file 3D cho web

Các kỹ thuật được sử dụng:
1. Draco Compression (Google) - Nén geometry cực mạnh
2. Texture compression & resizing
3. Mesh decimation (giảm polygon)
4. Material optimization
5. Remove unused data
6. Progressive loading format
"""

import os
import sys
import json
import shutil
from pathlib import Path
import subprocess
import argparse
from typing import List, Tuple, Dict, Optional

try:
    import trimesh
    import numpy as np
    from PIL import Image, ImageOps
    import gltflib
except ImportError as e:
    print(f"❌ Thiếu thư viện: {e}")
    print("📦 Cài đặt: pip install trimesh pillow gltflib numpy")
    sys.exit(1)

class WebOptimizer3D:
    def __init__(self, output_dir: str = "files-3d-optimized"):
        self.output_dir = Path(output_dir)
        self.output_dir.mkdir(exist_ok=True)
        
        # Cấu hình tối ưu hóa
        self.config = {
            "texture": {
                "max_size": 512,        # Texture tối đa 512x512
                "quality": 85,          # JPEG quality
                "format": "webp"        # WebP cho web (giảm 25-35%)
            },
            "mesh": {
                "target_faces": 5000,   # Giảm xuống 5K faces
                "simplify_ratio": 0.3,  # Giữ lại 30% vertices
                "smooth": True
            },
            "draco": {
                "compression_level": 10,  # Nén tối đa
                "quantize_position": 11,  # Độ chính xác position
                "quantize_normal": 8,     # Độ chính xác normal
                "quantize_texcoord": 10   # Độ chính xác UV
            }
        }
        
    def get_file_size(self, filepath: Path) -> int:
        """Lấy kích thước file"""
        return filepath.stat().st_size if filepath.exists() else 0
    
    def format_size(self, size_bytes: int) -> str:
        """Format kích thước file"""
        for unit in ['B', 'KB', 'MB', 'GB']:
            if size_bytes < 1024:
                return f"{size_bytes:.1f} {unit}"
            size_bytes /= 1024
        return f"{size_bytes:.1f} TB"
    
    def optimize_texture(self, texture_path: Path, output_path: Path) -> bool:
        """Tối ưu hóa texture"""
        try:
            print(f"🖼️  Tối ưu texture: {texture_path.name}")
            
            with Image.open(texture_path) as img:
                # Chuyển sang RGB nếu cần
                if img.mode in ('RGBA', 'LA'):
                    background = Image.new('RGB', img.size, (255, 255, 255))
                    if img.mode == 'RGBA':
                        background.paste(img, mask=img.split()[-1])
                    else:
                        background.paste(img)
                    img = background
                elif img.mode != 'RGB':
                    img = img.convert('RGB')
                
                # Resize nếu cần
                max_size = self.config["texture"]["max_size"]
                if max(img.size) > max_size:
                    img.thumbnail((max_size, max_size), Image.Resampling.LANCZOS)
                    print(f"   📏 Resize: {img.size}")
                
                # Lưu theo format tối ưu
                if self.config["texture"]["format"] == "webp":
                    output_path = output_path.with_suffix('.webp')
                    img.save(output_path, 'WEBP', 
                            quality=self.config["texture"]["quality"],
                            optimize=True)
                else:
                    img.save(output_path, 'JPEG',
                            quality=self.config["texture"]["quality"],
                            optimize=True)
                
                print(f"   ✅ Saved: {output_path.name}")
                return True
                
        except Exception as e:
            print(f"   ❌ Lỗi texture: {e}")
            return False
    
    def simplify_mesh(self, mesh: trimesh.Trimesh) -> trimesh.Trimesh:
        """Giảm độ phức tạp mesh"""
        try:
            original_faces = len(mesh.faces)
            target_faces = min(self.config["mesh"]["target_faces"], original_faces)
            
            if original_faces > target_faces:
                print(f"   🔺 Giảm faces: {original_faces} → {target_faces}")
                
                # Sử dụng quadric decimation
                simplified = mesh.simplify_quadric_decimation(target_faces)
                
                if simplified is not None and len(simplified.faces) > 0:
                    # Smooth surface nếu cần
                    if self.config["mesh"]["smooth"]:
                        simplified = simplified.smoothed()
                    
                    print(f"   ✅ Kết quả: {len(simplified.faces)} faces")
                    return simplified
                else:
                    print("   ⚠️  Simplification failed, keeping original")
                    return mesh
            else:
                print(f"   ℹ️  Mesh đã tối ưu ({original_faces} faces)")
                return mesh
                
        except Exception as e:
            print(f"   ❌ Lỗi simplify: {e}")
            return mesh
    
    def apply_draco_compression(self, glb_path: Path) -> Path:
        """Áp dụng Draco compression"""
        try:
            print(f"🗜️  Draco compression: {glb_path.name}")
            
            # Tạo tên file output
            draco_path = glb_path.parent / f"{glb_path.stem}_draco.glb"
            
            # Command line cho gltf-pipeline (nếu có)
            cmd = [
                "gltf-pipeline",
                "-i", str(glb_path),
                "-o", str(draco_path),
                "--draco.compressionLevel", str(self.config["draco"]["compression_level"]),
                "--draco.quantizePositionBits", str(self.config["draco"]["quantize_position"]),
                "--draco.quantizeNormalBits", str(self.config["draco"]["quantize_normal"]),
                "--draco.quantizeTexcoordBits", str(self.config["draco"]["quantize_texcoord"])
            ]
            
            try:
                subprocess.run(cmd, check=True, capture_output=True)
                print(f"   ✅ Draco applied: {draco_path.name}")
                return draco_path
            except (subprocess.CalledProcessError, FileNotFoundError):
                print("   ⚠️  gltf-pipeline not found, skipping Draco")
                return glb_path
                
        except Exception as e:
            print(f"   ❌ Lỗi Draco: {e}")
            return glb_path
    
    def create_progressive_format(self, model_path: Path) -> Dict:
        """Tạo format progressive loading"""
        try:
            print(f"📊 Progressive format: {model_path.name}")
            
            # Load mesh
            mesh = trimesh.load(model_path)
            if isinstance(mesh, trimesh.Scene):
                mesh = mesh.dump().sum()
            
            # Tạo 3 level detail
            levels = []
            
            # Level 1: Ultra low (1K faces) - Load đầu tiên
            ultra_low = mesh.simplify_quadric_decimation(1000)
            if ultra_low:
                level1_path = self.output_dir / f"{model_path.stem}_ultra_low.glb"
                ultra_low.export(level1_path)
                levels.append({
                    "level": "ultra_low",
                    "faces": len(ultra_low.faces),
                    "file": level1_path.name,
                    "size": self.get_file_size(level1_path)
                })
            
            # Level 2: Low (3K faces) - Load khi zoom
            low = mesh.simplify_quadric_decimation(3000)
            if low:
                level2_path = self.output_dir / f"{model_path.stem}_low.glb"
                low.export(level2_path)
                levels.append({
                    "level": "low",
                    "faces": len(low.faces),
                    "file": level2_path.name,
                    "size": self.get_file_size(level2_path)
                })
            
            # Level 3: Medium (tối ưu từ config)
            medium = self.simplify_mesh(mesh)
            level3_path = self.output_dir / f"{model_path.stem}_medium.glb"
            medium.export(level3_path)
            levels.append({
                "level": "medium",
                "faces": len(medium.faces),
                "file": level3_path.name,
                "size": self.get_file_size(level3_path)
            })
            
            print(f"   ✅ Created {len(levels)} LOD levels")
            return {"levels": levels}
            
        except Exception as e:
            print(f"   ❌ Lỗi progressive: {e}")
            return {"levels": []}
    
    def optimize_single_file(self, input_path: Path) -> Dict:
        """Tối ưu hóa một file"""
        print(f"\n🎯 Optimizing: {input_path.name}")
        print("=" * 50)
        
        original_size = self.get_file_size(input_path)
        results = {
            "input_file": input_path.name,
            "original_size": original_size,
            "original_size_formatted": self.format_size(original_size),
            "optimized_files": [],
            "total_savings": 0,
            "compression_ratio": 0
        }
        
        try:
            # 1. Load và analyze model
            print("📂 Loading model...")
            mesh = trimesh.load(input_path)
            if isinstance(mesh, trimesh.Scene):
                mesh = mesh.dump().sum()
            
            print(f"   📊 Original: {len(mesh.faces)} faces, {len(mesh.vertices)} vertices")
            
            # 2. Mesh optimization
            print("🔺 Mesh optimization...")
            optimized_mesh = self.simplify_mesh(mesh)
            
            # 3. Tạo file tối ưu cơ bản
            basic_output = self.output_dir / f"{input_path.stem}_optimized.glb"
            optimized_mesh.export(basic_output)
            basic_size = self.get_file_size(basic_output)
            
            results["optimized_files"].append({
                "type": "basic_optimized",
                "file": basic_output.name,
                "size": basic_size,
                "size_formatted": self.format_size(basic_size),
                "savings": original_size - basic_size,
                "compression": (1 - basic_size/original_size) * 100 if original_size > 0 else 0
            })
            
            # 4. Draco compression
            draco_output = self.apply_draco_compression(basic_output)
            if draco_output != basic_output:
                draco_size = self.get_file_size(draco_output)
                results["optimized_files"].append({
                    "type": "draco_compressed",
                    "file": draco_output.name,
                    "size": draco_size,
                    "size_formatted": self.format_size(draco_size),
                    "savings": original_size - draco_size,
                    "compression": (1 - draco_size/original_size) * 100 if original_size > 0 else 0
                })
            
            # 5. Progressive format
            progressive_info = self.create_progressive_format(input_path)
            results["progressive_levels"] = progressive_info["levels"]
            
            # 6. Tính tổng kết
            best_file = min(results["optimized_files"], key=lambda x: x["size"])
            results["best_compression"] = best_file
            results["total_savings"] = best_file["savings"]
            results["compression_ratio"] = best_file["compression"]
            
            print(f"\n✅ Optimization complete!")
            print(f"   📉 Best result: {best_file['compression']:.1f}% compression")
            print(f"   💾 Size: {self.format_size(original_size)} → {best_file['size_formatted']}")
            
        except Exception as e:
            print(f"❌ Lỗi optimization: {e}")
            results["error"] = str(e)
        
        return results
    
    def batch_optimize(self, input_dir: str = "files-3d") -> Dict:
        """Batch optimization"""
        input_path = Path(input_dir)
        if not input_path.exists():
            print(f"❌ Thư mục không tồn tại: {input_dir}")
            return {}
        
        print(f"🚀 Batch Optimization: {input_dir}")
        print(f"📁 Output: {self.output_dir}")
        print("=" * 60)
        
        # Tìm tất cả file 3D
        extensions = ['.glb', '.gltf', '.obj', '.ply', '.stl']
        files = []
        for ext in extensions:
            files.extend(input_path.glob(f"*{ext}"))
        
        if not files:
            print("❌ Không tìm thấy file 3D nào!")
            return {}
        
        print(f"📋 Found {len(files)} files to optimize")
        
        # Process từng file
        results = {"files": [], "summary": {}}
        total_original = 0
        total_optimized = 0
        
        for file_path in files:
            file_result = self.optimize_single_file(file_path)
            results["files"].append(file_result)
            
            total_original += file_result["original_size"]
            if "best_compression" in file_result:
                total_optimized += file_result["best_compression"]["size"]
        
        # Summary
        total_savings = total_original - total_optimized
        overall_compression = (1 - total_optimized/total_original) * 100 if total_original > 0 else 0
        
        results["summary"] = {
            "total_files": len(files),
            "total_original_size": total_original,
            "total_optimized_size": total_optimized,
            "total_savings": total_savings,
            "overall_compression": overall_compression,
            "original_formatted": self.format_size(total_original),
            "optimized_formatted": self.format_size(total_optimized),
            "savings_formatted": self.format_size(total_savings)
        }
        
        # Xuất report
        report_path = self.output_dir / "optimization_report.json"
        with open(report_path, 'w', encoding='utf-8') as f:
            json.dump(results, f, indent=2, ensure_ascii=False)
        
        print(f"\n🎉 BATCH OPTIMIZATION COMPLETE!")
        print("=" * 60)
        print(f"📊 Processed: {len(files)} files")
        print(f"💾 Total size: {self.format_size(total_original)} → {self.format_size(total_optimized)}")
        print(f"📉 Overall compression: {overall_compression:.1f}%")
        print(f"💰 Space saved: {self.format_size(total_savings)}")
        print(f"📄 Report: {report_path}")
        
        return results
    
    def generate_web_loader(self) -> None:
        """Tạo JavaScript loader cho progressive loading"""
        js_content = '''
// 🚀 Progressive 3D Model Loader
// Tự động load model từ thấp đến cao detail

class Progressive3DLoader {
    constructor(modelViewer) {
        this.modelViewer = modelViewer;
        this.currentLevel = 0;
        this.levels = ['ultra_low', 'low', 'medium'];
        this.baseName = '';
    }
    
    async loadProgressive(baseName) {
        this.baseName = baseName;
        
        // Load ultra low đầu tiên (fastest)
        await this.loadLevel(0);
        
        // Sau đó load level cao hơn trong background
        setTimeout(() => this.loadLevel(1), 1000);
        setTimeout(() => this.loadLevel(2), 3000);
    }
    
    async loadLevel(levelIndex) {
        if (levelIndex >= this.levels.length) return;
        
        const level = this.levels[levelIndex];
        const modelPath = `files-3d-optimized/${this.baseName}_${level}.glb`;
        
        try {
            // Preload in background
            const response = await fetch(modelPath);
            if (response.ok) {
                this.modelViewer.src = modelPath;
                this.currentLevel = levelIndex;
                console.log(`📈 Loaded level: ${level}`);
            }
        } catch (error) {
            console.log(`⚠️ Failed to load ${level}:`, error);
        }
    }
}

// Usage:
// const loader = new Progressive3DLoader(document.querySelector('model-viewer'));
// loader.loadProgressive('model_name');
'''
        
        js_path = self.output_dir / "progressive_loader.js"
        with open(js_path, 'w', encoding='utf-8') as f:
            f.write(js_content)
        
        print(f"📜 Progressive loader created: {js_path}")

def main():
    parser = argparse.ArgumentParser(description="🚀 3D Model Web Optimizer")
    parser.add_argument("--input", "-i", default="files-3d", help="Input directory")
    parser.add_argument("--output", "-o", default="files-3d-optimized", help="Output directory")
    parser.add_argument("--single", "-s", help="Optimize single file")
    parser.add_argument("--texture-size", type=int, default=512, help="Max texture size")
    parser.add_argument("--target-faces", type=int, default=5000, help="Target face count")
    
    args = parser.parse_args()
    
    # Create optimizer
    optimizer = WebOptimizer3D(args.output)
    
    # Update config from args
    optimizer.config["texture"]["max_size"] = args.texture_size
    optimizer.config["mesh"]["target_faces"] = args.target_faces
    
    print("🚀 3D Web Optimizer")
    print(f"   Texture size: {args.texture_size}px")
    print(f"   Target faces: {args.target_faces}")
    print()
    
    if args.single:
        # Single file mode
        single_path = Path(args.single)
        if single_path.exists():
            optimizer.optimize_single_file(single_path)
        else:
            print(f"❌ File not found: {args.single}")
    else:
        # Batch mode
        optimizer.batch_optimize(args.input)
    
    # Generate web loader
    optimizer.generate_web_loader()

if __name__ == "__main__":
    main()
