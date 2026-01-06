#!/bin/bash

# Script để chạy IP Scanner

# Kiểm tra xem venv có tồn tại không
if [ ! -d "venv" ]; then
    echo "Tạo virtual environment..."
    python3 -m venv venv
fi

# Kích hoạt venv
echo "Kích hoạt virtual environment..."
source venv/bin/activate

# Kiểm tra xem arping có được cài đặt không
if ! command -v arping &> /dev/null; then
    echo "⚠️  arping chưa được cài đặt!"
    echo "Cài đặt arping:"
    echo "  Ubuntu/Debian: sudo apt install iputils-arping"
    echo "  CentOS/RHEL: sudo yum install iputils"
    echo "  macOS: brew install arping"
    exit 1
fi

# Chạy chương trình
echo "Chạy IP Scanner..."
echo ""

# Lấy tham số từ dòng lệnh hoặc sử dụng mặc định
IP_CLASS="${1:-10.0.0.}"
INTERFACE="${2:-ens160}"
START="${3:-1}"
END="${4:-254}"
OUTPUT="${5:-}"

# Xây dựng lệnh
CMD="python scan_ip_mac.py -c $IP_CLASS -i $INTERFACE -s $START -e $END"

if [ ! -z "$OUTPUT" ]; then
    CMD="$CMD -o $OUTPUT"
fi

# Chạy với sudo
sudo $CMD

echo ""
echo "Hoàn thành!"
