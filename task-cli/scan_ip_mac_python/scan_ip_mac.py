#!/usr/bin/env python3
"""
IP Scanner - Quét dải IP và lấy MAC Address
"""

import json
import re
import subprocess
import argparse
import sys
import time
from pathlib import Path
from typing import Dict, List, Tuple
from datetime import datetime
from concurrent.futures import ThreadPoolExecutor, as_completed
from threading import Lock
from queue import Queue


class IPScanner:
    """Lớp để quét dải IP và lấy MAC Address"""
    
    def __init__(self, ip_class: str, nic: str, output_file: str = None):
        """
        Khởi tạo scanner
        
        Args:
            ip_class: Dải IP (ví dụ: '10.0.0.')
            nic: Tên card mạng (ví dụ: 'ens160', 'eth0')
            output_file: Đường dẫn file JSON output
        """
        self.ip_class = ip_class
        self.nic = nic
        self.output_file = output_file or f"scan_result_.json"
        self.results: Dict[str, str] = {}
        self.failed_ips: List[str] = []
        self.lock = Lock()  # Thread-safe lock for results
        self.completed = 0
        self.total = 0
    
    def execute_arping(self, ip: str) -> Tuple[bool, str]:
        """
        Chạy lệnh arping để quét IP
        
        Args:
            ip: Địa chỉ IP cần quét
            
        Returns:
            Tuple[bool, str]: (Thành công, MAC Address hoặc lỗi)
        """
        try:
            # Lệnh arping cho Linux - tối ưu cho tốc độ
            # Lệnh này tăng 3 giây cho chắc chắn được không?

            cmd = ["arping", "-I", self.nic, "-c", "3", "-w", "3", ip]
            
            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                timeout=3
            )
            
            if result.returncode == 0:
                # Parse MAC address từ output
                mac = self._parse_mac_address(result.stdout)
                if mac:
                    return True, mac
                else:
                    return False, "Không thể trích xuất MAC address"
            else:
                return False, f"Arping failed: {result.stderr[:100]}"
                
        except FileNotFoundError:
            return False, "arping command not found. Install it with: apt install iputils-arping"
        except subprocess.TimeoutExpired:
            return False, "Timeout"
        except Exception as e:
            return False, str(e)
    
    def _parse_mac_address(self, arping_output: str) -> str:
        """
        Trích xuất MAC address từ output của arping
        
        Args:
            arping_output: Output từ lệnh arping
            
        Returns:
            MAC address hoặc None
        """
        # Pattern để match MAC address (xx:xx:xx:xx:xx:xx)
        pattern = r'([0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2}:[0-9a-fA-F]{2})'
        match = re.search(pattern, arping_output)
        
        if match:
            return match.group(1)
        return None
    
    def _scan_ip(self, ip: str, verbose: bool = False) -> None:
        """
        Quét một IP (được gọi trong thread)
        
        Args:
            ip: Địa chỉ IP cần quét
            verbose: Hiển thị chi tiết
        """
        success, result = self.execute_arping(ip)
        
        with self.lock:
            if success:
                self.results[ip] = result
                if verbose:
                    print(f"✓ {ip:15} -> {result}")
            else:
                self.failed_ips.append(ip)
                if verbose:
                    print(f"✗ {ip:15} -> {result}")
            
            self.completed += 1
            # Hiển thị tiến độ
            progress = (self.completed / self.total) * 100
            sys.stdout.write(f"\rProgress: {self.completed}/{self.total} ({progress:.1f}%)")
            sys.stdout.flush()
    
    def scan_range(self, start: int = 1, end: int = 255, verbose: bool = False, num_threads: int = 50) -> None:
        """
        Quét dải IP từ start đến end sử dụng multithreading
        
        Args:
            start: IP bắt đầu (octet cuối cùng)
            end: IP kết thúc (octet cuối cùng)
            verbose: Hiển thị chi tiết
            num_threads: Số thread chạy song song (mặc định: 50)
        """
        self.total = end - start + 1
        self.completed = 0
        
        ip_list = [f"{self.ip_class}{i}" for i in range(start, end + 1)]
        
        with ThreadPoolExecutor(max_workers=num_threads) as executor:
            futures = [executor.submit(self._execute_and_track, ip, verbose) for ip in ip_list]
            for _ in as_completed(futures):
                pass
        
        print()  # Dòng mới sau tiến độ
    
    def add_ips(self, ip_list: List[str], verbose: bool = False, num_threads: int = 50) -> None:
        """
        Quét danh sách IP cụ thể sử dụng multithreading
        
        Args:
            ip_list: Danh sách IP cần quét
            verbose: Hiển thị chi tiết
            num_threads: Số thread chạy song song
        """
        self.total = len(ip_list)
        self.completed = 0
        
        with ThreadPoolExecutor(max_workers=num_threads) as executor:
            futures = [executor.submit(self._execute_and_track, ip, verbose) for ip in ip_list]
            for _ in as_completed(futures):
                pass
        
        print()  # Dòng mới sau tiến độ
    
    def _execute_and_track(self, ip: str, verbose: bool = False) -> None:
        """
        Quét IP và tracking tiến độ
        """
        self._scan_ip(ip, verbose)
    
    def save_json(self) -> None:
        """Lưu kết quả vào file JSON"""
        data = {
            "scan_info": {
                "ip_class": self.ip_class,
                "network_interface": self.nic,
                "timestamp": datetime.now().isoformat(),
                "total_found": len(self.results),
                "total_failed": len(self.failed_ips)
            },
            "results": self.results,
            "failed_ips": self.failed_ips
        }
        
        try:
            with open(self.output_file, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"\n✓ Kết quả đã được lưu vào: {self.output_file}")
            print(f"  - Tìm được: {len(self.results)} IP")
            print(f"  - Quét thất bại: {len(self.failed_ips)} IP")
        except Exception as e:
            print(f"✗ Lỗi khi lưu file: {e}")


def parse_network_configs(config_string: str) -> List[Tuple[str, str]]:
    """
    Parse network config string thành danh sách (ip_class, interface)
    
    Args:
        config_string: Chuỗi cấu hình theo định dạng: "10.0.0.0-ens160,10.0.1.0-ens160"
        
    Returns:
        Danh sách tuple (ip_class, interface)
    """
    configs = []
    for item in config_string.split(','):
        item = item.strip()
        if '-' not in item:
            continue
        parts = item.split('-')
        if len(parts) == 2:
            ip_part, nic_part = parts[0].strip(), parts[1].strip()
            # Chuyển IP cuối thành dấu chấm (10.0.0.0 -> 10.0.0.)
            if ip_part.endswith('.0'):
                ip_part = ip_part[:-1]  # Loại bỏ .0
            elif not ip_part.endswith('.'):
                ip_part = ip_part + '.'
            configs.append((ip_part, nic_part))
    return configs


def main():
    """Hàm main"""
    parser = argparse.ArgumentParser(
        description="Quét dải IP và lấy MAC Address bằng arping",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Ví dụ (một dải):
  python scan_ip_mac.py -c 10.0.0. -i ens160
  
Ví dụ (nhiều dải):
  python scan_ip_mac.py -m "10.0.0.0-ens160,10.0.1.0-ens160,10.0.21.0-ens160,103.163.216.0-ens192,103.163.217.0-ens192"
  
Tùy chọn:
  python scan_ip_mac.py -c 192.168.1. -i eth0 -s 1 -e 50 -o results.json -v
        """
    )
    
    parser.add_argument(
        "-c", "--ip-class",
        help="Dải IP đơn (ví dụ: '10.0.0.')"
    )
    
    parser.add_argument(
        "-i", "--interface",
        help="Tên card mạng (ví dụ: 'ens160', 'eth0')"
    )
    
    parser.add_argument(
        "-m", "--multi",
        help="Cấu hình nhiều dải IP (ví dụ: '10.0.0.0-ens160,10.0.1.0-ens160')"
    )
    
    parser.add_argument(
        "-s", "--start",
        type=int,
        default=1,
        help="IP bắt đầu (octet cuối cùng, mặc định: 1)"
    )
    
    parser.add_argument(
        "-e", "--end",
        type=int,
        default=254,
        help="IP kết thúc (octet cuối cùng, mặc định: 254)"
    )
    
    parser.add_argument(
        "-o", "--output",
        help="Đường dẫn file JSON output (mặc định: scan_result_TIMESTAMP.json)"
    )
    
    parser.add_argument(
        "-v", "--verbose",
        action="store_true",
        help="Hiển thị chi tiết từng IP"
    )
    
    parser.add_argument(
        "--loop",
        type=int,
        default=0,
        help="Lặp lại quét sau N giây (0 = không lặp, mặc định: 0)"
    )
    
    parser.add_argument(
        "-t", "--threads",
        type=int,
        default=100,
        help="Số thread chạy song song (mặc định: 100, khuyến nghị: 50-150)"
    )
    
    args = parser.parse_args()
    
    # Xác định configs
    configs = []
    if args.multi:
        configs = parse_network_configs(args.multi)
    elif args.ip_class and args.interface:
        ip_class = args.ip_class
        if not ip_class.endswith('.'):
            ip_class += '.'
        configs = [(ip_class, args.interface)]
    else:
        parser.print_help()
        sys.exit(1)
    
    print(f"{'='*60}")
    print(f"IP Scanner - Quét dải IP và lấy MAC Address")
    print(f"{'='*60}")
    print(f"Số dải cần quét: {len(configs)}")
    for idx, (ip_class, nic) in enumerate(configs, 1):
        print(f"  {idx}. {ip_class}* trên {nic}")
    print(f"Range: {args.start} - {args.end}")
    print(f"Output file: {args.output or 'scan_result_TIMESTAMP.json'}")
    if args.loop > 0:
        print(f"Loop mode: Mỗi {args.loop} giây")
    print(f"{'='*60}\n")
    
    # Vòng lặp chính
    loop_count = 0
    while True:
        loop_count += 1
        if args.loop > 0:
            print(f"\n[Lần {loop_count}] Bắt đầu quét lúc {datetime.now().strftime('%H:%M:%S')}")
        
        # Quét tất cả configs
        all_results = {}
        all_failed = []
        all_ips = []
        ip_to_nic = {}
        
        print(f"Tạo danh sách IP từ {len(configs)} dải...")
        for ip_class, nic in configs:
            for i in range(args.start, args.end + 1):
                ip = f"{ip_class}{i}"
                all_ips.append(ip)
                ip_to_nic[ip] = nic
        
        print(f"Tổng cộng {len(all_ips)} IP cần quét\n")
        
        # Quét tất cả IP cùng lúc
        print(f"Quét tất cả IP song song...")
        
        # Lưu thời gian bắt đầu
        start_time = datetime.now()
        
        # Tạo scanner chính (dùng để quét)
        main_scanner = IPScanner("", "", None)
        main_scanner.results = all_results
        main_scanner.failed_ips = all_failed
        main_scanner.total = len(all_ips)
        main_scanner.completed = 0
        
        # Override execute_arping để sử dụng NIC phù hợp cho mỗi IP
        def execute_arping_with_nic(self, ip: str) -> Tuple[bool, str]:
            nic = ip_to_nic[ip]
            try:
                cmd = ["arping", "-I", nic, "-c", "1", "-w", "1", ip]
                result = subprocess.run(
                    cmd,
                    capture_output=True,
                    text=True,
                    timeout=3
                )
                
                if result.returncode == 0:
                    mac = self._parse_mac_address(result.stdout)
                    if mac:
                        return True, mac
                    else:
                        return False, "Không thể trích xuất MAC address"
                else:
                    return False, f"Arping failed: {result.stderr[:100]}"
                    
            except FileNotFoundError:
                return False, "arping command not found. Install it with: apt install iputils-arping"
            except subprocess.TimeoutExpired:
                return False, "Timeout"
            except Exception as e:
                return False, str(e)
        
        # Gán method mới cho scanner
        main_scanner.execute_arping = lambda ip: execute_arping_with_nic(main_scanner, ip)
        
        # Quét tất cả IP
        main_scanner.add_ips(all_ips, args.verbose, num_threads=args.threads)
        
        all_results = main_scanner.results
        all_failed = main_scanner.failed_ips
        
        # Tính thời gian quét
        end_time = datetime.now()
        scan_duration = (end_time - start_time).total_seconds()
        
        # Lưu kết quả tổng hợp
        if args.output:
            output_file = args.output
        else:
            output_file = f"scan_result_.json"
        
        data = {
            "scan_info": {
                "networks": [f"{ip}{int(args.start)}-{int(args.end)} ({nic})" for ip, nic in configs],
                "timestamp": datetime.now().isoformat(),
                "total_found": len(all_results),
                "scan_duration_seconds": scan_duration
            },
            "results": all_results
        }
        
        try:
            with open(output_file, 'w', encoding='utf-8') as f:
                json.dump(data, f, indent=2, ensure_ascii=False)
            print(f"\n✓ Kết quả đã được lưu vào: {output_file}")
            print(f"  - Tìm được: {len(all_results)} IP")
            print(f"  - Thời gian quét: {scan_duration:.2f} giây")
            
            if args.loop > 0:
                print(f"  - Chờ {args.loop} giây trước lần quét tiếp theo... (Ctrl+C để dừng)")
                time.sleep(args.loop)
            else:
                break
        except Exception as e:
            print(f"✗ Lỗi khi lưu file: {e}")
            if args.loop > 0:
                print(f"  - Chờ {args.loop} giây trước lần quét tiếp theo...")
                time.sleep(args.loop)
            else:
                break


if __name__ == "__main__":
    main()
