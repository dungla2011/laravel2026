<?php

require_once __DIR__.'/../index.php';

use Medoo\Medoo;

// Connect the database.
//$db = new Medoo([
//    'type' => 'mysql',
//    'host' => 'localhost',
//    'database' => 'shoping01',
//    'username' => 'root',
//    'password' => ''
//]);

$con = \Illuminate\Support\Facades\DB::getPdo();

if ($con instanceof PDO);
$stm = $con->query('SHOW COLUMNS FROM users');
$stm->setFetchMode(PDO::FETCH_ASSOC);
$rows = $stm->fetchAll();

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($rows);
//echo "</pre>";

$mm = [];
foreach ($rows as $one) {
    $mm[$one['Field']] = $one['Type'];
}
echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($mm);
echo '</pre>';

$db = new Medoo([
    'pdo' => $con,
    'type' => env('DB_CONNECTION'),
]);

$data = $db->get('users', [
    'username',
    'email',
], [
    'id' => 1,
]);

echo "<br/>\n ret = ";
echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($data);
echo '</pre>';
