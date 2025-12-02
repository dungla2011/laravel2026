
<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/public/index.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>
        Log EventMng
    </title>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <style>
        * {
            font-size: small;
            font-family: "Courier New";
        }

        button.refress_log {
            margin-bottom: 5px;
        }
    </style>


</head>
<body>

<button class="refress_log"> Refresh </button>


<?php
$logFile = '/var/glx/weblog/event_mng.log';

$logFileHis = '/var/glx/weblog/event_mng_his.log';

if (file_exists($logFile)) {
    echo '<button class="clear_log" title="Log cũ sẽ được Backup ra 1 file khác"> Clear Log </button><br>';
    if ($_GET['clear_log'] ?? '') {
        $old = file_get_contents($logFile);
        output($logFileHis,$old);
        rename($logFile, $logFile.'.'.time());
    }
}

// Hàm để đọc nội dung của file log
function tail($file, $lines = 3000)
{
    // Đảo ngược file để dễ dàng đọc các dòng cuối cùng
    $fp = fopen($file, 'r');
    fseek($fp, 0, SEEK_END);
    $pos = ftell($fp);
    $buffer = '';
    $linesFound = 0;

    while ($pos) {
        fseek($fp, --$pos, SEEK_SET);
        $char = fgetc($fp);

        if ($char === "\n") {
            // Đếm số dòng đã tìm thấy
            $linesFound++;

            // Nếu đủ số dòng cần tìm, dừng lại
            if ($linesFound >= $lines + 1) {
                break;
            }
        }

        $buffer = $char.$buffer;
    }

    fclose($fp);

    // Trả về các dòng cuối cùng đã đọc được
    return $buffer;
}

// Trả về dữ liệu log
if (file_exists($logFile)) {
    $ret = tail($logFile);
    echo $ret = str_replace("\n", "<br>\n", $ret);
}

?>
<script>
    document.addEventListener("DOMContentLoaded", function(event) {
        console.log(" llll ", document.body.scrollHeight);
        window.scrollTo(0,document.body.scrollHeight - 100);
        setTimeout(function (){
            window.scrollTo(0,document.body.scrollHeight - 100);
        },1000)
    });
</script>
<br>


<button class="refress_log"> Refresh </button>

<button class="clear_log" title="Log cũ sẽ được Backup ra 1 file khác"> Clear Log </button>

</body>

<script>

    $(function (){

        $(".refress_log").on('click', function (){
            window.location.reload();
        })

        $(".clear_log").on('click', function (){
            console.log("Click ...");
            let url = "/tool1/_site/event_mng/read_log_file_event.php?clear_log=1"
            $.ajax({
                url: url,
                type: 'GET',
                success: function (data, status) {
                    console.log(" data = ", data);
                    window.location.reload();
                },
                error: function () {
                    alert("Errror?");
                    console.log(" Eror....");
                },
            });
        })
    })
</script>

</html>
