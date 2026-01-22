/**
 * VMware Console Client using WMKS Library
 * Handles connection to ESXi console using VMware WebMKS SDK
 */

let wmks = null;
let consoleTicket = null;

// API endpoint
const API_URL = 'get_console_ticket.php';

// UI Elements
const connectBtn = document.getElementById('connectBtn');
const disconnectBtn = document.getElementById('disconnectBtn');
const statusDot = document.getElementById('statusDot');
const statusText = document.getElementById('statusText');
const messageDiv = document.getElementById('message');
const vmNameInput = document.getElementById('vmNameInput');
const hostSelect = document.getElementById('hostSelect');

// Info panel
const infoVmName = document.getElementById('infoVmName');
const infoHost = document.getElementById('infoHost');
const infoPowerState = document.getElementById('infoPowerState');
const infoTicket = document.getElementById('infoTicket');

function updateStatus(status, message) {
    statusDot.className = 'status-dot';
    
    switch(status) {
        case 'connected':
            statusDot.classList.add('connected');
            statusText.textContent = 'Connected';
            connectBtn.disabled = true;
            disconnectBtn.disabled = false;
            messageDiv.style.display = 'none';
            break;
        case 'connecting':
            statusDot.classList.add('connecting');
            statusText.textContent = 'Connecting...';
            connectBtn.disabled = true;
            disconnectBtn.disabled = true;
            showMessage('connecting', message || 'Connecting...');
            break;
        case 'disconnected':
            statusText.textContent = 'Disconnected';
            connectBtn.disabled = false;
            disconnectBtn.disabled = true;
            showMessage('info', message || 'Select ESXi host and enter VM name, then click Connect');
            break;
        case 'error':
            statusDot.classList.add('error');
            statusText.textContent = 'Error';
            connectBtn.disabled = false;
            disconnectBtn.disabled = true;
            showMessage('error', message || 'Connection error');
            break;
    }
}

function showMessage(type, text) {
    messageDiv.style.display = 'block';
    messageDiv.className = 'message';
    
    if (type === 'error') {
        messageDiv.classList.add('error');
        messageDiv.innerHTML = `❌ ${text}`;
    } else if (type === 'connecting') {
        messageDiv.innerHTML = `<div class="loading"></div> ${text}`;
    } else {
        messageDiv.innerHTML = text;
    }
}

async function getConsoleTicket(vmName, host) {
    try {
        updateStatus('connecting', 'Requesting console ticket...');
        
        const response = await fetch(`${API_URL}?vm=${encodeURIComponent(vmName)}&host=${encodeURIComponent(host)}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.error || 'Failed to get console ticket');
        }
        
        return data;
    } catch (error) {
        throw new Error(`Failed to get ticket: ${error.message}`);
    }
}

async function connectConsole() {
    const vmName = vmNameInput.value.trim();
    const host = hostSelect.value;
    
    if (!vmName || !host) {
        updateStatus('error', 'Please select ESXi host and enter VM name');
        return;
    }
    
    try {
        // Get console ticket
        updateStatus('connecting', `Getting console ticket for ${vmName}...`);
        consoleTicket = await getConsoleTicket(vmName, host);
        
        // Update info panel
        infoVmName.textContent = consoleTicket.vm_name || vmName;
        infoHost.textContent = consoleTicket.host || host;
        infoPowerState.textContent = consoleTicket.power_state || 'Unknown';
        infoTicket.textContent = consoleTicket.ticket ? consoleTicket.ticket.substring(0, 20) + '...' : '-';
        
        // Clear message
        messageDiv.style.display = 'none';
        
        // Initialize WMKS if not already created
        if (!wmks) {
            wmks = WMKS.createWMKS('wmksContainer', {})
                .register(WMKS.CONST.Events.CONNECTION_STATE_CHANGE, function(event, data) {
                    console.log('WMKS Connection state:', data.state);
                    
                    if (data.state === WMKS.CONST.ConnectionState.CONNECTED) {
                        updateStatus('connected', 'Console connected');
                    } else if (data.state === WMKS.CONST.ConnectionState.DISCONNECTED) {
                        updateStatus('disconnected', 'Console disconnected');
                    } else if (data.state === WMKS.CONST.ConnectionState.CONNECTING) {
                        updateStatus('connecting', 'Connecting to console...');
                    }
                })
                .register(WMKS.CONST.Events.ERROR, function(event, data) {
                    console.error('WMKS Error:', data);
                    updateStatus('error', 'Connection error: ' + (data.errorType || 'Unknown error'));
                })
                .register(WMKS.CONST.Events.REMOTE_SCREEN_SIZE_CHANGE, function(event, data) {
                    console.log('Screen size changed:', data);
                });
        }
        
        // Build WebMKS URL
        // Format: wss://host:port/ticket/ticket_value
        const wsUrl = `wss://${consoleTicket.host}:${consoleTicket.port}/ticket/${consoleTicket.ticket}`;
        
        console.log('Connecting to:', wsUrl);
        updateStatus('connecting', 'Connecting to VM console...');
        
        // Connect using WMKS
        wmks.connect(wsUrl);
        
    } catch (error) {
        console.error('Connection error:', error);
        updateStatus('error', error.message);
    }
}

function disconnectConsole() {
    if (wmks) {
        wmks.disconnect();
    }
    updateStatus('disconnected', 'Disconnected by user');
    
    // Reset info
    infoVmName.textContent = '-';
    infoHost.textContent = '-';
    infoPowerState.textContent = '-';
    infoTicket.textContent = '-';
}

// Helper functions for VM control
function sendCtrlAltDel() {
    if (wmks) {
        wmks.sendKeyCodes([17, 18, 46]); // Ctrl + Alt + Del
        console.log('Sent Ctrl+Alt+Del');
    } else {
        alert('Please connect to VM first');
    }
}

function sendWindowsKey() {
    if (wmks) {
        wmks.sendKeyCodes([91]); // Windows key
        console.log('Sent Windows key');
    } else {
        alert('Please connect to VM first');
    }
}

function updateScreen() {
    if (wmks) {
        wmks.updateScreen();
    }
}

// VM Power Control Functions
function powerOn() {
    if (!consoleTicket) {
        alert('Please connect to VM first');
        return;
    }
    
    if (!confirm('Are you sure you want to Power ON this VM?')) {
        return;
    }
    
    const vmName = vmNameInput.value.trim();
    const host = hostSelect.value;
    
    fetch(`get_console_ticket.php?vm=${encodeURIComponent(vmName)}&host=${encodeURIComponent(host)}&cmd=poweron`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Power ON command sent successfully');
                setTimeout(() => connectConsole(), 3000);
            } else {
                alert('Failed to power on: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

function powerOff() {
    if (!consoleTicket) {
        alert('Please connect to VM first');
        return;
    }
    
    if (!confirm('Are you sure you want to Power OFF this VM?')) {
        return;
    }
    
    const vmName = vmNameInput.value.trim();
    const host = hostSelect.value;
    
    fetch(`get_console_ticket.php?vm=${encodeURIComponent(vmName)}&host=${encodeURIComponent(host)}&cmd=poweroff`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Power OFF command sent successfully');
                if (wmks) {
                    wmks.disconnect();
                }
            } else {
                alert('Failed to power off: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

function resetVM() {
    if (!consoleTicket) {
        alert('Please connect to VM first');
        return;
    }
    
    if (!confirm('Are you sure you want to RESET this VM?')) {
        return;
    }
    
    const vmName = vmNameInput.value.trim();
    const host = hostSelect.value;
    
    fetch(`get_console_ticket.php?vm=${encodeURIComponent(vmName)}&host=${encodeURIComponent(host)}&cmd=reset`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reset command sent successfully');
                setTimeout(() => connectConsole(), 3000);
            } else {
                alert('Failed to reset: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
}

// Virtual Keyboard Functions
let keyboardVisible = false;
let keyboardMinimized = false;

function toggleKeyboard() {
    const keyboard = document.getElementById('virtualKeyboard');
    keyboardVisible = !keyboardVisible;
    keyboard.style.display = keyboardVisible ? 'block' : 'none';
    
    if (keyboardVisible) {
        keyboardMinimized = false;
        keyboard.classList.remove('minimized');
    }
}

function minimizeKeyboard() {
    const keyboard = document.getElementById('virtualKeyboard');
    keyboardMinimized = !keyboardMinimized;
    
    if (keyboardMinimized) {
        keyboard.classList.add('minimized');
    } else {
        keyboard.classList.remove('minimized');
    }
}

// Make keyboard draggable
let isDragging = false;
let currentX;
let currentY;
let initialX;
let initialY;
let xOffset = 0;
let yOffset = 0;

function initKeyboardDrag() {
    const keyboardHeader = document.getElementById('keyboardHeader');
    const keyboard = document.getElementById('virtualKeyboard');
    
    if (!keyboardHeader || !keyboard) {
        return;
    }

    keyboardHeader.addEventListener('mousedown', dragStart);
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', dragEnd);

    function dragStart(e) {
        initialX = e.clientX - xOffset;
        initialY = e.clientY - yOffset;
        
        if (e.target === keyboardHeader || keyboardHeader.contains(e.target)) {
            isDragging = true;
        }
    }

    function drag(e) {
        if (isDragging) {
            e.preventDefault();
            currentX = e.clientX - initialX;
            currentY = e.clientY - initialY;
            xOffset = currentX;
            yOffset = currentY;
            
            keyboard.style.transform = `translate(${currentX}px, ${currentY}px)`;
        }
    }

    function dragEnd(e) {
        isDragging = false;
    }
}

// Virtual Keyboard Key Press Handler
document.addEventListener('DOMContentLoaded', function() {
    // Initialize keyboard drag functionality
    initKeyboardDrag();
    
    const keys = document.querySelectorAll('.key');
    
    // Track toggle states for modifier keys
    const modifierStates = {
        capsLock: false,
        shift: false,
        control: false,
        alt: false
    };
    
    keys.forEach(key => {
        key.addEventListener('click', function() {
            const keyValue = this.getAttribute('data-key');
            
            if (wmks && keyValue) {
                // Map special keys to key codes
                const specialKeys = {
                    'Enter': 13,
                    '\n': 13,
                    'Tab': 9,
                    'Backspace': 8,
                    'Escape': 27,
                    'Delete': 46,
                    'Insert': 45,
                    'ArrowUp': 38,
                    'ArrowDown': 40,
                    'ArrowLeft': 37,
                    'ArrowRight': 39,
                    'Shift': 16,
                    'Control': 17,
                    'Alt': 18,
                    'CapsLock': 20,
                    'Meta': 91
                };
                
                // Handle toggle keys (CapsLock, Shift, Control, Alt)
                if (keyValue === 'CapsLock') {
                    modifierStates.capsLock = !modifierStates.capsLock;
                    this.classList.toggle('active-toggle', modifierStates.capsLock);
                    wmks.sendKeyCodes([specialKeys[keyValue]]);
                    console.log('CapsLock toggled:', modifierStates.capsLock);
                    return;
                } else if (keyValue === 'Shift') {
                    modifierStates.shift = !modifierStates.shift;
                    // Toggle all Shift keys
                    document.querySelectorAll('[data-key="Shift"]').forEach(shiftKey => {
                        shiftKey.classList.toggle('active-toggle', modifierStates.shift);
                    });
                    console.log('Shift toggled:', modifierStates.shift);
                    return;
                } else if (keyValue === 'Control') {
                    modifierStates.control = !modifierStates.control;
                    // Toggle all Control keys
                    document.querySelectorAll('[data-key="Control"]').forEach(ctrlKey => {
                        ctrlKey.classList.toggle('active-toggle', modifierStates.control);
                    });
                    console.log('Control toggled:', modifierStates.control);
                    return;
                } else if (keyValue === 'Alt') {
                    modifierStates.alt = !modifierStates.alt;
                    // Toggle all Alt keys
                    document.querySelectorAll('[data-key="Alt"]').forEach(altKey => {
                        altKey.classList.toggle('active-toggle', modifierStates.alt);
                    });
                    console.log('Alt toggled:', modifierStates.alt);
                    return;
                }
                
                // Check if it's a special key
                if (specialKeys[keyValue]) {
                    // Send with modifiers if active
                    if (modifierStates.control || modifierStates.alt) {
                        const keyCodes = [];
                        if (modifierStates.control) keyCodes.push(17); // Ctrl
                        if (modifierStates.alt) keyCodes.push(18); // Alt
                        keyCodes.push(specialKeys[keyValue]);
                        wmks.sendKeyCodes(keyCodes);
                        console.log('Special key with modifiers:', keyValue, keyCodes);
                        
                        // Auto-reset modifiers
                        if (modifierStates.control) {
                            modifierStates.control = false;
                            document.querySelectorAll('[data-key="Control"]').forEach(ctrlKey => {
                                ctrlKey.classList.remove('active-toggle');
                            });
                        }
                        if (modifierStates.alt) {
                            modifierStates.alt = false;
                            document.querySelectorAll('[data-key="Alt"]').forEach(altKey => {
                                altKey.classList.remove('active-toggle');
                            });
                        }
                    } else {
                        wmks.sendKeyCodes([specialKeys[keyValue]]);
                        console.log('Special key pressed:', keyValue, 'code:', specialKeys[keyValue]);
                    }
                } else if (keyValue.startsWith('F') && keyValue.length <= 3) {
                    // Function keys F1-F12
                    const fKeyNum = parseInt(keyValue.substring(1));
                    if (fKeyNum >= 1 && fKeyNum <= 12) {
                        wmks.sendKeyCodes([111 + fKeyNum]); // F1=112, F2=113, etc.
                        console.log('Function key pressed:', keyValue);
                    }
                } else {
                    // Regular character
                    let charToSend = keyValue;
                    
                    // Handle Ctrl/Alt combinations (e.g., Ctrl+A, Ctrl+C)
                    if (modifierStates.control || modifierStates.alt) {
                        // Get key code for the character
                        const charCode = keyValue.toUpperCase().charCodeAt(0);
                        const keyCodes = [];
                        
                        if (modifierStates.control) keyCodes.push(17); // Ctrl
                        if (modifierStates.alt) keyCodes.push(18); // Alt
                        keyCodes.push(charCode);
                        
                        wmks.sendKeyCodes(keyCodes);
                        console.log('Key combination:', keyValue, keyCodes);
                        
                        // Auto-reset modifiers after combination
                        if (modifierStates.control) {
                            modifierStates.control = false;
                            document.querySelectorAll('[data-key="Control"]').forEach(ctrlKey => {
                                ctrlKey.classList.remove('active-toggle');
                            });
                        }
                        if (modifierStates.alt) {
                            modifierStates.alt = false;
                            document.querySelectorAll('[data-key="Alt"]').forEach(altKey => {
                                altKey.classList.remove('active-toggle');
                            });
                        }
                    } else {
                        // Regular character - apply case based on CapsLock and Shift
                        // Check if it's a letter
                        if (keyValue.length === 1 && keyValue.match(/[a-zA-Z]/)) {
                            // XOR logic: CapsLock XOR Shift determines uppercase
                            const shouldBeUpperCase = modifierStates.capsLock !== modifierStates.shift;
                            charToSend = shouldBeUpperCase ? keyValue.toUpperCase() : keyValue.toLowerCase();
                        }
                        
                        wmks.sendInputString(charToSend);
                        console.log('Virtual key pressed:', charToSend, 'CapsLock:', modifierStates.capsLock, 'Shift:', modifierStates.shift);
                        
                        // Auto-reset Shift after character (like real keyboard)
                        if (modifierStates.shift) {
                            modifierStates.shift = false;
                            document.querySelectorAll('[data-key="Shift"]').forEach(shiftKey => {
                                shiftKey.classList.remove('active-toggle');
                            });
                        }
                    }
                }
                
                // Visual feedback for non-toggle keys
                if (!['CapsLock', 'Shift', 'Control', 'Alt'].includes(keyValue)) {
                    this.style.background = '#0e639c';
                    setTimeout(() => {
                        this.style.background = '';
                    }, 100);
                }
            } else {
                // Don't show alert if not connected - just do nothing silently
                console.log('Not connected to VM, key press ignored:', keyValue);
            }
        });
    });
});

// Allow Enter key to connect
vmNameInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        connectConsole();
    }
});

// Initialize
updateStatus('disconnected');