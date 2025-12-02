<?php

require_once '/var/www/html/public/index.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebSocket Message Sender</title>
    <style>
        #statusIcon {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: red;
            display: inline-block;
        }
        .blinking-green {
            animation: blinking-green 1s infinite;
        }
        .blinking-red {
            animation: blinking-red 1s infinite;
        }
        @keyframes blinking-green {
            0% { background-color: green; }
            50% { background-color: lightgreen; }
            100% { background-color: green; }
        }
        @keyframes blinking-red {
            0% { background-color: white; }
            100% { background-color: red; }
        }
    </style>
</head>
<body>

<?php
$uid = getCurrentUserId();
$tk = $_COOKIE['_tglx863516839'] ?? '';
echo $uid;
?>
<div id="statusIcon"></div>
<input type="text" value="123" id="messageInput" placeholder="Enter your message">
<input type="text" id="recipientIdInput" placeholder="Enter recipient ID">
<button id="sendMessageButton">Send Message</button>

<script>
    let ws;
    const statusIcon = document.getElementById('statusIcon');

    const connectWebSocket = () => {
        ws = new WebSocket('wss://mytree.vn:51112?tkx=<?php echo $tk; ?>');

        ws.onopen = function () {
            console.log('Connected to WebSocket server');
            statusIcon.classList.remove('blinking-red');
            statusIcon.classList.add('blinking-green');
        };

        ws.onmessage = function (event) {
            console.log('Message from server: ', event.data);
        };

        ws.onclose = function () {
            console.log('WebSocket connection closed, attempting to reconnect...');
            statusIcon.classList.remove('blinking-green');
            statusIcon.classList.add('blinking-red');
            setTimeout(connectWebSocket, 2000); // Attempt to reconnect after 2 seconds
        };

        ws.onerror = function (error) {
            console.log('WebSocket error: ', error);
            ws.close();
        };
    };
    connectWebSocket();

    document.getElementById('sendMessageButton').addEventListener('click', function() {
        const message = document.getElementById('messageInput').value;
        const recipientId = document.getElementById('recipientIdInput').value;
        if (message && recipientId) {
            ws.send(JSON.stringify({ recipient_id: recipientId, message: message }));
            console.log('Message sent: ', message);
        } else {
            console.log('Please enter a message and recipient ID');
        }
    });
</script>
</body>
</html>
