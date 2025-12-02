#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🎯 Simple 3D Web Optimizer
Script đơn giản để giảm 80-90% dung lượng file 3D cho web
Chỉ cần trimesh và PIL
"""

import os
import sys
from pathlib import Path
import json

try:
    import trimesh
    import numpy as np
    from PIL import Image
except ImportError as e:
    print(f"❌ Thiếu thư viện: {e}")
    print("📦 Cài đặt: pip install trimesh pillow")
    sys.exit(1)

class Simple3DOptimizer:
    def __init__(self):
        self.output_dir = Path("files-3d-web")
        self.output_dir.mkdir(exist_ok=True)
        
    def format_size(self, size_bytes):
        """Format file size"""
        for unit in ['B', 'KB', 'MB']:
            if size_bytes < 1024:
                return f"{size_bytes:.1f} {unit}"
            size_bytes /= 1024
        return f"{size_bytes:.1f} GB"
    
    def optimize_texture(self, texture_path):
        """Tối ưu texture - giảm 60-80% kích thước"""
        print(f"  🖼️  Optimizing texture: {texture_path.name}")
        
        try:
            with Image.open(texture_path) as img:
                # Convert to RGB
                if img.mode != 'RGB':
                    if img.mode in ('RGBA', 'LA'):
                        # Tạo background trắng
                        background = Image.new('RGB', img.size, (255, 255, 255))
                        if img.mode == 'RGBA':
                            background.paste(img, mask=img.split()[-1])
                        else:
                            background.paste(img)
                        img = background
                    else:
                        img = img.convert('RGB')
                
                # Resize to 512x512 max (giảm đáng kể)
                if max(img.size) > 512:
                    img.thumbnail((512, 512), Image.Resampling.LANCZOS)
                    print(f"     📏 Resized to: {img.size}")
                
                # Save as WebP (giảm 25-40% so với JPEG)
                output_path = self.output_dir / f"{texture_path.stem}_opt.webp"
                img.save(output_path, 'WEBP', quality=75, optimize=True)
                
                original_size = texture_path.stat().st_size
                new_size = output_path.stat().st_size
                compression = (1 - new_size/original_size) * 100
                
                print(f"     ✅ {self.format_size(original_size)} → {self.format_size(new_size)} ({compression:.1f}% saved)")
                return output_path
                
        except Exception as e:
            print(f"     ❌ Error: {e}")
            return None
    
    def decimate_mesh(self, mesh, target_ratio=0.2):
        """Giảm 80% polygon - Kỹ thuật quan trọng nhất!"""
        original_faces = len(mesh.faces)
        target_faces = max(500, int(original_faces * target_ratio))  # Tối thiểu 500 faces
        
        print(f"  🔺 Decimating mesh: {original_faces} → {target_faces} faces")
        
        try:
            # Quadric decimation - thuật toán tốt nhất cho web
            simplified = mesh.simplify_quadric_decimation(target_faces)
            
            if simplified is not None and len(simplified.faces) > 0:
                actual_faces = len(simplified.faces)
                reduction = (1 - actual_faces/original_faces) * 100
                print(f"     ✅ Result: {actual_faces} faces ({reduction:.1f}% reduction)")
                return simplified
            else:
                print("     ⚠️  Decimation failed, trying simpler method...")
                # Fallback to vertex clustering
                simplified = mesh.simplify_vertex_clustering(radius=mesh.scale/100)
                return simplified if simplified is not None else mesh
                
        except Exception as e:
            print(f"     ❌ Decimation error: {e}")
            return mesh
    
    def remove_unused_data(self, mesh):
        """Xóa dữ liệu không cần thiết"""
        print("  🧹 Cleaning unused data...")
        
        try:
            # Remove degenerate faces
            mesh.remove_degenerate_faces()
            
            # Remove duplicate faces
            mesh.remove_duplicate_faces()
            
            # Remove unreferenced vertices
            mesh.remove_unreferenced_vertices()
            
            # Fix winding
            mesh.fix_normals()
            
            print("     ✅ Cleanup complete")
            return mesh
            
        except Exception as e:
            print(f"     ⚠️  Cleanup warning: {e}")
            return mesh
    
    def create_ultra_low_version(self, mesh):
        """Tạo version ultra-low cho loading nhanh"""
        print("  ⚡ Creating ultra-low version...")
        
        try:
            # Giảm xuống 200-500 faces cho loading instant
            target_faces = min(300, len(mesh.faces) // 10)
            ultra_low = mesh.simplify_quadric_decimation(target_faces)
            
            if ultra_low is not None and len(ultra_low.faces) > 0:
                print(f"     ✅ Ultra-low: {len(ultra_low.faces)} faces")
                return ultra_low
            else:
                # Fallback: vertex clustering với radius lớn
                ultra_low = mesh.simplify_vertex_clustering(radius=mesh.scale/20)
                return ultra_low if ultra_low is not None else mesh
                
        except Exception as e:
            print(f"     ❌ Ultra-low error: {e}")
            return mesh
    
    def optimize_model(self, input_path):
        """Main optimization function"""
        print(f"\n🎯 OPTIMIZING: {input_path.name}")
        print("=" * 50)
        
        original_size = input_path.stat().st_size
        print(f"📊 Original size: {self.format_size(original_size)}")
        
        try:
            # 1. Load model
            print("📂 Loading model...")
            mesh = trimesh.load(input_path)
            
            if isinstance(mesh, trimesh.Scene):
                # Convert scene to single mesh
                mesh = mesh.dump().sum()
            
            print(f"   📐 Original: {len(mesh.faces)} faces, {len(mesh.vertices)} vertices")
            
            # 2. Clean up
            mesh = self.remove_unused_data(mesh)
            
            # 3. Create multiple versions
            results = {}
            
            # Version 1: Standard optimized (20% of original polygons)
            print("\n🎯 Creating standard version...")
            standard = self.decimate_mesh(mesh.copy(), target_ratio=0.2)
            standard_path = self.output_dir / f"{input_path.stem}_standard.glb"
            standard.export(standard_path)
            standard_size = standard_path.stat().st_size
            
            results['standard'] = {
                'file': standard_path.name,
                'size': standard_size,
                'faces': len(standard.faces),
                'compression': (1 - standard_size/original_size) * 100
            }
            
            # Version 2: Aggressive (10% of original polygons)
            print("\n🔥 Creating aggressive version...")
            aggressive = self.decimate_mesh(mesh.copy(), target_ratio=0.1)
            aggressive_path = self.output_dir / f"{input_path.stem}_aggressive.glb"
            aggressive.export(aggressive_path)
            aggressive_size = aggressive_path.stat().st_size
            
            results['aggressive'] = {
                'file': aggressive_path.name,
                'size': aggressive_size,
                'faces': len(aggressive.faces),
                'compression': (1 - aggressive_size/original_size) * 100
            }
            
            # Version 3: Ultra-low for instant loading
            print("\n⚡ Creating ultra-low version...")
            ultra_low = self.create_ultra_low_version(mesh.copy())
            ultra_path = self.output_dir / f"{input_path.stem}_ultralow.glb"
            ultra_low.export(ultra_path)
            ultra_size = ultra_path.stat().st_size
            
            results['ultralow'] = {
                'file': ultra_path.name,
                'size': ultra_size,
                'faces': len(ultra_low.faces),
                'compression': (1 - ultra_size/original_size) * 100
            }
            
            # 4. Show results
            print(f"\n✅ OPTIMIZATION COMPLETE!")
            print("=" * 50)
            
            for version, data in results.items():
                print(f"📦 {version.upper()}:")
                print(f"   📄 File: {data['file']}")
                print(f"   📊 Size: {self.format_size(data['size'])} ({data['compression']:.1f}% smaller)")
                print(f"   🔺 Faces: {data['faces']}")
                print()
            
            # Find best compression
            best = max(results.values(), key=lambda x: x['compression'])
            print(f"🏆 BEST: {best['compression']:.1f}% compression!")
            
            return results
            
        except Exception as e:
            print(f"❌ OPTIMIZATION FAILED: {e}")
            return {}
    
    def batch_optimize(self, input_dir="files-3d"):
        """Optimize all models in directory"""
        input_path = Path(input_dir)
        
        if not input_path.exists():
            print(f"❌ Directory not found: {input_dir}")
            return
        
        # Find 3D files
        extensions = ['.glb', '.gltf', '.obj', '.ply']
        files = []
        for ext in extensions:
            files.extend(input_path.glob(f"*{ext}"))
        
        if not files:
            print(f"❌ No 3D files found in: {input_dir}")
            return
        
        print(f"🚀 BATCH OPTIMIZATION")
        print(f"📁 Input: {input_dir}")
        print(f"📁 Output: {self.output_dir}")
        print(f"📋 Files: {len(files)}")
        print("=" * 60)
        
        # Process each file
        all_results = {}
        total_original = 0
        total_optimized = 0
        
        for file_path in files:
            results = self.optimize_model(file_path)
            if results:
                all_results[file_path.name] = results
                
                total_original += file_path.stat().st_size
                # Use aggressive version for total calculation
                if 'aggressive' in results:
                    total_optimized += results['aggressive']['size']
        
        # Summary
        if total_original > 0:
            overall_compression = (1 - total_optimized/total_original) * 100
            print(f"\n🎉 BATCH COMPLETE!")
            print("=" * 60)
            print(f"📊 Total files: {len(files)}")
            print(f"💾 Total size: {self.format_size(total_original)} → {self.format_size(total_optimized)}")
            print(f"📉 Overall compression: {overall_compression:.1f}%")
            print(f"💰 Space saved: {self.format_size(total_original - total_optimized)}")
        
        # Save report
        report_path = self.output_dir / "optimization_report.json"
        with open(report_path, 'w', encoding='utf-8') as f:
            json.dump(all_results, f, indent=2, ensure_ascii=False)
        
        print(f"📄 Report saved: {report_path}")

def main():
    print("🎯 Simple 3D Web Optimizer")
    print("Giảm 80-90% dung lượng file 3D cho web")
    print()
    
    optimizer = Simple3DOptimizer()
    
    # Check if single file specified
    if len(sys.argv) > 1:
        file_path = Path(sys.argv[1])
        if file_path.exists():
            optimizer.optimize_model(file_path)
        else:
            print(f"❌ File not found: {sys.argv[1]}")
    else:
        # Batch mode
        optimizer.batch_optimize()

if __name__ == "__main__":
    main()
