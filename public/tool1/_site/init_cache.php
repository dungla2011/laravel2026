<?php

$hostname = gethostname();
//CreateCache System

$sizeDisk0 = 6000; //Megabyte

$markFile = "/mnt/glx/_cache_create_ok";

$ct = file_get_contents("/etc/passwd");
if (strstr($ct, "www-data")) {
    $userWeb = "www-data";
    //exec("chown -R www-data:www-data /mnt/glx && chmod 755 /mnt/glx -R ");
}
else
    if (strstr($ct, "apache"))
    $userWeb = "apache";


exec("chown -R $userWeb:$userWeb /mnt/glx && chmod 755 /mnt/glx -R ");

if (file_exists($markFile)) {
    echo "\n Already created!!";
}

$sizedisk = 0;
if (file_exists("/mnt/glx"))
    $sizedisk = disk_total_space("/mnt/glx");
$sizedisk = $sizedisk / 1024 / 1024;

if ($sizeDisk0 == $sizedisk) {
    exec("touch $markFile");
    echo "\nCache1 size OK, NOT create! STOP";
} else {

    if (!file_exists("/mnt/glx"))
        exec("mkdir /mnt/glx");

//    exec("umount /mnt/glx");
    exec("mount -v -t tmpfs -o size=" . $sizeDisk0 . "M none /mnt/glx");

    $sizedisk = disk_total_space("/mnt/glx");
    $sizedisk = $sizedisk / 1024 / 1024;

    if ($sizeDisk0 == $sizedisk) {

        exec("touch $markFile");

        echo "\nDisk Create OK : $sizedisk";
        exec("chmod 755 /mnt/glx");
        exec("mkdir /mnt/glx/weblog/");

        //Last Download IP
        if (file_exists("/var/glx/weblog/last-download-of-ip"))
            exec("mkdir -p /mnt/glx/weblog/last-download-of-ip/");

        //For count point user, max x point/h
        if (file_exists("/var/glx/weblog/last-bonus-point-of-ip")) {
            exec("mkdir /mnt/glx/weblog/last-bonus-point-of-ip/");
            exec("chown $userWeb:$userWeb /mnt/glx/weblog/last-bonus-point-of-ip/");
        }

        //For DoS
        exec("mkdir -p /mnt/glx/weblog/tmp_iplog/ipfiles/");

        //CountDownload
        exec("mkdir /mnt/glx/weblog/for_count_download");

        exec("chown $userWeb:$userWeb /mnt/glx/weblog/ -R");

        if (file_exists("/var/glx/weblog/cache"))
            exec("cp -uarfp /var/glx/weblog/cache /mnt/glx/weblog/");
        if (file_exists("/var/glx/weblog/fileinfo"))
            exec("cp -uarfp /var/glx/weblog/fileinfo /mnt/glx/weblog/");
        if (file_exists("/mnt/glx/laravel/framework/sessions/"))
            exec("mkdir -p /mnt/glx/laravel/framework/sessions/");

        exec("chown -R $userWeb:$userWeb /mnt/glx");

    } else {
        die("\n Error create RDISK, size not ok: $sizeDisk0<>$sizedisk MB");
    }
}

exec("chown -R $userWeb:$userWeb /mnt/glx/*");
exec("chmod 755 /mnt/glx/* -R ");


