#!/usr/bin/env python3
"""
Service wrapper cho IP Scanner
Đọc config từ file và chạy scanner
"""

import json
import sys
import os
from pathlib import Path

# Lấy đường dẫn thư mục hiện tại
SCRIPT_DIR = Path(__file__).parent
CONFIG_FILE = SCRIPT_DIR / "config.json"

def load_config(config_path=CONFIG_FILE):
    """Load cấu hình từ file JSON"""
    if not config_path.exists():
        print(f"Error: Config file not found at {config_path}")
        return None
    
    try:
        with open(config_path, 'r', encoding='utf-8') as f:
            config = json.load(f)
        return config
    except Exception as e:
        print(f"Error reading config: {e}")
        return None


def build_command(config):
    """Xây dựng command từ config"""
    cmd = ["python3", str(SCRIPT_DIR / "scan_ip_mac.py")]
    
    # Thêm các tham số
    if config.get("networks"):
        cmd.extend(["-m", config["networks"]])
    
    if config.get("output_file"):
        cmd.extend(["-o", str(SCRIPT_DIR / config["output_file"])])
    
    if config.get("loop_interval"):
        cmd.extend(["--loop", str(config["loop_interval"])])
    
    if config.get("threads"):
        cmd.extend(["-t", str(config["threads"])])
    
    if config.get("verbose"):
        cmd.append("-v")
    
    if config.get("start_octet"):
        cmd.extend(["-s", str(config["start_octet"])])
    
    if config.get("end_octet"):
        cmd.extend(["-e", str(config["end_octet"])])
    
    return cmd


def main():
    """Hàm main"""
    print("=" * 60)
    print("GLX IP Scanner Service")
    print("=" * 60)
    
    # Load config
    config = load_config()
    if not config:
        sys.exit(1)
    
    print(f"Service Name: {config.get('service_name', 'Unknown')}")
    print(f"Config File: {CONFIG_FILE}")
    print(f"Networks: {config.get('networks', 'N/A')}")
    print(f"Output File: {config.get('output_file', 'N/A')}")
    print(f"Loop Interval: {config.get('loop_interval', 0)} seconds")
    print(f"Threads: {config.get('threads', 100)}")
    print("=" * 60 + "\n")
    
    # Xây dựng command
    cmd = build_command(config)
    
    print(f"Command: {' '.join(cmd)}\n")
    
    # Chạy scanner
    import subprocess
    try:
        result = subprocess.run(cmd, cwd=SCRIPT_DIR)
        sys.exit(result.returncode)
    except KeyboardInterrupt:
        print("\n\nService stopped by user")
        sys.exit(0)
    except Exception as e:
        print(f"Error running service: {e}")
        sys.exit(1)


if __name__ == "__main__":
    main()
