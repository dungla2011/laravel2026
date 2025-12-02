#!/usr/bin/env python3
# -*- coding: utf-8 -*-

import requests
import json
from PIL import Image
import io
import base64

# Cấu hình
API_BASE = "http://localhost:5000"

def test_get_face_vector():
    """Test API lấy vector từ image URL"""
    print("\n🧪 TEST: GET_FACE_VECTOR")
    
    url = f"{API_BASE}/get_face_vector"
    
    # Test với ảnh có khuôn mặt
    data = {
        "image_link": "https://events.dav.edu.vn/test_cloud_file?fid=3508"
    }
    
    try:
        response = requests.post(url, json=data)
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            vector = result.get('vector', [])
            print(f"✅ Success: Vector có {len(vector)} chiều")
            print(f"📊 Sample: {vector[:3]}...")
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def test_update_face():
    """Test API update face cache"""
    print("\n🧪 TEST: UPDATE_FACE")
    
    url = f"{API_BASE}/update_face"
    
    # Tạo data test với 2 người
    data = {
        "face_array": [
            {
                "id": "person1",
                "name": "Nguyen Van A",
                "face": [0.1] * 512,  # Vector 512 chiều
                "url_confirm": "https://example.com/person1"
            },
            {
                "id": "person2", 
                "name": "Tran Thi B",
                "face": [0.2] * 512,  # Vector 512 chiều khác
                "url_confirm": "https://example.com/person2"
            }
        ]
    }
    
    try:
        response = requests.post(url, json=data)
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            print("✅ Success: Face cache đã được cập nhật")
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def test_detect_face():
    """Test API detect face với file upload"""
    print("\n🧪 TEST: DETECT_FACE")
    
    url = f"{API_BASE}/detect_face"
    
    # Tạo ảnh test
    create_test_image("test_face.jpg")
    
    try:
        with open("test_face.jpg", 'rb') as f:
            files = {'file': ('test.jpg', f, 'image/jpeg')}
            response = requests.post(url, files=files)
        
        print(f"📄 Status: {response.status_code}")
        result = response.json()
        
        if result.get('status') == 'success':
            data = result.get('data', {})
            print(f"✅ Success: Nhận diện được người có ID: {data.get('id')}")
            print(f"🔗 Confirm URL: {data.get('url_confirm')}")
        else:
            print(f"❌ Failed: {result.get('error')}")
            
    except Exception as e:
        print(f"🔥 Error: {str(e)}")

def create_test_image(filename):
    """Tạo ảnh test đơn giản"""
    img = Image.new('RGB', (200, 200), color='lightblue')
    img.save(filename)
    print(f"📸 Created test image: {filename}")

def check_server():
    """Kiểm tra server có chạy không"""
    try:
        response = requests.get(f"{API_BASE}/", timeout=5)
        return True
    except:
        return False

def main():
    print("🚀 FACE API SIMPLE TEST")
    print("=" * 40)
    
    # Kiểm tra server
    if not check_server():
        print("❌ Server không chạy!")
        print("💡 Hãy chạy: python face_api.py")
        return
    
    print("✅ Server đang chạy")
    
    # Chạy test theo thứ tự
    print("\n📝 Chạy test theo thứ tự:")
    print("1. Update face cache")
    print("2. Test get face vector")
    print("3. Test detect face")
    
    # test_update_face()
    test_get_face_vector()
    # test_detect_face()
    
    print("\n🎉 Test hoàn tất!")

if __name__ == "__main__":
    main() 