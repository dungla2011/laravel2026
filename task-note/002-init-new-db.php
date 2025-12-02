<?php

require_once __DIR__.'/../public/index.php';

$dbInfo = (\App\Components\Helper1::getDBInfo());

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($dbInfo);
echo '</pre>';

$dbName = $dbInfo['database'];
$dbHost = $dbInfo['host'];
$dbUsername = $dbInfo['username'];
$dbPw = $dbInfo['password'];

$newDbName = getch('DBname: ');

if (! $dbName) {
    exit("\n\n*** Error: not db name? $dbName");
}

$file = 'e:/mydb_dump_to_copy.db';
if (file_exists($file)) {
    unlink($file);
}

echo "\n FILE = $file / $dbPw";

//mysqldump -u root -p password db1 > dump.sql
//mysqladmin -u root -p password create db2
//mysql -u root -p password db2 < dump.sql

//$cmd = "mysqldump -P 3306 -h $dbHost -u $dbUsername –p$dbPw $dbName --ignore-table=$dbName.rand_table   > $file";
//echo "\n CMD = $cmd ";
$cmd = "mysqldump -P 3306 -h $dbHost -u $dbUsername -p $dbPw $dbName > e:/dump1.sql";
getch("$cmd");
exec($cmd);
$cmd = "mysqladmin -P 3306 -h $dbHost -u $dbUsername -p $dbPw create $newDbName";
getch("$cmd");
exec($cmd);

getch("$cmd");
$cmd = "mysql -P 3306 -h $dbHost -u $dbUsername -p $dbPw $newDbName < e:/dump1.sql";
exec($cmd);

//
//$cmd = "mysqladmin -h $dbHost  –user=$dbUsername –password=$dbPw create $newDbName";
//exec($cmd);
//
//$cmd = "mysql -h $dbHost  –user=$dbUsername –password=$dbPw $newDbName < $file";
//exec($cmd);
