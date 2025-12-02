<style>


    table.glx03 {
        width: 100%;
        border: 1px MintCream solid;
        border-collapse: collapse;
        margin: 0px 0px 0px 0px;
    }

    table.glx03 td {
        border: 1px solid #ccc;
        padding: 3px 5px 3px 5px;;
    }

    table.glx03 th {
        border: 1px MintCream solid;
        padding: 3px 5px 3px 5px;;
    }

    table.glx03 tr:nth-child(even) {
        background: #f6f6f6
    }

    table.glx03 tr:nth-child(odd) {
        background: beige
    }

</style>


<?php

use App\Models\EventInfo;
require "/var/www/html/public/index.php";


echo "\n <A HREF='/'>TRANG CHỦ</A>";
echo "\n <h1> CÁC MÃ CODE THAY THẾ TRONG SMS, EMAIL</h1>";

echo EventInfo::getListCodeReplace();
