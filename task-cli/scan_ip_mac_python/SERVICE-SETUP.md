# GLX IP Scanner - Service Configuration

## Cài đặt Service

### 1. Cập nhật Config File

Sửa file `config.json`:
```json
{
  "service_name": "glxScanIpMac",
  "networks": "10.0.0.0-ens192,10.0.1.0-ens192,10.0.21.0-ens192,103.163.216.0-ens160,103.163.217.0-ens160,103.74.121.0-ens224",
  "output_file": "ip_mac.json",
  "loop_interval": 10,
  "threads": 100,
  "verbose": false,
  "start_octet": 1,
  "end_octet": 254
}
```

**Các tham số:**
- `service_name`: Tên service
- `networks`: Danh sách dải IP và card mạng (định dạng: `IP-NIC,IP-NIC,...`)
- `output_file`: File output JSON
- `loop_interval`: Khoảng thời gian lặp lại (giây), 0 = chạy 1 lần
- `threads`: Số thread (50-150, khuyến nghị 100)
- `verbose`: In chi tiết (true/false)
- `start_octet`: Octet bắt đầu (1-254)
- `end_octet`: Octet kết thúc (1-254)

### 2. Cài đặt Service (Linux/macOS)

```bash
cd /path/to/scan_ip_mac_python
sudo bash install-service.sh
```

Script sẽ:
- Copy service file vào `/etc/systemd/system/`
- Cập nhật đường dẫn tự động
- Reload systemd daemon

### 3. Quản lý Service

**Khởi động:**
```bash
sudo systemctl start glxScanIpMac
```

**Dừng:**
```bash
sudo systemctl stop glxScanIpMac
```

**Xem trạng thái:**
```bash
sudo systemctl status glxScanIpMac
```

**Xem log (real-time):**
```bash
sudo journalctl -u glxScanIpMac -f
```

**Xem log cũ:**
```bash
sudo journalctl -u glxScanIpMac --no-pager
```

**Kích hoạt tự động khởi động:**
```bash
sudo systemctl enable glxScanIpMac
```

**Vô hiệu hóa tự động khởi động:**
```bash
sudo systemctl disable glxScanIpMac
```

### 4. Chạy Service Thủ Công (Testing)

```bash
# Chạy trực tiếp
python3 service.py

# Hoặc chạy scanner với tham số tùy chỉnh
python3 scan_ip_mac.py -m "10.0.0.0-ens192" -o test.json --loop=10 -t 100
```

### 5. Cấu Hình Advanced

**Sửa service file (nếu cần):**
```bash
sudo systemctl edit glxScanIpMac
```

**Restart service sau khi sửa config:**
```bash
sudo systemctl restart glxScanIpMac
```

### 6. Xem Output File

```bash
# Xem file JSON
cat ip_mac.json | jq .

# Hoặc pretty print
python3 -m json.tool ip_mac.json
```

## Ví dụ Sử Dụng

### Config 1: Quét liên tục mỗi 10 giây
```json
{
  "networks": "10.0.0.0-ens192,10.0.1.0-ens192",
  "output_file": "ip_mac.json",
  "loop_interval": 10,
  "threads": 100
}
```

### Config 2: Quét 1 lần rồi dừng
```json
{
  "networks": "10.0.0.0-ens192",
  "output_file": "ip_mac.json",
  "loop_interval": 0,
  "threads": 150
}
```

### Config 3: Quét từng phút
```json
{
  "networks": "10.0.0.0-ens192,10.0.1.0-ens192,10.0.21.0-ens192",
  "output_file": "ip_mac.json",
  "loop_interval": 60,
  "threads": 100,
  "verbose": true
}
```

## Troubleshooting

**Service không khởi động:**
```bash
sudo journalctl -u glxScanIpMac -n 50
```

**Config không load:**
- Kiểm tra file `config.json` có tồn tại không
- Kiểm tra JSON syntax: `python3 -m json.tool config.json`

**Service chiếm CPU cao:**
- Giảm `threads` trong config (ví dụ 50 thay vì 100)
- Tăng `loop_interval` để chạy ít thường xuyên hơn

**File JSON không được tạo:**
- Kiểm tra quyền ghi thư mục
- Kiểm tra log: `sudo journalctl -u glxScanIpMac -f`
