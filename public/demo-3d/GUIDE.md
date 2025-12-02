# 🎮 3D Model Web Converter

Dự án này convert file 3D từ định dạng OBJ (Maya) sang các định dạng web-friendly để hiển thị trên trình duyệt web.

## 📁 Files trong dự án

### Input Files
- `1.obj` - File 3D gốc từ Maya (85.0 MB)
- `1.mtl` - Material file 
- `1.jpg` - Texture file (3.9 MB)

### Output Files (đã được tạo)
- `1_web.glb` - Binary glTF format (15.9 MB) ⭐ **Khuyến nghị**
- `1_web.gltf` - Text glTF format (1.4 KB)
- `1_web.ply` - PLY format (19.7 MB)

### Web Viewers
- `view_3d.html` - Three.js viewer với đầy đủ tính năng
- `model_viewer.html` - Model Viewer component (dễ sử dụng)

### Scripts
- `convert_to_web.py` - Script Python để convert file
- `GUIDE.md` - File hướng dẫn này

## 🚀 Cách sử dụng

### 1. Xem model trên web

#### Option 1: Three.js Viewer (Nâng cao)
```bash
# Mở file trong trình duyệt (cần web server)
# Hoặc sử dụng Live Server extension trong VS Code
start view_3d.html
```

#### Option 2: Model Viewer (Đơn giản)
```bash
start model_viewer.html
```

### 2. Khởi động local web server
Vì các file GLB cần được serve qua HTTP, không thể mở trực tiếp:

#### Sử dụng Python
```bash
python -m http.server 8000
# Sau đó mở: http://localhost:8000/view_3d.html
```

#### Sử dụng Node.js
```bash
npx serve .
# Sau đó mở URL được hiển thị
```

#### Sử dụng VS Code Live Server
1. Cài đặt extension "Live Server"
2. Right-click vào `view_3d.html` → "Open with Live Server"

## 🎯 Tính năng Web Viewer

### Three.js Viewer (`view_3d.html`)
- ✅ Xoay, zoom, pan model
- ✅ Wireframe mode
- ✅ Reset camera
- ✅ Pause/resume animation
- ✅ Responsive design
- ✅ Loading progress

### Model Viewer (`model_viewer.html`)
- ✅ Auto-rotate
- ✅ Environment lighting
- ✅ Shadow effects
- ✅ Touch controls (mobile)
- ✅ Download GLB file
- ✅ Fullscreen mode

## 📊 So sánh định dạng

| Format | Size | Web Support | Performance | Use Case |
|--------|------|-------------|-------------|----------|
| GLB | 15.9 MB | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | **Web chính** |
| PLY | 19.7 MB | ⭐⭐⭐ | ⭐⭐⭐⭐ | Scientific apps |
| glTF | 1.4 KB | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | Development/debug |

## 🔧 Conversion Script

### Chạy lại conversion
```bash
python convert_to_web.py
```

### Custom conversion
```python
from convert_to_web import convert_obj_to_gltf, convert_obj_to_ply

# Convert to GLB
convert_obj_to_gltf("1.obj", "custom_name.glb", format='glb')

# Convert to PLY
convert_obj_to_ply("1.obj", "custom_name.ply")
```

## 🌐 Web Framework Support

### Three.js
```javascript
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
const loader = new GLTFLoader();
loader.load('1_web.glb', (gltf) => {
    scene.add(gltf.scene);
});
```

### Babylon.js
```javascript
BABYLON.SceneLoader.ImportMesh("", "", "1_web.glb", scene);
```

### A-Frame
```html
<a-entity gltf-model="1_web.glb"></a-entity>
```

## 📱 Mobile Support

- ✅ Touch controls (pinch, drag, rotate)
- ✅ Responsive design
- ✅ Optimized performance
- ✅ Progressive loading

## 🎨 Customization

### Thay đổi materials
```javascript
model.traverse((child) => {
    if (child.isMesh) {
        child.material.color.setHex(0xff0000); // Đỏ
        child.material.roughness = 0.5;
        child.material.metalness = 0.8;
    }
});
```

### Thêm animations
```javascript
const mixer = new THREE.AnimationMixer(model);
const action = mixer.clipAction(gltf.animations[0]);
action.play();
```

## 🐛 Troubleshooting

### Model không hiển thị
1. Kiểm tra console cho errors
2. Đảm bảo file GLB tồn tại
3. Sử dụng web server (không mở file:// trực tiếp)

### Performance chậm
1. Giảm số lượng vertices trong Maya
2. Optimize textures
3. Sử dụng LOD (Level of Detail)

### CORS errors
```bash
# Khởi động server với CORS enabled
python -m http.server 8000 --bind 0.0.0.0
```

## 📈 Metrics

- **Original OBJ**: 85.0 MB
- **Optimized GLB**: 15.9 MB (81.3% reduction)
- **Vertices**: 417,388
- **Faces**: 693,353
- **Load time**: ~3-5 seconds (fast internet)

## 🔗 Resources

- [Three.js Documentation](https://threejs.org/docs/)
- [glTF 2.0 Specification](https://github.com/KhronosGroup/glTF)
- [Model Viewer](https://modelviewer.dev/)
- [Trimesh Python Library](https://trimsh.org/)

---

**Created**: July 30, 2025
**Author**: GitHub Copilot 🤖
