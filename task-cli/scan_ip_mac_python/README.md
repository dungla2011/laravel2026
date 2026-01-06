# IP Scanner - Quét dải IP và lấy MAC Address

Chương trình CLI để quét dải địa chỉ IP trong mạng LAN và lấy MAC address tương ứng bằng lệnh `arping`.

## Yêu cầu

- Python 3.6+
- `arping` tool (cài đặt: `apt install iputils-arping` hoặc `brew install arping`)
- Quyền root hoặc sudo để chạy lệnh arping

## Cài đặt

### 1. Tạo virtual environment
```bash
cd e:\Projects\ScanIpMac
python -m venv venv

# Kích hoạt venv
# Trên Linux/Mac:
source venv/bin/activate
# Trên Windows:
venv\Scripts\activate
```

### 2. Cài đặt dependencies (nếu cần)
```bash
pip install -r requirements.txt
```

## Cách sử dụng

### Cú pháp cơ bản
```bash
python scan_ip_mac.py -c <IP_CLASS> -i <INTERFACE>
```

### Các tham số

- `-c, --ip-class` (bắt buộc): Dải IP (ví dụ: `10.0.0.`)
- `-i, --interface` (bắt buộc): Tên card mạng (ví dụ: `ens160`, `eth0`)
- `-s, --start`: IP bắt đầu (octet cuối, mặc định: 1)
- `-e, --end`: IP kết thúc (octet cuối, mặc định: 254)
- `-o, --output`: Đường dẫn file JSON output
- `-v, --verbose`: Hiển thị chi tiết từng IP

### Ví dụ sử dụng

#### Quét toàn bộ dải 10.0.0.0/24
```bash
sudo python scan_ip_mac.py -c 10.0.0. -i ens160
```

#### Quét dải 192.168.1.1 - 192.168.1.50 với output tùy chỉnh
```bash
sudo python scan_ip_mac.py -c 192.168.1. -i eth0 -s 1 -e 50 -o my_results.json
```

#### Quét với verbose mode (hiển thị chi tiết)
```bash
sudo python scan_ip_mac.py -c 10.0.0. -i ens160 -v
```

## Output JSON

Kết quả được lưu dưới dạng JSON:

```json
{
  "scan_info": {
    "ip_class": "10.0.0.",
    "network_interface": "ens160",
    "timestamp": "2024-01-15T10:30:45.123456",
    "total_found": 5,
    "total_failed": 249
  },
  "results": {
    "10.0.0.1": "00:11:22:33:44:55",
    "10.0.0.5": "aa:bb:cc:dd:ee:ff",
    "10.0.0.10": "11:22:33:44:55:66"
  },
  "failed_ips": [
    "10.0.0.2",
    "10.0.0.3",
    ...
  ]
}
```

## Ghi chú

- Cần chạy với quyền `sudo` hoặc `root` để sử dụng `arping`
- Thời gian quét phụ thuộc vào tốc độ mạng và số IP
- Mỗi IP được quét với timeout 3 giây
- Chương trình sẽ tự động thêm `.` vào cuối IP class nếu chưa có

## Troubleshooting

### Lỗi: "arping command not found"
Cài đặt arping:
- **Ubuntu/Debian**: `sudo apt install iputils-arping`
- **CentOS/RHEL**: `sudo yum install iputils`
- **macOS**: `brew install arping`

### Lỗi: "Permission denied"
Chạy với sudo:
```bash
sudo python scan_ip_mac.py -c 10.0.0. -i ens160
```

### Không tìm được IP
- Kiểm tra tên interface: `ip addr` hoặc `ifconfig`
- Kiểm tra dải IP có chính xác không
- Kiểm tra card mạng có hoạt động không

## Giấy phép

MIT License
