# 3D Model Conversion for Web

## 📁 Files trong thư mục

### Input Files (Maya)
- `1.obj` - File 3D gốc từ Maya (85.0 MB)
- `1.mtl` - Material file 
- `1.jpg` - Texture file

### Output Files (Web-ready)
- `1_web.glb` - **Khuyến nghị** - Binary glTF format (15.9 MB, giảm 81.3%)
- `1_web.gltf` - Text glTF format (1.4 KB + external files)
- `1_web.ply` - PLY format (19.7 MB, giảm 76.8%)

### Viewer Files
- `view_3d.html` - Three.js viewer (tự build)
- `model_viewer.html` - Model Viewer component (dễ sử dụng)
- `convert_to_web.py` - Script conversion

## 🚀 Cách sử dụng

### 1. Xem trực tiếp trên browser
```bash
# Mở file HTML trong browser
start model_viewer.html
# hoặc
start view_3d.html
```

### 2. Tích hợp vào website

#### Sử dụng Model Viewer (Đơn giản nhất)
```html
<script type="module" src="https://ajax.googleapis.com/ajax/libs/model-viewer/3.3.0/model-viewer.min.js"></script>

<model-viewer 
    src="1_web.glb" 
    alt="3D Model"
    auto-rotate 
    camera-controls>
</model-viewer>
```

#### Sử dụng Three.js (Linh hoạt hơn)
```javascript
import * as THREE from 'three';
import { GLTFLoader } from 'three/examples/jsm/loaders/GLTFLoader.js';

const loader = new GLTFLoader();
loader.load('1_web.glb', function (gltf) {
    scene.add(gltf.scene);
});
```

#### Sử dụng Babylon.js
```javascript
BABYLON.SceneLoader.ImportMesh("", "", "1_web.glb", scene, function (meshes) {
    // Model loaded
});
```

#### Sử dụng A-Frame (WebXR/VR)
```html
<a-scene>
    <a-gltf-model src="1_web.glb" position="0 0 -5"></a-gltf-model>
</a-scene>
```

## 📊 So sánh Formats

| Format | Kích thước | Giảm % | Tương thích | Khuyến nghị |
|--------|------------|--------|-------------|-------------|
| **GLB** | 15.9 MB | 81.3% | ⭐⭐⭐⭐⭐ | **Tốt nhất cho web** |
| glTF | 1.4 KB | 100%* | ⭐⭐⭐⭐⭐ | Tốt cho debug |
| PLY | 19.7 MB | 76.8% | ⭐⭐⭐ | Cho scientific apps |

*glTF text format tách geometry ra external files

## 🎯 Tối ưu hóa thêm

### 1. Compression
```bash
# Sử dụng gltf-pipeline để compress thêm
npm install -g gltf-pipeline
gltf-pipeline -i 1_web.glb -o 1_compressed.glb --draco.compressionLevel=10
```

### 2. Texture optimization
```python
# Giảm kích thước texture nếu cần
from PIL import Image

img = Image.open('1.jpg')
img = img.resize((1024, 1024))  # Resize texture
img.save('1_optimized.jpg', quality=85)
```

### 3. LOD (Level of Detail)
```python
# Tạo nhiều mức detail khác nhau
import trimesh

mesh = trimesh.load('1.obj')

# LOD 0 - High detail (original)
mesh.export('1_lod0.glb')

# LOD 1 - Medium detail
simplified = mesh.simplify_quadric_decimation(face_count=int(len(mesh.faces) * 0.5))
simplified.export('1_lod1.glb')

# LOD 2 - Low detail
simplified2 = mesh.simplify_quadric_decimation(face_count=int(len(mesh.faces) * 0.1))
simplified2.export('1_lod2.glb')
```

## 🌐 Deploy lên Web

### 1. Local server
```bash
# Python
python -m http.server 8000

# Node.js
npx serve .

# Visual Studio Code Live Server extension
```

### 2. CDN hosting
- Upload file GLB lên: AWS S3, Google Cloud, Azure Blob
- Sử dụng CDN như CloudFlare để tăng tốc

### 3. Optimization tips
- Enable GZIP compression trên server
- Set proper MIME types: `model/gltf-binary` cho .glb
- Use HTTP/2 để tăng tốc tải
- Implement progressive loading cho model lớn

## 🔧 Troubleshooting

### Model không hiển thị
1. Kiểm tra console browser xem có lỗi CORS không
2. Đảm bảo file GLB được serve từ HTTP server (không phải file://)
3. Kiểm tra đường dẫn file có đúng không

### Performance issues
1. Giảm polygon count nếu model quá phức tạp
2. Optimize texture size
3. Sử dụng LOD system
4. Enable frustum culling

### Compatibility issues
1. GLB format được hỗ trợ rộng rãi nhất
2. Fallback về PLY hoặc OBJ nếu cần
3. Check browser support cho WebGL

## 📱 Mobile Optimization

```javascript
// Detect mobile và adjust quality
const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

if (isMobile) {
    // Load lower quality model
    modelSrc = '1_lod2.glb';
    renderer.setPixelRatio(1); // Lower pixel ratio
} else {
    modelSrc = '1_web.glb';
    renderer.setPixelRatio(window.devicePixelRatio);
}
```

## 🎮 Tích hợp Gaming Engines

### Unity WebGL
```csharp
// Import GLB vào Unity và export WebGL build
```

### Unreal Engine WebGL
```cpp
// Import GLB và package cho web platform
```

---

**🏆 Kết luận:** File `1_web.glb` (15.9 MB) là lựa chọn tốt nhất cho web, giảm 81.3% kích thước so với file gốc và tương thích với hầu hết các thư viện 3D web hiện đại.
