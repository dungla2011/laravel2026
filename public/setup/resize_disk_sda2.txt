#!/bin/bash

# Script resize phân vùng sda2 tối đa
# Cảnh báo: Cần backup dữ liệu trước khi chạy!

set -e

echo "=== Script Resize sda2 ==="
echo ""
echo "⚠️  CẢNH BÁO: Script này sẽ resize phân vùng sda2. Hãy backup dữ liệu trước!"
echo ""

# Kiểm tra quyền root
if [ "$EUID" -ne 0 ]; then
   echo "❌ Script phải chạy với quyền root (sudo)"
   exit 1
fi

# Hiển thị thông tin hiện tại
echo "📊 Thông tin hiện tại:"
fdisk -l /dev/sda | grep -E "sda|Disklabel"
echo ""

# Xác nhận
read -p "Bạn có chắc muốn tiếp tục? (yes/no): " confirm
if [ "$confirm" != "yes" ]; then
    echo "❌ Đã hủy"
    exit 1
fi

echo ""
echo "🔧 Bắt đầu resize..."

# Step 1: Fix GPT table (nếu có lỗi)
echo "1️⃣  Sửa GPT table..."
echo "Fix GPT table"
sgdisk -e /dev/sda 2>/dev/null || echo "   (GPT table đã sẵn sàng)"

# Step 2: Xóa phân vùng sda2 trong bảng phân vùng (không xóa dữ liệu)
echo "2️⃣  Chuẩn bị resize phân vùng..."
sgdisk -d 2 /dev/sda

# Step 3: Tạo lại phân vùng sda2 với kích thước tối đa
echo "3️⃣  Tạo lại phân vùng sda2 với kích thước tối đa..."
sgdisk -N 2 /dev/sda

# Step 4: Reload kernel partition table
echo "4️⃣  Reload kernel partition table..."
partprobe /dev/sda || echo "   (Có thể cần reboot)"

# Step 5: Resize filesystem
echo "5️⃣  Resize filesystem ext4..."
resize2fs /dev/sda2

echo ""
echo "✅ Resize thành công!"
echo ""
echo "📊 Thông tin mới:"
df -h | grep sda2
echo ""
echo "💡 Nếu kích thước vẫn chưa cập nhật, hãy reboot: sudo reboot"
