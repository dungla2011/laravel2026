<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Console</title>
    <style>
        body { font-family: Arial, sans-serif; background: #333; color: #fff; margin: 0; }
        #console { width: 100%; height: 80vh; overflow-y: auto; padding: 10px; background: #000; border: 1px solid #555; }
        #input { width: 70%; padding: 10px; background: #222; border: none; color: #fff; }
        #stop { width: 20%; padding: 10px; background: red; border: none; color: white; cursor: pointer; }
    </style>
</head>
<body>
<div id="console"></div>
<div>
    <input id="input" type="text" placeholder="Type your command here...">
    <button id="stop">Stop</button>
</div>
<script>
    const consoleDiv = document.getElementById('console');
    const input = document.getElementById('input');
    const stopBtn = document.getElementById('stop');

    const socket = new WebSocket('wss://mytree.vn:51115'); // Địa chỉ server Workerman

    socket.onmessage = (event) => {
        consoleDiv.innerHTML += `<div>> ${event.data}</div>`;
        consoleDiv.scrollTop = consoleDiv.scrollHeight; // Tự động cuộn xuống
    };

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            const command = input.value.trim();
            if (command) {
                consoleDiv.innerHTML += `<div style="color: cyan;">$ ${command}</div>`;
                socket.send(JSON.stringify({ action: 'execute', command })); // Gửi lệnh
                input.value = '';
            }
        }
    });

    stopBtn.addEventListener('click', () => {
        socket.send(JSON.stringify({ action: 'stop' })); // Gửi tín hiệu dừng
        consoleDiv.innerHTML += `<div style="color: red;">> Command stopped.</div>`;
    });
</script>
</body>
</html>
