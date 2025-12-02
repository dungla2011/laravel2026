<?php
error_reporting(E_ALL);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = '4share.vn';
require_once "/var/www/html/public/index.php";

if(!isAdminCookie())
   die("ABC = ");


?>

<table border="1">
<?php
if($server = $_GET['sv'] ?? ''){
    $link = "https://$server/sysinfo_glx.html?disklist_ex2=1&get_temp=1";
    $ct = file_get_contents($link);
    $js = json_decode($ct);

    foreach ($js AS $mm){
        echo "\n<tr>";
        foreach ($mm AS $k=>$v){
            echo "\n <td> $k => $v </td>";
        }
        echo "\n</tr>";
    }

}

?>

</table>
