<?php

//require_once __DIR__.'/public/index.php';

//
//$mfile = [
//    'E:\Projects\galaxy2018.pm33.net\train\tree-view\lad-tree2022\clsTreeJs-v1.css'=>'E:\Projects\laravel2022-01\laravel01\public\backup_lib_remote\clsTreeJs-v1.css',
//    'E:\Projects\galaxy2018.pm33.net\train\tree-view\lad-tree2022\clsTreeJs-v2.js'=>'E:\Projects\laravel2022-01\laravel01\public\backup_lib_remote\clsTreeJs-v2.js',
//];
//
//foreach ($mfile AS $src=>$dest){
//    $cont1 = file_get_contents($src);
//    $cont2 = '';
//    if(file_exists($dest))
//        $cont2 = file_get_contents($dest);
//
//    if($cont1 != $cont2){
//        if(file_exists($dest))
//            unlink($dest);
//        echo "\n $src=>$dest";
//        copy($src, $dest);
//    }
//    else{
//        echo "\n The same code, not copy: $dest";
//    }
//}
