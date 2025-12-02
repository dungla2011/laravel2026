import requests
import json
import os
import time
from PIL import Image
import io
import base64

# Cấu hình API
BASE_URL = "http://localhost:5000"
HEADERS = {'Content-Type': 'application/json'}

class FaceAPITester:
    def __init__(self):
        self.base_url = BASE_URL
        self.session = requests.Session()
        
    def test_get_face_vector(self):
        """Test API /get_face_vector"""
        print("\n=== TEST GET_FACE_VECTOR ===")
        
        # Test case 1: Valid image URL
        print("1. Test với image URL hợp lệ:")
        test_data = {
            "image_link": "https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Vd-Orig.png/256px-Vd-Orig.png"
        }
        
        try:
            response = self.session.post(
                f"{self.base_url}/get_face_vector", 
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            result = response.json()
            print(f"Response: {result}")
            
            if result.get('status') == 'success':
                vector = result.get('vector', [])
                print(f"Vector length: {len(vector)}")
                print(f"Vector sample: {vector[:5]}...")
            
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 2: Missing image_link
        print("\n2. Test thiếu image_link:")
        try:
            response = self.session.post(
                f"{self.base_url}/get_face_vector", 
                json={},
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 3: Invalid image URL
        print("\n3. Test với URL không hợp lệ:")
        test_data = {
            "image_link": "https://invalid-url-12345.com/image.jpg"
        }
        try:
            response = self.session.post(
                f"{self.base_url}/get_face_vector", 
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
    
    def test_detect_face(self):
        """Test API /detect_face"""
        print("\n=== TEST DETECT_FACE ===")
        
        # Tạo ảnh test đơn giản
        test_image_path = "test_image.jpg"
        self.create_test_image(test_image_path)
        
        # Test case 1: Upload file hợp lệ
        print("1. Test upload file hợp lệ:")
        try:
            with open(test_image_path, 'rb') as f:
                files = {'file': ('test.jpg', f, 'image/jpeg')}
                response = self.session.post(
                    f"{self.base_url}/detect_face",
                    files=files
                )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 2: Không có file
        print("\n2. Test không có file:")
        try:
            response = self.session.post(f"{self.base_url}/detect_face")
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Cleanup
        if os.path.exists(test_image_path):
            os.remove(test_image_path)
    
    def test_update_face(self):
        """Test API /update_face"""
        print("\n=== TEST UPDATE_FACE ===")
        
        # Test case 1: Valid face_array
        print("1. Test với face_array hợp lệ:")
        test_data = {
            "face_array": [
                {
                    "id": "user1",
                    "name": "John Doe",
                    "face": [0.1] * 512,  # Vector 512 chiều
                    "url_confirm": "https://example.com/confirm1"
                },
                {
                    "id": "user2", 
                    "name": "Jane Smith",
                    "face": [0.2] * 512,  # Vector 512 chiều
                    "url_confirm": "https://example.com/confirm2"
                }
            ]
        }
        
        try:
            response = self.session.post(
                f"{self.base_url}/update_face",
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 2: Invalid face_array (thiếu trường)
        print("\n2. Test với face_array thiếu trường:")
        test_data = {
            "face_array": [
                {
                    "id": "user1",
                    "name": "John Doe",
                    # thiếu face và url_confirm
                }
            ]
        }
        try:
            response = self.session.post(
                f"{self.base_url}/update_face",
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 3: Invalid face vector length
        print("\n3. Test với face vector không đúng 512 chiều:")
        test_data = {
            "face_array": [
                {
                    "id": "user1",
                    "name": "John Doe",
                    "face": [0.1] * 100,  # Chỉ 100 chiều thay vì 512
                    "url_confirm": "https://example.com/confirm1"
                }
            ]
        }
        try:
            response = self.session.post(
                f"{self.base_url}/update_face",
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
        
        # Test case 4: face_array không phải list
        print("\n4. Test với face_array không phải list:")
        test_data = {
            "face_array": "invalid_data"
        }
        try:
            response = self.session.post(
                f"{self.base_url}/update_face",
                json=test_data,
                headers=HEADERS
            )
            print(f"Status Code: {response.status_code}")
            print(f"Response: {response.json()}")
        except Exception as e:
            print(f"Error: {str(e)}")
    
    def create_test_image(self, filename):
        """Tạo ảnh test đơn giản"""
        # Tạo ảnh RGB đơn giản
        img = Image.new('RGB', (100, 100), color='white')
        img.save(filename)
        print(f"Created test image: {filename}")
    
    def test_full_workflow(self):
        """Test quy trình đầy đủ"""
        print("\n=== TEST FULL WORKFLOW ===")
        
        # Bước 1: Update face cache
        print("1. Cập nhật face cache:")
        face_data = {
            "face_array": [
                {
                    "id": "demo_user",
                    "name": "Demo User",
                    "face": [0.5] * 512,
                    "url_confirm": "https://example.com/confirm_demo"
                }
            ]
        }
        
        try:
            response = self.session.post(
                f"{self.base_url}/update_face",
                json=face_data,
                headers=HEADERS
            )
            print(f"Update Status: {response.status_code}")
            print(f"Update Response: {response.json()}")
        except Exception as e:
            print(f"Update Error: {str(e)}")
        
        # Bước 2: Test detect face với cache có data
        print("\n2. Test detect face với cache có data:")
        test_image_path = "test_workflow.jpg"
        self.create_test_image(test_image_path)
        
        try:
            with open(test_image_path, 'rb') as f:
                files = {'file': ('test.jpg', f, 'image/jpeg')}
                response = self.session.post(
                    f"{self.base_url}/detect_face",
                    files=files
                )
            print(f"Detect Status: {response.status_code}")
            print(f"Detect Response: {response.json()}")
        except Exception as e:
            print(f"Detect Error: {str(e)}")
        
        # Cleanup
        if os.path.exists(test_image_path):
            os.remove(test_image_path)
    
    def run_all_tests(self):
        """Chạy tất cả test"""
        print("🚀 STARTING FACE API TESTS")
        print("=" * 50)
        
        # Kiểm tra server có chạy không
        try:
            response = self.session.get(f"{self.base_url}/")
            print("✅ Server is running")
        except:
            print("❌ Server is not running. Please start face_api.py first!")
            return
        
        # Chạy các test
        self.test_get_face_vector()
        self.test_detect_face()
        self.test_update_face()
        self.test_full_workflow()
        
        print("\n" + "=" * 50)
        print("🏁 ALL TESTS COMPLETED")

if __name__ == "__main__":
    tester = FaceAPITester()
    # tester.run_all_tests() 
    tester.test_get_face_vector()