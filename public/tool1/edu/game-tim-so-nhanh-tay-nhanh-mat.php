<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "/var/www/html/public/index.php";

?>
<meta charset="UTF-8">

<title>
    Thi Tìm số - Nhanh tay nhanh mắt
</title>


<meta name="description" content="Thi nhanh tay nhanh mắt, giúp các bé [ và bố mẹ =)) ] tránh xa điện thoại!" />

<script src="/assert/library_ex/js/dom-to-image.js"></script>
<script src="/assert/library_ex/js/FileSaver.js"></script>


<div id="allPage" style="padding: 10px">

<div>
    <h1 style="font-size: 20px">
    Thi tìm số nhanh tay nhanh mắt
    </h1>
    Số ngẫu nhiên từ 1-100
</div>
<hr>
<?php


$mm = [];
for($i = 1; $i <= 100; $i++){
    $mm[] = $i;
}

shuffle($mm);

for($i = 1; $i < 100; $i++){
    $num = $mm[$i];
    $degre = rand(-90,90);
    echo " <span style='transform: rotate(".$degre."deg);  display: inline-block; padding: 20px; '> <span style='border-bottom: 1px dotted'>$num</span> </span>    ";

}

?>

<hr>
<b>Hướng dẫn</b>: in bản này ra, 02 người chơi trở lên, dùng bút gạch vào số lần lượt từ 1-100, bút màu khác nhau, hoặc cách gạch khác nhau. Khi gạch hết hết đếm lại số gạch, ai tìm được nhiều hơn là thắng
<br>
- Kịch bản khác:
<br>
- 1 người gạch chẵn, 1 người gạch lẻ, ai xong trước là thắng
<br>
- Gạch số chia hết cho 2,3,4,5...
<br>

<?php
echo \LadLib\Common\UrlHelper1::getFullUrl();
?>
    <br>
    Bấm Ctrl + Print để in trang
<br>
    (Bấm F5 để tạo bài mới, các số ngẫu nhiên!)


</div>


<script>
    function downloadImage(){

        function filter (node) {
            return (node.tagName !== 'i');
        }

        var elm = document.getElementsByTagName("g")[0];
        elm = document.getElementById('allPage');

        domtoimage.toPng(elm, {filter: filter})
            .then(function (dataUrl) {
//                console.log(" Datax: " + dataUrl);
                /* do something */
                window.saveAs(dataUrl, '<?php echo basename(__FILE__) ?>.png');
            });

        //domtoimage.toBlob(document.getElementById('graph1')
        //,options
        //).then(function (blob) {
        //        window.saveAs(blob, '<?php //echo $title ?>//.png');
        //    });
    }
</script>


<!--<br>-->
<!--<button onclick="downloadImage()">Tải ảnh để In</button>-->
<!--<br>-->
