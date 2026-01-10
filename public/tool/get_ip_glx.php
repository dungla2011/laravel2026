<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
//
//

//$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';

require "/var/www/html/vendor/autoload.php";
$app = require_once ('/var/www/html/bootstrap/app.php');
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$mac = trim(strtolower($_REQUEST['mac'] ?? ''));
$mac = str_replace("-", ':', $mac);

if(!$mac){
    die("Not mac address!");
}

$sid = \App\Models\SiteMng::getSiteId();


// Get init_ip from VPS by MAC address (latest VPS first)
$vps = \App\Models\VpsInstance::whereRaw("LOWER(init_mac_address) LIKE ?", ["%{$mac}%"])
    ->whereNotNull('init_ip')
    ->orderBy('id', 'desc')
    ->first();

if (!$vps || !$vps->init_ip) {
    die("MAC address not found or no IP assigned!");
}

$ip = $vps->init_ip;
$subnet = "255.255.254.0"; // Default subnet
$gateway = "103.163.216.1"; // Default gateway
$dns1 = "8.8.8.8";
$dns2 = "8.8.4.4";
$hostname = $vps->name;

// Return format: ip,subnet,gateway,dns1,dns2,hostname
$return = "{$ip},{$subnet},{$gateway},{$dns1},{$dns2},{$hostname}";

die($return);
