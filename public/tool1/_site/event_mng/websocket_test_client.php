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
1 UserID trên Các web TAB khác nhau, server nhận làm nhiều client, là OK, để tránh bị case chỉ tính 1 client
<br> Mỗi lần F5 , server lại ghi nhận là 1 client
<hr>
<?php
$uid = getCurrentUserId();
$tk = $_COOKIE['_tglx863516839'] ?? '';
echo "uid = " . $uid;
?>

<br>

<div id="statusIcon"></div>
<input type="text" value="123" id="messageInput" placeholder="Enter your message">
<input type="text" value="111" id="recipientIdInput" placeholder="Enter recipient ID"><button id="sendMessageButton">Send Message</button>
<br><br>
<textarea name="" id="log1" style="width: 800px; height: 600px"></textarea>



<script>
    let ws;
    const statusIcon = document.getElementById('statusIcon');

    function addLog(log) {
        document.getElementById('log1').value = log + "\n" + document.getElementById('log1').value;
    }

    const connectWebSocket = () => {
        ws = new WebSocket('wss://events.dav.edu.vn:51111?tkx=<?php echo $tk; ?>');

        ws.onopen = function () {
            console.log('Connected to WebSocket server');
            addLog('Connected to WebSocket server');
            statusIcon.classList.remove('blinking-red');
            statusIcon.classList.add('blinking-green');
        };

        ws.onmessage = function (event) {
            console.log('Message from server: ', event.data);
            addLog(event.data);
        };

        ws.onclose = function () {
            console.log('WebSocket connection closed, attempting to reconnect...');
            addLog('WebSocket connection closed, attempting to reconnect...')
            statusIcon.classList.remove('blinking-green');
            statusIcon.classList.add('blinking-red');
            setTimeout(connectWebSocket, 2000); // Attempt to reconnect after 2 seconds
        };

        ws.onerror = function (error) {
            console.log('WebSocket error: ', error);
            addLog('WebSocket error: ' + error)
            ws.close();
        };
    };
    connectWebSocket();

    document.getElementById('sendMessageButton').addEventListener('click', function() {
        const message = document.getElementById('messageInput').value;
        const recipientId = document.getElementById('recipientIdInput').value;
        if (message) {
            ws.send(JSON.stringify({ recipient_id: recipientId, message: message }));
            console.log('Message sent: ', message);
            addLog('Message sent: ' + message);
        } else {
            console.log('Please enter a message and recipient ID');
            addLog('Please enter a message and recipient ID');
        }
    });
</script>
</body>
</html>
