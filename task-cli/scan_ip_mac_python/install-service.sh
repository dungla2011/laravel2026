#!/bin/bash
# Script cài đặt systemd service cho glxScanIpMac

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SERVICE_FILE="/etc/systemd/system/glxScanIpMac.service"

echo "=========================================="
echo "GLX IP Scanner Service Installer"
echo "=========================================="
echo ""

# Kiểm tra quyền root
if [[ $EUID -ne 0 ]]; then
   echo "Error: This script must be run as root"
   exit 1
fi

# Cập nhật đường dẫn trong service file
echo "Cập nhật service file..."
sed -i "s|/path/to/scan_ip_mac_python|$SCRIPT_DIR|g" "$SCRIPT_DIR/glxScanIpMac.service"

# Copy service file
echo "Cài đặt service file..."
cp "$SCRIPT_DIR/glxScanIpMac.service" "$SERVICE_FILE"
chmod 644 "$SERVICE_FILE"

# Reload systemd daemon
echo "Reload systemd daemon..."
systemctl daemon-reload

echo ""
echo "=========================================="
echo "✓ Service installed successfully!"
echo "=========================================="
echo ""
echo "Cách sử dụng:"
echo "  Khởi động service:"
echo "    sudo systemctl start glxScanIpMac"
echo ""
echo "  Dừng service:"
echo "    sudo systemctl stop glxScanIpMac"
echo ""
echo "  Xem trạng thái:"
echo "    sudo systemctl status glxScanIpMac"
echo ""
echo "  Xem log:"
echo "    sudo journalctl -u glxScanIpMac -f"
echo ""
echo "  Kích hoạt tự động khởi động khi reboot:"
echo "    sudo systemctl enable glxScanIpMac"
echo ""
echo "  Chỉnh sửa cấu hình:"
echo "    vi $SCRIPT_DIR/config.json"
echo ""
