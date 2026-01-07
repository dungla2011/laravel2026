#!/bin/bash
# ============================================================================
# init_network.sh
#
# Launcher script for init_network.py on Linux
# Automatically requests sudo if needed
# ============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SCRIPT_PATH="$SCRIPT_DIR/init_network.py"

# Check if Python 3 is available
if ! command -v python3 &> /dev/null; then
    echo "[ERROR] Python 3 is not installed"
    exit 1
fi

# Check if running as root
if [[ $EUID -ne 0 ]]; then
    echo "[INFO] Requesting sudo privileges..."
    sudo python3 "$SCRIPT_PATH" "$@"
    exit $?
else
    python3 "$SCRIPT_PATH" "$@"
    exit $?
fi
