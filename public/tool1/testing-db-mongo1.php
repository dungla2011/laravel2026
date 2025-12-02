<?php

require_once __DIR__.'/../index.php';

$obj = new \LadLib\Common\Database\mongoDb();

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r(\LadLib\Common\Database\MongoDbConnection::getConnection());
//echo "</pre>";

$obj = new \App\Models\demoMg();
$ctl = $obj->getCtrlDb();
$ctl->deleteMany([]);
$obj->name = '123';
$obj->title = 'abc';
$idNew = $obj->insert();

echo "<br/>\nID OK: $idNew";

return;
$obj::$_dbName = 'test123';
$obj::$_tableName = 'test1';
$ret = $obj->insert(['a' => 1, 'b' => time()]);
echo "<br/>\n RET = $ret";

$obj->getAll();

return;

$m1 = \LadLib\Common\Database\MongoDbConnection::getListCollection();

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($m1);
echo '</pre>';

echo "<br/>\nxxx";

return;

$obj = new \App\Models\TestMongoBase();

$obj->getOneWhere(['_id' => 3]);

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($obj);
echo '</pre>';

echo "<br/>\n DBname: ".$obj->getDbName();
echo "<br/>\n tablname: ".$obj->getTableName();

$obj->reset();

//$obj->test1 = '2';
//$obj->test2 = '1';
//$obj->insert();
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($obj);
//echo "</pre>";

$m1 = $obj->getArrayWhere();

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($m1);
echo '</pre>';
