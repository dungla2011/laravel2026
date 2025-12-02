#!/usr/bin/env python3
"""
Script để convert file OBJ sang các định dạng web-friendly
Hỗ trợ: glTF, GLB, PLY
"""

import os
import sys
import trimesh
from pathlib import Path

def convert_obj_to_gltf(obj_file, output_file=None, format='glb'):
    """
    Convert OBJ file to glTF/GLB format with texture support
    
    Args:
        obj_file (str): Path to input OBJ file
        output_file (str): Path to output file (optional)
        format (str): Output format ('gltf' or 'glb')
    """
    try:
        print(f"Đang load file: {obj_file}")
        
        # Load mesh from OBJ file (will automatically load MTL and textures)
        mesh = trimesh.load(obj_file)
        
        # Kiểm tra xem có phải là Scene không (nhiều object)
        if isinstance(mesh, trimesh.Scene):
            print(f"File chứa {len(mesh.geometry)} objects")
            # Convert scene to single mesh while preserving materials
            combined_mesh = mesh.dump().sum()
            mesh = combined_mesh
        
        print(f"Mesh info:")
        print(f"  - Vertices: {len(mesh.vertices):,}")
        print(f"  - Faces: {len(mesh.faces):,}")
        print(f"  - Bounds: {mesh.bounds}")
        
        # Kiểm tra materials và textures
        if hasattr(mesh.visual, 'material'):
            if hasattr(mesh.visual.material, 'image'):
                print(f"  - Texture: Found ({mesh.visual.material.image.size if mesh.visual.material.image else 'None'})")
            else:
                print(f"  - Material: {type(mesh.visual.material).__name__}")
        
        # Tạo tên file output nếu không được cung cấp
        if output_file is None:
            base_name = Path(obj_file).stem
            extension = 'glb' if format.lower() == 'glb' else 'gltf'
            output_file = f"{base_name}_web.{extension}"
        
        print(f"Đang export sang {format.upper()}: {output_file}")
        
        # Export to glTF/GLB with texture embedding
        export_options = {}
        if format.lower() == 'glb':
            # GLB embeds everything in binary format
            export_options['file_type'] = 'glb'
        else:
            # glTF can embed or reference textures
            export_options['file_type'] = 'gltf'
            
        mesh.export(output_file, **export_options)
        
        # Kiểm tra kích thước file output
        output_size = os.path.getsize(output_file)
        input_size = os.path.getsize(obj_file)
        
        print(f"✅ Conversion thành công!")
        print(f"   Input size:  {input_size / (1024*1024):.1f} MB")
        print(f"   Output size: {output_size / (1024*1024):.1f} MB")
        print(f"   Ratio:       {(output_size / input_size):.2f}x")
        
        return output_file
        
    except Exception as e:
        print(f"❌ Lỗi khi convert: {str(e)}")
        return None

def convert_obj_to_ply(obj_file, output_file=None):
    """
    Convert OBJ file to PLY format (lighter format)
    """
    try:
        print(f"Đang load file: {obj_file}")
        
        mesh = trimesh.load(obj_file)
        
        if isinstance(mesh, trimesh.Scene):
            mesh = mesh.dump().sum()
        
        if output_file is None:
            base_name = Path(obj_file).stem
            output_file = f"{base_name}_web.ply"
        
        print(f"Đang export sang PLY: {output_file}")
        mesh.export(output_file)
        
        output_size = os.path.getsize(output_file)
        input_size = os.path.getsize(obj_file)
        
        print(f"✅ PLY conversion thành công!")
        print(f"   Output size: {output_size / (1024*1024):.1f} MB")
        print(f"   Reduction:   {((input_size - output_size) / input_size * 100):.1f}%")
        
        return output_file
        
    except Exception as e:
        print(f"❌ Lỗi khi convert sang PLY: {str(e)}")
        return None

def main():
    """Convert tất cả OBJ files có trong thư mục"""
    obj_files = ["1.obj", "2.obj", "3.obj"]
    
    print("🔄 Bắt đầu conversion process with textures...")
    print("=" * 60)
    
    results = []
    
    for obj_file in obj_files:
        if not os.path.exists(obj_file):
            print(f"⚠️  Không tìm thấy file: {obj_file}")
            continue
            
        print(f"\n🎯 Processing: {obj_file}")
        print("-" * 40)
        
        # Convert to GLB (recommended for web)
        print("\n1. Converting to GLB (Binary glTF)...")
        glb_file = convert_obj_to_gltf(obj_file, format='glb')
        
        # Convert to glTF (text format)
        print("\n2. Converting to glTF...")
        gltf_file = convert_obj_to_gltf(obj_file, format='gltf')
        
        # Convert to PLY (lightweight option)
        print("\n3. Converting to PLY...")
        ply_file = convert_obj_to_ply(obj_file)
        
        results.append({
            'obj': obj_file,
            'glb': glb_file,
            'gltf': gltf_file,
            'ply': ply_file
        })
        
        print(f"\n✅ Completed: {obj_file}")
    
    print("\n" + "=" * 60)
    print("📋 FINAL SUMMARY:")
    print("Các file đã được tạo:")
    
    for result in results:
        print(f"\n📁 Source: {result['obj']}")
        for file_type, file_path in [
            ('GLB', result['glb']),
            ('glTF', result['gltf']), 
            ('PLY', result['ply'])
        ]:
            if file_path and os.path.exists(file_path):
                size = os.path.getsize(file_path) / (1024*1024)
                print(f"  ✅ {file_path} ({size:.1f} MB)")
            else:
                print(f"  ❌ {file_type} conversion failed")
    
    print(f"\n📖 Cách sử dụng:")
    print("- GLB: Tốt nhất cho web (có texture embedded)")
    print("- glTF: Dạng text, texture riêng biệt")
    print("- PLY: Format nhẹ, không có texture")
    print("\n🎮 Upload các file GLB lên web server để test!")

def convert_single_obj(obj_file):
    """Convert một file OBJ cụ thể"""
    if not os.path.exists(obj_file):
        print(f"❌ Không tìm thấy file: {obj_file}")
        return
    
    print("🔄 Bắt đầu conversion process...")
    print("=" * 50)
    
    # Convert to GLB (recommended for web)
    print("\n1. Converting to GLB (Binary glTF)...")
    glb_file = convert_obj_to_gltf(obj_file, format='glb')
    
    # Convert to glTF (text format)
    print("\n2. Converting to glTF...")
    gltf_file = convert_obj_to_gltf(obj_file, format='gltf')
    
    # Convert to PLY (lightweight option)
    print("\n3. Converting to PLY...")
    ply_file = convert_obj_to_ply(obj_file)
    
    print("\n" + "=" * 50)
    print("📋 SUMMARY:")
    print("Các file đã được tạo:")
    
    for file_path, description in [
        (glb_file, "GLB - Binary glTF (khuyến nghị cho web)"),
        (gltf_file, "glTF - Text format"),
        (ply_file, "PLY - Lightweight format")
    ]:
        if file_path and os.path.exists(file_path):
            size = os.path.getsize(file_path) / (1024*1024)
            print(f"  ✅ {file_path} ({size:.1f} MB) - {description}")
        else:
            print(f"  ❌ Failed to create {description}")
    
    print("\n📖 Cách sử dụng:")
    print("- GLB: Tốt nhất cho Three.js, Babylon.js, A-Frame")
    print("- glTF: Tương tự GLB nhưng dạng text, dễ debug")
    print("- PLY: Format nhẹ, hỗ trợ bởi nhiều viewer")

if __name__ == "__main__":
    import sys
    
    if len(sys.argv) > 1:
        # Convert specific file
        obj_file = sys.argv[1]
        convert_single_obj(obj_file)
    else:
        # Convert all OBJ files
        main()
