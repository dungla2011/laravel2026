<?php
if(!isSupperAdmin_()){
    die("Not admin!");
}
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("p").click(function(){
    $(this).hide();
  });
});
</script>
    <style>
        body {
            font-family: Tahoma;
            font-size: smaller;
            color: white;
            background-color: #585858;
        }

        a{
            color: yellow;
        }


        table.glx01 { border: 1px #ccc solid; border-collapse: collapse; margin: 0px 0px 0px 0px; }
        table.glx01 td {  border: 1px #ccc solid;      padding: 3px 5px 3px 5px;     ;}
        table.glx01 th {  border: 1px #ccc solid;    padding: 3px 5px 3px 5px;    ;}
        table.glx01 tr:nth-child(even) { }
        table.glx01 tr:nth-child(odd) { }

        table.glx03 { border: 1px MintCream solid; border-collapse: collapse; margin: 0px 0px 0px 0px; }
        table.glx03 td {  border: 1px MintCream solid;      padding: 3px 5px 3px 5px;     ;}
        table.glx03 th {  border: 1px MintCream solid;    padding: 3px 5px 3px 5px;     ;}
        table.glx03 tr:nth-child(even) {background:   #f6f6f6 }
        table.glx03 tr:nth-child(odd) {background:  beige }


        table.glx04 { box-shadow: 10px; border-radius: 10px; border: 2px #ccc solid; border-collapse: collapse; margin: 0px 0px 0px 0px; }
        table.glx04 td {  border: 1px MintCream solid;      padding: 5px 12px 5px 12px;     ;}
        table.glx04 th {  border: 1px MintCream solid;    padding: 5px 12px 5px 12px;     ;}
        table.glx04 tr:nth-child(even) {background:   #f6fbfb }
        table.glx04 tr:nth-child(odd) {background:  beige }


    </style>
</head>
<body>

<div class="content-wrapper">
    <div class="container">
        <h2 data-code-pos="ppp1676864326156"><a href="/admin"> Admin</a> |
            Auto insert permision
        </h2>

        Xem bảng permissions để thấy các quyền được tự động insert vào
        <br>
        Hàm checkValidUriInsertRoute: cho phép các url /admin /member /api /task được Auto insert route!
        <hr>
        <br>
        delete_all=1 : to delete all
        <br>
        <?php

        if(request('delete_all'))
            \App\Components\Helper1::deleteAllPermissionUrlRoute();

        \App\Components\Helper1::createPermissionAutomatic();

        use Illuminate\Support\Facades\Route;
        $routeCollection = Route::getRoutes();


        $cc = 0;
        echo "<table class='glx01'>";
        echo "<tr>";
        echo "<td width=''><h4>No</h4></td>";
        echo "<td width=''><h4>HTTP Method</h4></td>";
        echo "<td width=''><h4>uri</h4></td>";
        echo "<td width=''><h4>getName</h4></td>";
        echo "<td width=''><h4>Corresponding Action</h4></td>";
        echo "<td width=''><h4>MidleWare</h4></td>";
        echo "<td width=''><h4>Prefix</h4></td>";
        echo "<td width=''><h4>Route desc</h4></td>";
        echo "<td width=''><h4>Route Group desc</h4></td>";

        echo "</tr>";
        foreach ($routeCollection as $value) {
            if ($value instanceof \Illuminate\Routing\Route) ;
            if ($value->uri() != 'login') {
                //            continue;
            }

            if (!\App\Components\Helper1::checkValidUriInsertRoute($value))
                continue;

            $cc++;
            echo "<tr>";
            $uri0 = $value->uri();
            $uri = explode("/{", $uri0) [0];

            echo "<td>  $cc </td>";
            echo "<td> " . $value->methods()[0] . "</td>";
            echo "<td> <a href='/$uri' target='_blank'>$uri0 </a>" . "</td>";
            echo "<td>" . $value->getName() . "</td>";
            echo "<td>" . $value->getActionName() . "</td>";
            echo "<td>";
            foreach ($value->middleware() AS $md) {
                echo " $md <br/>\n";
            }
            echo "</td>";
            echo "<td>" . $value->getPrefix() . "</td>";

            if (isset($value->route_desc_))
                echo "<td>" . $value->route_desc_ . "</td>";
            else
                echo "<td></td>";
            if (isset($value->route_group_desc_))
                echo "<td>" . $value->route_group_desc_ . "</td>";
            else
                echo "<td></td>";

            echo "</tr>";

        }
        echo "</table>";

        ?>
    </div>
</div>

</body>
</html>
