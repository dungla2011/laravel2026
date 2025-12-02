<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "4share.vn";
require_once "/var/www/html/public/index.php";
check_unique_script();


if(!isAdminReal_())
if(!isIPVpn()){
    die("Not vpn?");
}

$mm  = \Base\ModelCloudServer::getArrayWhereSql("enable = 1");

foreach ($mm AS $obj){
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($obj);
//    echo "</pre>";

    $md = explode(",", $obj->mount_list);
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($md);
//    echo "</pre>";
    ///mountfile/sv93/sdb/
    $sv = explode(".", $obj->domain)[0];

    $path = "/mountfile/$sv";
    foreach ($md AS $id=>$disk){
//        echo "<br/>\n$disk";
        for($i = 'a'; $i <= 'y'; $i++){
            if($disk == "sd$i"){
                $pathDisk = $path."/$disk";
                $sizeDisk = disk_total_space($pathDisk);
                $fileSig = $pathDisk."/_$disk";
                if(file_exists($fileSig)){
                    echo "<br/>\n Disk mount ok $fileSig";
                }
                else{
                    echo "<br/>\n Error:Disk not valid? $fileSig";
                }

                if($sizeDisk < 1000 * _GB){
                    echo "<br/>\n  Error: Size disk not valid ".ByteSize($sizeDisk);;
                }
                else
                    echo "<br/>\n Size OK: $pathDisk " . ByteSize($sizeDisk);

                break;
            }
        }
    }

}
