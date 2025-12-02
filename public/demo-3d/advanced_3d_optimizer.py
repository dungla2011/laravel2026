#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
🚀 Advanced 3D Web Optimizer
Giảm 80-90% dung lượng với các kỹ thuật tối ưu
"""

import os
import sys
import json
from pathlib import Path
import struct
import gzip
import shutil

try:
    import trimesh
    import numpy as np
    from PIL import Image
except ImportError as e:
    print(f"❌ Thiếu thư viện: {e}")
    print("📦 Cài đặt: pip install trimesh pillow numpy")
    sys.exit(1)

class AdvancedWebOptimizer:
    def __init__(self):
        self.output_dir = Path("files-3d-optimized")
        self.output_dir.mkdir(exist_ok=True)
        
        # Statistics
        self.stats = {
            'total_original': 0,
            'total_optimized': 0,
            'files_processed': 0
        }
    
    def format_size(self, size_bytes):
        """Format file size"""
        for unit in ['B', 'KB', 'MB', 'GB']:
            if size_bytes < 1024:
                return f"{size_bytes:.1f} {unit}"
            size_bytes /= 1024
        return f"{size_bytes:.1f} TB"
    
    def optimize_texture_advanced(self, texture_path):
        """Tối ưu texture với nhiều kỹ thuật"""
        print(f"  🖼️  Advanced texture optimization: {texture_path.name}")
        
        try:
            with Image.open(texture_path) as img:
                original_size = texture_path.stat().st_size
                
                # Convert to RGB
                if img.mode != 'RGB':
                    if img.mode in ('RGBA', 'LA'):
                        background = Image.new('RGB', img.size, (255, 255, 255))
                        if img.mode == 'RGBA':
                            background.paste(img, mask=img.split()[-1])
                        else:
                            background.paste(img)
                        img = background
                    else:
                        img = img.convert('RGB')
                
                # Aggressive resize - Kỹ thuật 1: Size reduction
                target_sizes = [256, 512, 1024]  # Test multiple sizes
                best_quality_ratio = 0
                best_output = None
                
                for target_size in target_sizes:
                    if max(img.size) <= target_size:
                        continue
                        
                    # Create resized version
                    test_img = img.copy()
                    test_img.thumbnail((target_size, target_size), Image.Resampling.LANCZOS)
                    
                    # Test multiple formats and qualities
                    for format_info in [
                        ('webp', 60), ('webp', 75), ('webp', 85),
                        ('jpeg', 70), ('jpeg', 85)
                    ]:
                        format_type, quality = format_info
                        test_path = self.output_dir / f"test_{target_size}_{format_type}_{quality}.tmp"
                        
                        if format_type == 'webp':
                            test_img.save(test_path, 'WEBP', quality=quality, optimize=True)
                        else:
                            test_img.save(test_path, 'JPEG', quality=quality, optimize=True)
                        
                        test_size = test_path.stat().st_size
                        compression_ratio = (1 - test_size/original_size) * 100
                        
                        # Quality score (size reduction vs resolution loss)
                        resolution_penalty = (1 - (target_size * target_size) / (img.width * img.height)) * 0.3
                        quality_ratio = compression_ratio - resolution_penalty
                        
                        if quality_ratio > best_quality_ratio:
                            best_quality_ratio = quality_ratio
                            if best_output:
                                best_output.unlink(missing_ok=True)
                            best_output = test_path
                        else:
                            test_path.unlink(missing_ok=True)
                
                if best_output:
                    final_path = self.output_dir / f"{texture_path.stem}_opt{best_output.suffix}"
                    shutil.move(best_output, final_path)
                    
                    final_size = final_path.stat().st_size
                    compression = (1 - final_size/original_size) * 100
                    
                    print(f"     ✅ {self.format_size(original_size)} → {self.format_size(final_size)} ({compression:.1f}% saved)")
                    return final_path
                
        except Exception as e:
            print(f"     ❌ Error: {e}")
        
        return None
    
    def decimate_mesh_advanced(self, mesh, target_reduction=0.8):
        """Advanced mesh decimation với nhiều thuật toán"""
        print(f"  🔺 Advanced mesh decimation...")
        
        original_faces = len(mesh.faces)
        target_faces = max(100, int(original_faces * (1 - target_reduction)))
        
        print(f"     📊 Target: {original_faces} → {target_faces} faces ({target_reduction*100:.0f}% reduction)")
        
        # Kỹ thuật 1: Vertex clustering (Fast, good for high-poly models)
        try:
            print("     🔄 Trying vertex clustering...")
            # Calculate appropriate cluster radius
            bbox_size = mesh.bounding_box.extents.max()
            cluster_radius = bbox_size / (target_faces ** 0.5 * 0.1)
            
            clustered = mesh.simplify_vertex_clustering(radius=cluster_radius)
            if clustered and len(clustered.faces) > 0:
                cluster_faces = len(clustered.faces)
                cluster_reduction = (1 - cluster_faces/original_faces) * 100
                print(f"     ✅ Clustering result: {cluster_faces} faces ({cluster_reduction:.1f}% reduction)")
                
                if cluster_faces <= target_faces * 1.5:  # Close enough
                    return clustered
        except Exception as e:
            print(f"     ⚠️  Clustering failed: {e}")
        
        # Kỹ thuật 2: Voxel-based simplification
        try:
            print("     🔄 Trying voxel simplification...")
            # Calculate voxel size based on target faces
            bbox_volume = np.prod(mesh.bounding_box.extents)
            target_voxel_count = target_faces * 2  # Rough estimate
            voxel_size = (bbox_volume / target_voxel_count) ** (1/3)
            
            voxelized = mesh.voxelized(pitch=voxel_size)
            if voxelized:
                voxel_mesh = voxelized.marching_cubes
                if voxel_mesh and len(voxel_mesh.faces) > 0:
                    voxel_faces = len(voxel_mesh.faces)
                    voxel_reduction = (1 - voxel_faces/original_faces) * 100
                    print(f"     ✅ Voxel result: {voxel_faces} faces ({voxel_reduction:.1f}% reduction)")
                    
                    if voxel_faces <= target_faces * 1.5:
                        return voxel_mesh
        except Exception as e:
            print(f"     ⚠️  Voxel failed: {e}")
        
        # Kỹ thuật 3: Manual edge collapse (Custom implementation)
        try:
            print("     🔄 Trying edge collapse...")
            simplified = self.edge_collapse_simplify(mesh, target_faces)
            if simplified and len(simplified.faces) > 0:
                edge_faces = len(simplified.faces)
                edge_reduction = (1 - edge_faces/original_faces) * 100
                print(f"     ✅ Edge collapse result: {edge_faces} faces ({edge_reduction:.1f}% reduction)")
                return simplified
        except Exception as e:
            print(f"     ⚠️  Edge collapse failed: {e}")
        
        # Fallback: Return original with cleanup
        print("     ⚠️  Using original mesh with cleanup only")
        return self.cleanup_mesh(mesh)
    
    def edge_collapse_simplify(self, mesh, target_faces):
        """Custom edge collapse implementation"""
        if len(mesh.faces) <= target_faces:
            return mesh
        
        # Simple edge collapse based on shortest edges
        vertices = mesh.vertices.copy()
        faces = mesh.faces.copy()
        
        # Calculate edge lengths
        edges = mesh.edges_unique
        edge_lengths = mesh.edges_unique_length
        
        # Sort edges by length (collapse shortest first)
        sorted_indices = np.argsort(edge_lengths)
        
        faces_to_remove = len(faces) - target_faces
        removed_count = 0
        
        for i in range(len(sorted_indices)):
            if removed_count >= faces_to_remove:
                break
            
            edge_idx = sorted_indices[i]
            edge = edges[edge_idx]
            v1, v2 = edge
            
            # Find faces containing this edge
            face_mask1 = np.any(faces == v1, axis=1)
            face_mask2 = np.any(faces == v2, axis=1)
            shared_faces = np.where(face_mask1 & face_mask2)[0]
            
            if len(shared_faces) > 0:
                # Collapse v2 to v1 (merge vertices)
                vertices[v2] = (vertices[v1] + vertices[v2]) / 2
                faces[faces == v2] = v1
                
                # Remove degenerate faces (faces with repeated vertices)
                valid_faces = []
                for face in faces:
                    if len(np.unique(face)) == 3:  # Triangle with 3 unique vertices
                        valid_faces.append(face)
                
                faces = np.array(valid_faces)
                removed_count = len(mesh.faces) - len(faces)
        
        if len(faces) > 0:
            try:
                return trimesh.Trimesh(vertices=vertices, faces=faces, validate=False)
            except:
                pass
        
        return mesh
    
    def cleanup_mesh(self, mesh):
        """Clean up mesh without changing topology much"""
        try:
            # Remove degenerate faces
            valid_faces = []
            for face in mesh.faces:
                if len(np.unique(face)) == 3:
                    valid_faces.append(face)
            
            if len(valid_faces) < len(mesh.faces):
                mesh = trimesh.Trimesh(vertices=mesh.vertices, faces=valid_faces, validate=False)
            
            # Fix normals
            mesh.fix_normals()
            
            # Remove unreferenced vertices
            mesh.remove_unreferenced_vertices()
            
            return mesh
        except:
            return mesh
    
    def compress_binary_data(self, data):
        """Compress binary data using gzip"""
        try:
            original_size = len(data)
            compressed = gzip.compress(data)
            compression_ratio = (1 - len(compressed)/original_size) * 100
            print(f"     📦 Binary compression: {compression_ratio:.1f}% saved")
            return compressed
        except:
            return data
    
    def create_level_of_detail(self, mesh, levels=[0.1, 0.3, 0.6]):
        """Create multiple LOD levels"""
        print(f"  📊 Creating {len(levels)} LOD levels...")
        
        lod_meshes = []
        original_faces = len(mesh.faces)
        
        for i, reduction in enumerate(levels):
            print(f"     🎯 LOD {i+1}: {reduction*100:.0f}% reduction")
            
            try:
                if reduction >= 0.9:  # Ultra aggressive
                    lod_mesh = self.decimate_mesh_advanced(mesh.copy(), reduction)
                elif reduction >= 0.7:  # Aggressive
                    lod_mesh = self.decimate_mesh_advanced(mesh.copy(), reduction)
                else:  # Conservative
                    lod_mesh = self.cleanup_mesh(mesh.copy())
                
                if lod_mesh and len(lod_mesh.faces) > 0:
                    lod_faces = len(lod_mesh.faces)
                    actual_reduction = (1 - lod_faces/original_faces) * 100
                    print(f"        ✅ Result: {lod_faces} faces ({actual_reduction:.1f}% reduction)")
                    lod_meshes.append(lod_mesh)
                else:
                    lod_meshes.append(mesh)
                    
            except Exception as e:
                print(f"        ❌ LOD {i+1} failed: {e}")
                lod_meshes.append(mesh)
        
        return lod_meshes
    
    def optimize_model_advanced(self, input_path):
        """Advanced model optimization"""
        print(f"\n🚀 ADVANCED OPTIMIZATION: {input_path.name}")
        print("=" * 60)
        
        original_size = input_path.stat().st_size
        print(f"📊 Original size: {self.format_size(original_size)}")
        
        self.stats['total_original'] += original_size
        self.stats['files_processed'] += 1
        
        try:
            # Load model
            print("📂 Loading model...")
            mesh = trimesh.load(input_path)
            
            if isinstance(mesh, trimesh.Scene):
                # Handle scene
                mesh = mesh.dump().sum()
            
            if isinstance(mesh, list):
                # Handle multiple meshes
                if len(mesh) > 0:
                    mesh = trimesh.util.concatenate(mesh)
                else:
                    print("❌ No valid mesh found")
                    return {}
            
            print(f"   📐 Original: {len(mesh.faces)} faces, {len(mesh.vertices)} vertices")
            
            # Create multiple optimized versions
            results = {}
            
            # Version 1: Conservative (30% reduction)
            print(f"\n🎯 Conservative optimization (30% reduction)...")
            conservative = self.decimate_mesh_advanced(mesh.copy(), target_reduction=0.3)
            conservative_path = self.output_dir / f"{input_path.stem}_conservative.glb"
            conservative.export(conservative_path)
            conservative_size = conservative_path.stat().st_size
            
            results['conservative'] = {
                'file': conservative_path.name,
                'size': conservative_size,
                'faces': len(conservative.faces),
                'compression': (1 - conservative_size/original_size) * 100
            }
            
            # Version 2: Aggressive (70% reduction)
            print(f"\n🔥 Aggressive optimization (70% reduction)...")
            aggressive = self.decimate_mesh_advanced(mesh.copy(), target_reduction=0.7)
            aggressive_path = self.output_dir / f"{input_path.stem}_aggressive.glb"
            aggressive.export(aggressive_path)
            aggressive_size = aggressive_path.stat().st_size
            
            results['aggressive'] = {
                'file': aggressive_path.name,
                'size': aggressive_size,
                'faces': len(aggressive.faces),
                'compression': (1 - aggressive_size/original_size) * 100
            }
            
            # Version 3: Ultra (90% reduction)
            print(f"\n⚡ Ultra optimization (90% reduction)...")
            ultra = self.decimate_mesh_advanced(mesh.copy(), target_reduction=0.9)
            ultra_path = self.output_dir / f"{input_path.stem}_ultra.glb"
            ultra.export(ultra_path)
            ultra_size = ultra_path.stat().st_size
            
            results['ultra'] = {
                'file': ultra_path.name,
                'size': ultra_size,
                'faces': len(ultra.faces),
                'compression': (1 - ultra_size/original_size) * 100
            }
            
            # Progressive loading versions
            print(f"\n📊 Creating progressive loading versions...")
            lod_meshes = self.create_level_of_detail(mesh, [0.9, 0.7, 0.5])
            
            for i, lod_mesh in enumerate(lod_meshes):
                lod_name = ['instant', 'fast', 'detailed'][i]
                lod_path = self.output_dir / f"{input_path.stem}_{lod_name}.glb"
                lod_mesh.export(lod_path)
                lod_size = lod_path.stat().st_size
                
                results[f'lod_{lod_name}'] = {
                    'file': lod_path.name,
                    'size': lod_size,
                    'faces': len(lod_mesh.faces),
                    'compression': (1 - lod_size/original_size) * 100
                }
            
            # Show results
            print(f"\n✅ OPTIMIZATION COMPLETE!")
            print("=" * 60)
            
            best_compression = 0
            best_version = None
            
            for version, data in results.items():
                print(f"📦 {version.upper()}:")
                print(f"   📄 File: {data['file']}")
                print(f"   📊 Size: {self.format_size(data['size'])} ({data['compression']:.1f}% smaller)")
                print(f"   🔺 Faces: {data['faces']}")
                print()
                
                if data['compression'] > best_compression:
                    best_compression = data['compression']
                    best_version = version
            
            print(f"🏆 BEST: {best_version} with {best_compression:.1f}% compression!")
            
            # Update stats
            best_size = results[best_version]['size']
            self.stats['total_optimized'] += best_size
            
            return results
            
        except Exception as e:
            print(f"❌ OPTIMIZATION FAILED: {e}")
            import traceback
            traceback.print_exc()
            return {}
    
    def batch_optimize_advanced(self, input_dir="files-3d"):
        """Advanced batch optimization"""
        input_path = Path(input_dir)
        
        if not input_path.exists():
            print(f"❌ Directory not found: {input_dir}")
            return
        
        # Find 3D files
        extensions = ['.glb', '.obj', '.ply']  # Focus on main formats
        files = []
        for ext in extensions:
            files.extend(input_path.glob(f"*{ext}"))
        
        # Filter out already optimized files
        files = [f for f in files if not any(
            suffix in f.stem for suffix in ['_conservative', '_aggressive', '_ultra', '_instant', '_fast', '_detailed']
        )]
        
        if not files:
            print(f"❌ No 3D files found in: {input_dir}")
            return
        
        print(f"🚀 ADVANCED BATCH OPTIMIZATION")
        print(f"📁 Input: {input_dir}")
        print(f"📁 Output: {self.output_dir}")
        print(f"📋 Files: {len(files)}")
        print("=" * 80)
        
        # Process each file
        all_results = {}
        
        for file_path in files:
            results = self.optimize_model_advanced(file_path)
            if results:
                all_results[file_path.name] = results
        
        # Final summary
        if self.stats['total_original'] > 0:
            overall_compression = (1 - self.stats['total_optimized']/self.stats['total_original']) * 100
            
            print(f"\n🎉 ADVANCED BATCH COMPLETE!")
            print("=" * 80)
            print(f"📊 Processed files: {self.stats['files_processed']}")
            print(f"💾 Total original: {self.format_size(self.stats['total_original'])}")
            print(f"💾 Total optimized: {self.format_size(self.stats['total_optimized'])}")
            print(f"📉 Overall compression: {overall_compression:.1f}%")
            print(f"💰 Total space saved: {self.format_size(self.stats['total_original'] - self.stats['total_optimized'])}")
        
        # Save detailed report
        report_path = self.output_dir / "advanced_optimization_report.json"
        with open(report_path, 'w', encoding='utf-8') as f:
            json.dump({
                'results': all_results,
                'summary': self.stats,
                'overall_compression': overall_compression if self.stats['total_original'] > 0 else 0
            }, f, indent=2, ensure_ascii=False)
        
        print(f"📄 Detailed report: {report_path}")

def main():
    print("🚀 Advanced 3D Web Optimizer")
    print("Giảm 80-90% dung lượng với kỹ thuật tiên tiến")
    print()
    
    optimizer = AdvancedWebOptimizer()
    
    if len(sys.argv) > 1:
        # Single file mode
        file_path = Path(sys.argv[1])
        if file_path.exists():
            optimizer.optimize_model_advanced(file_path)
        else:
            print(f"❌ File not found: {sys.argv[1]}")
    else:
        # Batch mode
        optimizer.batch_optimize_advanced()

if __name__ == "__main__":
    main()
