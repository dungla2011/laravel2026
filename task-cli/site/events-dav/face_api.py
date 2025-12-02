import os
import io
import requests
import numpy as np
from flask import Flask, request, jsonify
from PIL import Image
import cv2
import insightface
from insightface.app import FaceAnalysis
from threading import Lock, Thread
from datetime import datetime
import json
import time

app = Flask(__name__)

# Debug function
def log_request_info(endpoint_name):
    """Log request information for debugging"""
    content_type = request.headers.get('Content-Type', '')
    print(f"🔍 [{endpoint_name}] Content-Type: {content_type}")

    if request.is_json:
        print(f"🔍 [{endpoint_name}] JSON data: {request.get_json()}")
    else:
        print(f"🔍 [{endpoint_name}] Form data: {request.form.to_dict()}")
        print(f"🔍 [{endpoint_name}] Request data: {request.data}")

# URL API để lấy danh sách face vector
FACE_LIST_URL = "https://events.dav.edu.vn/tool1/_site/event_mng/galaxy_face_detection/get-face-vector.php"

def get_face_list():
    """
    Lấy danh sách face vector từ server
    Returns: List[dict] - Danh sách face vector với format: {id, name, face, url_confirm}
    """
    try:
        print("🔄 Loading face list from server...")
        response = requests.get(FACE_LIST_URL, timeout=30)
        response.raise_for_status()

        data = response.json()

        if data.get('status') != 'success':
            print(f"❌ API returned error: {data}")
            return []

        face_list = []
        for item in data.get('data', []):
            try:
                # Chuyển đổi face string thành list floats
                face_str = item.get('face', '[]')
                if isinstance(face_str, str):
                    # Parse JSON string thành list
                    face_vector = json.loads(face_str)
                else:
                    # Trường hợp face đã là list
                    face_vector = face_str

                # Validate face vector
                if not isinstance(face_vector, list) or len(face_vector) != 512:
                    print(f"⚠️  Invalid face vector for ID {item.get('id')}: length={len(face_vector) if isinstance(face_vector, list) else 'not list'}")
                    continue

                # Tạo entry cho face_cache
                face_entry = {
                    'id': str(item.get('id', '')),  # Convert to string
                    'name': item.get('name', ''),
                    'face': face_vector,  # List of 512 floats
                    'user_event_id': item.get('user_event_id', '')
                }

                face_list.append(face_entry)

            except Exception as e:
                print(f"⚠️  Error processing face entry {item.get('id')}: {str(e)}")
                continue

        print(f"✅ Successfully loaded {len(face_list)} face vectors")
        return face_list

    except requests.exceptions.RequestException as e:
        print(f"❌ Network error loading face list: {str(e)}")
        return []
    except json.JSONDecodeError as e:
        print(f"❌ JSON decode error: {str(e)}")
        return []
    except Exception as e:
        print(f"❌ Unexpected error loading face list: {str(e)}")
        return []

def start_background_cache_updater():
    """
    Khởi động background thread để tự động cập nhật cache
    """
    global cache_update_thread, should_stop_thread

    if cache_update_thread is None or not cache_update_thread.is_alive():
        should_stop_thread = False
        cache_update_thread = Thread(target=background_cache_updater, daemon=True)
        cache_update_thread.start()
        print("🚀 Background cache updater started")
    else:
        print("⚠️  Background cache updater is already running")

def stop_background_cache_updater():
    """
    Dừng background thread
    """
    global should_stop_thread
    should_stop_thread = True
    print("🛑 Stopping background cache updater...")

def init_face_cache():
    """
    Khởi tạo face cache từ server
    """
    global face_cache
    with cache_lock:
        face_list = get_face_list()
        face_cache.clear()
        face_cache.extend(face_list)
        print(f"💾 Face cache initialized with {len(face_cache)} entries")

# Khởi tạo model InsightFace
face_analyzer = FaceAnalysis(
    name='buffalo_l',
    allowed_modules=['detection', 'recognition'],
    providers=['CPUExecutionProvider']
)
face_analyzer.prepare(ctx_id=0, det_thresh=0.4, det_size=(640, 640))

# Cache RAM cho các vector khuôn mặt
face_cache = []  # List[dict] mỗi dict: {id, name, face, url_confirm}
cache_lock = Lock()

# Background thread control
cache_update_thread = None
should_stop_thread = False

def background_cache_updater():
    """
    Background thread để tự động cập nhật face cache mỗi 10 giây
    """
    global should_stop_thread
    print("🔄 Starting background cache updater (every 10 seconds)...")

    while not should_stop_thread:
        try:
            time.sleep(10)  # Chờ 10 giây
            if should_stop_thread:
                break

            print("⏰ Auto-updating face cache...")
            face_list = get_face_list()

            if face_list:  # Chỉ update nếu có dữ liệu
                with cache_lock:
                    old_count = len(face_cache)
                    face_cache.clear()
                    face_cache.extend(face_list)
                    new_count = len(face_cache)

                    if new_count != old_count:
                        print(f"✅ Face cache updated: {old_count} → {new_count} entries")
                    else:
                        print(f"✅ Face cache refreshed: {new_count} entries")
            else:
                print("⚠️  Failed to get face list in background update")

        except Exception as e:
            print(f"❌ Error in background cache updater: {str(e)}")
            time.sleep(5)  # Chờ 5 giây trước khi thử lại nếu có lỗi

    print("🛑 Background cache updater stopped")

# Helper: Tạo vector từ ảnh PIL
def get_face_vector_from_pil(pil_img):
    try:
        img = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)
        faces = face_analyzer.get(img)
        if not faces:
            return None, 'No face detected'
        # Lấy khuôn mặt lớn nhất
        faces.sort(key=lambda f: (f.bbox[2] - f.bbox[0]) * (f.bbox[3] - f.bbox[1]), reverse=True)
        return faces[0].embedding.tolist(), None
    except Exception as e:
        return None, str(e)

# Helper: Nhận diện khuôn mặt từ vector
def find_best_match(query_embedding, cache, threshold=0.5):
    if not cache:
        return None, 'Cache is empty'
    query = np.array(query_embedding, dtype=np.float32)
    if query.shape[0] != 512:
        return None, 'Embedding shape invalid'
    best_match = None
    best_sim = -1.0
    norm_query = query / np.linalg.norm(query)
    for item in cache:
        try:
            emb = np.array(item['face'], dtype=np.float32)
            if emb.shape[0] != 512:
                continue
            norm_emb = emb / np.linalg.norm(emb)
            sim = np.dot(norm_query, norm_emb)
            if sim > best_sim and sim >= threshold:
                best_sim = sim
                best_match = item
        except Exception:
            continue
    return best_match, None if best_match else 'No match found'

@app.route('/get_face_vector', methods=['POST'])
def get_face_vector():
    log_request_info('get_face_vector')
    # Nhận dữ liệu từ JSON hoặc form-data
    data = request.get_json()
    if data is None:
        # Nếu không phải JSON, thử lấy từ form-data
        data = request.form.to_dict()

    image_list_info = data.get('image_list_info')
    if not image_list_info:
        return jsonify({'status': 'fail', 'data': [], 'error': 'Missing image_list_info'}), 400

    # Kiểm tra image_list_info phải là mảng
    if not isinstance(image_list_info, list):
        return jsonify({'status': 'fail', 'data': [], 'error': 'image_list_info must be an array'}), 400

    try:
        results = []

        for image_obj in image_list_info:
            try:
                # Kiểm tra object có đủ field cần thiết không
                if not isinstance(image_obj, dict) or 'cloud_id' not in image_obj or 'file_path' not in image_obj:
                    print(f"⚠️  Invalid object format: {image_obj}")
                    # Thêm object với lỗi vào kết quả
                    error_obj = image_obj.copy() if isinstance(image_obj, dict) else {}
                    error_obj['face_vector'] = 'error_invalid_format'
                    results.append(error_obj)
                    continue

                cloud_id = image_obj['cloud_id']
                file_path = image_obj['file_path']
                
                print(f"🔍 Processing cloud_id: {cloud_id}, file_path: {file_path}")

                # Tạo copy của object gốc
                result_obj = image_obj.copy()
                pil_img = None

                # Kiểm tra nếu file_path bắt đầu bằng URL (http hoặc https)
                if file_path.startswith(('http://', 'https://')):
                    print(f"📥 Downloading image from URL: {file_path}")
                    resp = requests.get(file_path, timeout=10)
                    resp.raise_for_status()
                    pil_img = Image.open(io.BytesIO(resp.content)).convert('RGB')

                # Kiểm tra nếu file_path bắt đầu bằng / (đường dẫn file vật lý)
                elif file_path.startswith('/'):
                    print(f"📁 Loading image from file path: {file_path}")
                    if os.path.exists(file_path):
                        pil_img = Image.open(file_path).convert('RGB')
                    else:
                        print(f"⚠️  File not found: {file_path}")
                        result_obj['face_vector'] = 'error_file_not_found'
                        results.append(result_obj)
                        continue

                else:
                    print(f"⚠️  Invalid file path format: {file_path}")
                    result_obj['face_vector'] = 'error_invalid_path_format'
                    results.append(result_obj)
                    continue

                # Xử lý ảnh nếu tải thành công
                if pil_img is not None:
                    vector, err = get_face_vector_from_pil(pil_img)
                    if vector is not None:
                        result_obj['face_vector'] = vector
                        print(f"✅ Face vector extracted for cloud_id: {cloud_id}")
                    else:
                        result_obj['face_vector'] = 'error_extracting_vector'
                        print(f"❌ Failed to extract face vector for cloud_id {cloud_id}: {err}")

                results.append(result_obj)

            except Exception as e:
                print(f"❌ Error processing cloud_id {image_obj.get('cloud_id', 'unknown')}: {str(e)}")
                # Thêm object với lỗi vào kết quả
                error_obj = image_obj.copy() if isinstance(image_obj, dict) else {}
                error_obj['face_vector'] = 'error_processing'
                results.append(error_obj)

        return jsonify({'status': 'success', 'data': results})

    except Exception as e:
        return jsonify({'status': 'fail', 'data': [], 'error': str(e)}), 500

@app.route('/detect_face', methods=['POST'])
def detect_face():
    log_request_info('detect_face')
    if 'file' not in request.files:
        return jsonify({'status': 'fail', 'data': None, 'error': 'No file uploaded'}), 400
    file = request.files['file']
    try:
        # Tạo thư mục tam_thoi nếu chưa có
        if not os.path.exists('tam_thoi'):
            os.makedirs('tam_thoi')
        # Lưu file với tên duy nhất
        filename = f"tam_thoi/upload_{datetime.now().strftime('%Y%m%d_%H%M%S_%f')}.jpg"
        file.save(filename)
        # Đọc lại file vừa lưu
        pil_img = Image.open(filename).convert('RGB')
        img_np = cv2.cvtColor(np.array(pil_img), cv2.COLOR_RGB2BGR)
        # Phát hiện khuôn mặt trong ảnh gốc
        faces = face_analyzer.get(img_np)
        if not faces:
            return jsonify({'status': 'fail', 'data': None, 'error': 'No face detected'}), 200
        # Lấy khuôn mặt lớn nhất
        faces.sort(key=lambda f: (f.bbox[2] - f.bbox[0]) * (f.bbox[3] - f.bbox[1]), reverse=True)
        face = faces[0]
        embedding = face.embedding
        if embedding.shape[0] != 512:
            return jsonify({'status': 'fail', 'data': None, 'error': 'Embedding shape invalid'}), 200
        # Nhận diện
        with cache_lock:
            match, err = find_best_match(embedding, face_cache)
        if match:
#             return jsonify({'status': 'success', 'data': {'id': match['id'], 'url_confirm': match['url_confirm']}})
            return jsonify({'status': 'success', 'data': {'id': match['id'], 'name': match['name'], 'user_event_id': match['user_event_id'] }})
        else:
            return jsonify({'status': 'fail', 'data': None, 'error': err}), 200
    except Exception as e:
        return jsonify({'status': 'fail', 'data': None, 'error': str(e)}), 500

@app.route('/update_face', methods=['POST'])
def update_face():
    log_request_info('update_face')
    # Nhận dữ liệu từ JSON hoặc form-data
    data = request.get_json()
    if data is None:
        # Nếu không phải JSON, thử lấy từ form-data
        data = request.form.to_dict()

    face_array = data.get('face_array')
    if not isinstance(face_array, list):
        return jsonify({'status': 'fail', 'error': 'face_array must be a list'}), 400
    try:
        # Kiểm tra từng phần tử hợp lệ
        for item in face_array:
            if not all(k in item for k in ('id', 'name', 'face', 'user_event_id')):
                return jsonify({'status': 'fail', 'error': 'Invalid item in face_array'}), 400
            # Đảm bảo face là list 512 chiều
            if not isinstance(item['face'], list) or len(item['face']) != 512:
                return jsonify({'status': 'fail', 'error': 'face must be a list of 512 floats'}), 400
        with cache_lock:
            face_cache.clear()
            face_cache.extend(face_array)
        return jsonify({'status': 'success'})
    except Exception as e:
        return jsonify({'status': 'fail', 'error': str(e)}), 500

@app.route('/reload_face_cache', methods=['POST'])
def reload_face_cache():
    """
    Reload face cache từ server
    """
    log_request_info('reload_face_cache')
    try:
        init_face_cache()
        return jsonify({'status': 'success', 'message': f'Face cache reloaded with {len(face_cache)} entries'})
    except Exception as e:
        return jsonify({'status': 'fail', 'error': str(e)}), 500

@app.route('/cache_status', methods=['GET'])
def cache_status():
    """
    Kiểm tra trạng thái face cache
    """
    try:
        with cache_lock:
            cache_info = {
                'total_entries': len(face_cache),
                'entries': [{'id': item['id'], 'name': item['name']} for item in face_cache],
                'auto_update_enabled': cache_update_thread is not None and cache_update_thread.is_alive(),
                'auto_update_interval': 10  # seconds
            }
        return jsonify({'status': 'success', 'data': cache_info})
    except Exception as e:
        return jsonify({'status': 'fail', 'error': str(e)}), 500

@app.route('/start_auto_update', methods=['POST'])
def start_auto_update():
    """
    Bắt đầu tự động cập nhật cache
    """
    try:
        start_background_cache_updater()
        return jsonify({'status': 'success', 'message': 'Auto update started'})
    except Exception as e:
        return jsonify({'status': 'fail', 'error': str(e)}), 500

@app.route('/stop_auto_update', methods=['POST'])
def stop_auto_update():
    """
    Dừng tự động cập nhật cache
    """
    try:
        stop_background_cache_updater()
        return jsonify({'status': 'success', 'message': 'Auto update stopped'})
    except Exception as e:
        return jsonify({'status': 'fail', 'error': str(e)}), 500

if __name__ == '__main__':
    # Khởi tạo face cache từ server
    init_face_cache()

    # Khởi động background cache updater tự động
    start_background_cache_updater()

    # Lấy port từ biến môi trường, mặc định là 8080
    port = int(os.getenv('FLASK_PORT', 50000))

    print(f"🚀 Starting Face API server on port {port}...")
    app.run(host='0.0.0.0', port=port, debug=True)
