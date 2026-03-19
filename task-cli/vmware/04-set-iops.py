#!/usr/bin/env python3
"""
VPS Creation Daemon
Continuously monitors vps_instances table and creates VPS instances based on init_os
Clones VMs and updates creation status in database

Install: pip install pyvmomi pymysql python-dotenv
"""

import atexit
import ssl
import sys
import os
import json
import time
import threading
from datetime import datetime
from threading import Thread, Lock
from dotenv import load_dotenv
import pymysql
from pyVmomi import vim, vmodl
from pyVim.connect import SmartConnect, Disconnect

# Load environment variables from parent .env
env_path = os.path.join(os.path.dirname(__file__), '../../.env')
load_dotenv(env_path)

# Global lock for thread safety
db_lock = Lock()
print_lock = Lock()

def print_log(msg, level="info"):
    """Thread-safe print logging"""
    with print_lock:
        print(msg)

def get_db_connection():
    """Create and return database connection"""
    try:
        conn = pymysql.connect(
            host=os.getenv('DB_RM_HOST8', 'localhost'),
            port=int(os.getenv('DB_PORT', 3306)),
            user=os.getenv('DB_RM_USER8', 'root'),
            password=os.getenv('DB_RM_PW8', ''),
            database=os.getenv('DB_RM_NAME8', 'laravel'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        return conn
    except Exception as e:
        print(f"❌ Database connection error: {e}")
        return None

