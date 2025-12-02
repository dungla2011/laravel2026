#!/bin/bash
APP_DIR="/var/www/html/task-cli/site/events-dav"
VENV_DIR="$APP_DIR/venv"
LOG_FILE="/var/log/face_api.log"
SCRIPT="face_api.py"

cd "$APP_DIR"

# Nếu có tham số rs thì kill tiến trình cũ
if [ "$1" = "rs" ]; then
    PID=$(pgrep -f "$SCRIPT")
    if [ -n "$PID" ]; then
        echo "Đang kill tiến trình cũ (PID: $PID)..."
        kill -9 $PID
    fi
fi

# Kích hoạt venv
source "$VENV_DIR/bin/activate"

# Chạy script mới
echo "Khởi động $SCRIPT ..."
nohup python3 "$SCRIPT" >> "$LOG_FILE" 2>&1 &
echo "Đã chạy background, log ghi tại $LOG_FILE"
