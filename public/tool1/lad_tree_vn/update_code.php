<?php

$file = 'E:\Projects\laravel2022-01\laravel01\public\tool1\lad_tree_vn\clsTreeTopDown_src_glx.001.js';
$fileE = 'E:\Projects\laravel2022-01\laravel01\public\tool1\lad_tree_vn\tree_glx01.js';

$ftime = filemtime($file);
while (1) {
    usleep(100000);
    echo "\n ...".time();
    //    if($ftime != filemtime($file))

    echo "\n Change file ...".time();
    $ftime = filemtime($file);
    exec('javascript-obfuscator E:\Projects\laravel2022-01\laravel01\public\tool1\lad_tree_vn\clsTreeTopDown_src_glx.001.js -o E:\Projects\laravel2022-01\laravel01\public\tool1\lad_tree_vn\tree_glx01.js');

}
