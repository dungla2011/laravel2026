<?php

$mm = [
    'href="plugins' => 'href="/admin/plugins',
    'src="plugins/' => 'src="/admin/plugins/',
    'href="dist' => 'href="/admin/dist',
    'src="dist' => 'src="/admin/dist',
    'href="pages/' => 'href="/admin/pages/',
];
$indexFile = __DIR__.'/index.html';
$cont = @file_get_contents($indexFile);
if (! strstr($cont, '<title>AdminLTE')) {
    exit('Not valid file AdminLTE?');
}
foreach ($mm as $str => $replace) {
    $cont = str_replace($str, $replace, $cont);
}
file_put_contents($indexFile, $cont);
