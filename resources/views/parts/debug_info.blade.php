<?php
if(!isDebugViewable()){
    return;
}
?>

<button class="debug_button" style='' onclick='showHideInfoObjDebug()'> ? </button>
<script>
    function showHideInfoObjDebug() {
        $("#debug_info_admin").toggle();
    }
</script>

<div id='debug_info_admin' style=''>
    <?php
    if(!$mMetaAll)
    {
        echo "<br/>\n Not found MetaAll1";
    }else
    {

        $objMeta = end($mMetaAll);
        if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);
        $db = getDatabaseName_();

        $dbAdminLink = \App\Models\SiteMng::getDbAdminUrl();

        $linkDB = "#";
        if(isDebugIp()){
            $linkDB = "https://mytree.vn/dbadmin123/index.php?route=/sql&server=1&db=$db&table=$objMeta->table_name_model&pos=0";
            if($dbAdminLink)
                $linkDB = $dbAdminLink . "/index.php?route=/sql&server=1&db=$db&table=$objMeta->table_name_model&pos=0";
        }
        $linkDev = 'https://docs.google.com/document/d/1VPn8YinjUZqwMb3pHFpcAPj-peoK8bvacmZzyPaQNwc/edit';

        echo "<a target='_blank' href='/admin/db-permission?table=$objMeta->table_name_model'>MetaInfo</a> | <a href='$linkDB' target='_blank'>DB</a> ";

        echo "\n | <a href='$linkDev' target='_blank'> DEV </a>";


        echo "<br/>\n LastQr: "  ;
        echo "<pre>";
        print_r(clsDebugHelper::$lastQuery);
        echo "</pre>";
        echo "<br/>\n Data1:";

        dump(request('id'));
        //Data là cho edit
        if(isset($data))
        {

            $idx = request('id');
            if(isUUidStr($idx))
                $idx = $data::where("ide__", $idx)->first()->id;
            elseif(!is_numeric($idx))
                $idx = qqgetIdFromRand_($idx);

            $x11 = $data->find($idx);

            if($x11)
                dump(get_class($data) , $x11->toArray());
            $x11 = $data;
            if($x11){
                $x11 = $x11->toArray();
                foreach ($x11 AS $k=>$v){
                    if(is_array($v))
                        continue;
//                    $v = strip_tags($v);
                    $len = strlen($v);
                    if($len > 200)
                        $v = substr($v, 0, 200)."... $len character";
                    $x11[$k] = $v;
                }
                dump($x11);
            }
        }

        echo "<br/>\n AfterIndexData";
        dump($objMeta::$preDataAfterIndex);
//        dump($dataView->items());

        echo "<br/>\n Data2:";
        if(isset($dataView)){
            foreach ($dataView as $x1){
                $x11 = $x1->find($x1->id);
                if($x11){
                $x11 = $x11->toArray();
//                if(0)
                foreach ($x11 AS $k=>$v){
                    if(is_array($v) || !$v)
                        continue;
                    $v = strip_tags($v);
                    $len = strlen($v);
                    if($len > 200)
                        $v = substr($v, 0, 200)."... $len character";
                    $x11[$k] = $v;
                }
                dump($x11);
                }
            }
        }

    }

    ?>
</div>
