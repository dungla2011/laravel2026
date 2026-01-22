# VMware Console Access

Web-based console access to ESXi VMs using VMware WebMKS protocol.

## Files

- `get_console_ticket.py` - Python script to get console ticket from ESXi
- `get_console_ticket.php` - PHP wrapper to call Python script
- `console.html` - Web UI for console access
- `console.js` - JavaScript WebSocket client

## Requirements

- Python 3.x with `pyvmomi` installed
- ESXi credentials in `.env` file
- Web server (Apache/Nginx) with PHP support

## Usage

### 1. Setup Environment

Create `.env` file in project root:
```
ES_USER=root
ES_PW=your_password_prefix
ES_PW2=your_password_prefix_for_host_19
```

### 2. Install Python Dependencies

```bash
pip install pyvmomi python-dotenv
```

### 3. Test Python Script

```bash
python get_console_ticket.py vm=MyVM host=10.0.1.11
```

Expected output:
```json
{
  "success": true,
  "ticket": "cst-VCT-...",
  "host": "10.0.1.11",
  "port": 443,
  "cfgFile": "/vmfs/volumes/.../MyVM.vmx",
  "vm_name": "MyVM",
  "vm_id": "...",
  "power_state": "poweredOn"
}
```

### 4. Access Web Console

1. Open `console.html` in browser
2. Select ESXi host from dropdown
3. Enter VM name
4. Click "Connect"

## Architecture

```
Browser (console.html)
      ↓
   JavaScript (console.js)
      ↓
   PHP Wrapper (get_console_ticket.php)
      ↓
   Python Script (get_console_ticket.py)
      ↓
   ESXi API (pyVmomi)
      ↓
   WebSocket Connection (WebMKS)
```

## Flow

1. **Get Ticket**: Browser calls PHP → PHP calls Python → Python logs into ESXi and gets console ticket
2. **Connect WebSocket**: JavaScript opens WebSocket to ESXi using ticket
3. **Console Stream**: WebSocket streams screen updates and forwards keyboard/mouse events

## Troubleshooting

### "VM not found"
- Check VM name is exact (case-sensitive)
- Verify VM is on the selected ESXi host

### "VM is not powered on"
- Power on VM first
- Console only works for running VMs

### WebSocket connection fails
- Check ESXi firewall allows WebSocket on port 443/902
- Verify SSL certificate (may need to accept self-signed cert)
- Check browser console for detailed errors

### Python script timeout
- Check network connectivity to ESXi host
- Verify credentials in `.env` file
- Increase timeout in Python script

## Notes

- VMware WebMKS protocol is proprietary and may require additional libraries
- For production use, consider using VMware official WMKS SDK
- Console access requires proper ESXi permissions
- SSL certificate warnings are expected with self-signed ESXi certs
