#!/bin/bash
################################################################################
# init-network-linux.sh
# 
# Linux network initialization script
# - Get MAC address of first NIC
# - Query metadata server for IP configuration
# - Apply IP, gateway, DNS using netplan
#
# Usage: sudo /opt/init-network.sh
################################################################################

set -e

# Configuration
METADATA_SERVER="${METADATA_SERVER:-10.1.1.1}"
METADATA_URL="http://$METADATA_SERVER/api/vm/getconfig"
LOG_FILE="/var/log/init-network.log"

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "[$(date)] Starting network initialization..." | tee -a $LOG_FILE

###############################################################################
# Function: Log message
###############################################################################
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $@" | tee -a $LOG_FILE
}

###############################################################################
# Function: Get first NIC name
###############################################################################
get_first_nic() {
    # Get first non-loopback interface
    ip -o link show | awk -F': ' '{print $2}' | grep -v "^lo$" | head -1
}

###############################################################################
# Function: Get MAC address of NIC
###############################################################################
get_mac_address() {
    local nic=$1
    ip link show $nic | awk '/ether/ {print $2}'
}

###############################################################################
# Function: Query metadata server
###############################################################################
query_metadata() {
    local mac=$1
    local url="${METADATA_URL}?mac=${mac}"
    
    log "🌐 Querying metadata server: $url"
    
    # Retry logic (try 5 times with 5s interval)
    for attempt in {1..5}; do
        log "  ⏳ Attempt $attempt/5..."
        
        response=$(curl -s -m 10 "$url" 2>&1 || true)
        
        if [[ -z "$response" ]]; then
            log "  ⏳ No response, retrying..."
            sleep 5
            continue
        fi
        
        # Check if response is valid JSON
        if echo "$response" | jq . > /dev/null 2>&1; then
            echo "$response"
            return 0
        else
            log "  ⚠️  Invalid JSON response: $response"
            sleep 5
            continue
        fi
    done
    
    log "❌ Failed to get metadata after 5 attempts"
    return 1
}

###############################################################################
# Function: Parse metadata and extract config
###############################################################################
parse_config() {
    local json=$1
    
    # Extract values using jq
    IP=$(echo "$json" | jq -r '.ip_address')
    SUBNET=$(echo "$json" | jq -r '.subnet_mask // .netmask')
    GATEWAY=$(echo "$json" | jq -r '.gateway')
    DNS_SERVERS=$(echo "$json" | jq -r '.dns_servers | join(",")')
    HOSTNAME=$(echo "$json" | jq -r '.hostname // "localhost"')
    
    log "✅ Metadata received:"
    log "   IP: $IP"
    log "   Subnet: $SUBNET"
    log "   Gateway: $GATEWAY"
    log "   DNS: $DNS_SERVERS"
    log "   Hostname: $HOSTNAME"
}

###############################################################################
# Function: Convert netmask to CIDR
###############################################################################
netmask_to_cidr() {
    local mask=$1
    
    # Convert netmask to CIDR notation
    # 255.255.255.0 → 24
    local bits=0
    local IFS='.'
    local -a octets=($mask)
    
    for octet in "${octets[@]}"; do
        for ((bit = 7; bit >= 0; bit--)); do
            if (( octet & (1 << bit) )); then
                ((bits++))
            fi
        done
    done
    
    echo $bits
}

###############################################################################
# Function: Configure network with netplan (Ubuntu 18+)
###############################################################################
configure_netplan() {
    local nic=$1
    local ip=$2
    local subnet=$3
    local gateway=$4
    local dns=$5
    
    log "🔧 Configuring network via netplan..."
    
    # Convert netmask to CIDR
    local cidr=$(netmask_to_cidr "$subnet")
    
    # Create netplan config
    local config_file="/etc/netplan/99-custom.yaml"
    
    log "   Creating $config_file"
    
    # Convert comma-separated DNS to array format
    local dns_array=$(echo "$dns" | sed 's/,/, /g')
    
    cat > "$config_file" <<EOF
network:
  version: 2
  ethernets:
    $nic:
      dhcp4: no
      addresses:
        - $ip/$cidr
      gateway4: $gateway
      nameservers:
        addresses: [$dns_array]
EOF
    
    log "   Netplan config:"
    cat "$config_file" | tee -a $LOG_FILE
    
    log "   Applying netplan..."
    netplan apply
    
    log "✅ Network configured"
}

###############################################################################
# Function: Configure network with ifupdown (Debian, older Ubuntu)
###############################################################################
configure_ifupdown() {
    local nic=$1
    local ip=$2
    local subnet=$3
    local gateway=$4
    local dns=$5
    
    log "🔧 Configuring network via ifupdown..."
    
    local config_file="/etc/network/interfaces.d/99-custom"
    
    cat > "$config_file" <<EOF
auto $nic
iface $nic inet static
    address $ip
    netmask $subnet
    gateway $gateway
    dns-nameservers $dns
EOF
    
    log "   Interface config:"
    cat "$config_file" | tee -a $LOG_FILE
    
    log "   Restarting networking..."
    systemctl restart networking
    
    log "✅ Network configured"
}

###############################################################################
# Function: Set hostname
###############################################################################
set_hostname() {
    local hostname=$1
    
    if [[ -z "$hostname" ]] || [[ "$hostname" == "null" ]] || [[ "$hostname" == "localhost" ]]; then
        log "ℹ️  Hostname not provided, skipping..."
        return
    fi
    
    log "🏠 Setting hostname: $hostname"
    
    hostnamectl set-hostname "$hostname"
    
    log "✅ Hostname set"
}

###############################################################################
# Function: Verify network configuration
###############################################################################
verify_config() {
    local nic=$1
    local ip=$2
    
    log "✔️  Verifying network configuration..."
    
    sleep 2
    
    # Check if IP is assigned
    ip_check=$(ip addr show $nic | grep "inet $ip" || true)
    
    if [[ -n "$ip_check" ]]; then
        log "✅ IP configured correctly: $ip_check"
        return 0
    else
        log "⚠️  IP not found, checking status..."
        ip addr show $nic | tee -a $LOG_FILE
        return 1
    fi
}

###############################################################################
# Main execution
###############################################################################

# Check if running as root
if [[ $EUID -ne 0 ]]; then
    log "❌ This script must be run as root"
    exit 1
fi

log "🖥️  System: $(hostname)"

# Get first NIC
NIC=$(get_first_nic)
if [[ -z "$NIC" ]]; then
    log "❌ No network interface found"
    exit 1
fi

log "📡 First NIC: $NIC"

# Get MAC address
MAC=$(get_mac_address "$NIC")
if [[ -z "$MAC" ]]; then
    log "❌ Could not get MAC address for $NIC"
    exit 1
fi

log "🔗 MAC Address: $MAC"

# Query metadata server
METADATA=$(query_metadata "$MAC")
if [[ -z "$METADATA" ]]; then
    log "❌ Could not retrieve metadata"
    exit 1
fi

# Parse configuration
parse_config "$METADATA"

# Validate required fields
if [[ -z "$IP" ]] || [[ "$IP" == "null" ]]; then
    log "❌ No IP address in metadata"
    exit 1
fi

# Check which network configuration method to use
if [[ -f "/etc/netplan/00-installer-config.yaml" ]] || [[ -d "/etc/netplan" ]]; then
    configure_netplan "$NIC" "$IP" "$SUBNET" "$GATEWAY" "$DNS_SERVERS"
else
    configure_ifupdown "$NIC" "$IP" "$SUBNET" "$GATEWAY" "$DNS_SERVERS"
fi

# Set hostname
set_hostname "$HOSTNAME"

# Verify configuration
verify_config "$NIC" "$IP"

log "✅ Network initialization completed successfully!"
exit 0
