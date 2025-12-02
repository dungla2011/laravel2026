<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "\n ABC123";

//DB_USERNAME_DEFAULT=for_sync
//DB_PASSWORD_DEFAULT=Qaz@12abc_000
//DB_HOST_VPN=12.0.0.54
$servername = '12.0.0.54';
$username = 'for_sync';
$password = 'Qaz@12abc_000';
$dbname = 'glx2023_for_testing';

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    exit('Connection failed: '.$conn->connect_error);
}

$sql = 'SELECT * FROM users LIMIT 10';
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo '<pre>';
        print_r($row);
        echo '</pre>';
    }
} else {
    echo '0 results';
}
$conn->close();
