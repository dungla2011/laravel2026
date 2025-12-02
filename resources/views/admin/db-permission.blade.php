<?php
/**
 * Lấy toàn bộ Meta của 1 bảng ra, và cho phép edit các meta data đó
 */
use LadLib\Common\Database\MetaTableCommon;
use \Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
?>
@extends("layouts.adm")

@section("title")
    DbPermission
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">

    <style>
        input.input_value_to_post[data-meta-field=name] {
            width: 300px!important;
        }


    </style>
@endsection

@section('js')
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>
    <script src="/admins/meta-data-table/meta-data-table.js"></script>
@endsection

@section("content")

    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">
                            <a href="<?php echo request()->url() ?>">DbPermission</a>

                        </h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" data-code-pos="ppp1734677889157" style="font-family: 'Courier New'">
                                <?php
                                $meta = new \App\Models\ModelMetaInfo();
                                $conName =  $meta->getConnectionName() ;
                                if(!$conName)
                                    $conName = "default__con";
                                echo " " . $conName . " | " . $meta->getDatabaseName();
                                ?>
                            </li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <?php

                $mRole = \App\Models\Role::all()->toArray();

                $strHintGid = "\n- Danh sách RoleId hiện có:\n";
                foreach ($mRole AS $role)
                    $strHintGid .= "RoleID = " . $role['id']." | ".$role['name']." | ".$role['display_name']."\n";
                $strHintGid .= "\n(Nhập các RoleId được cấp quyền này, các RoleID cách nhau bởi dấu Phẩy)";

                //
                $connectionDb = \Illuminate\Support\Facades\DB::getPdo();
                if (!$connectionDb)
                    die("NOT CON1");

                //zzzzzzzz
//                $mTable = \LadLib\Common\Database\DbHelper::getAllTableName($connectionDb, env("DB_DATABASE"));
                $mTable = \LadLib\Laravel\Database\DbHelperLaravel::getAllTableName();

                sort($mTable);
                $tableModelSelecting = request('table');
                if (!$tableModelSelecting)
                    $tableModelSelecting = 'users';

                $urlCurrent = request()->getPathInfo();


                $metaModelOfTable = \LadLib\Common\Database\MetaTableCommon::getMetaObjFromTableName($tableModelSelecting);



                $clsMeta = $urlApi = null;
                if($metaModelOfTable){
                    $urlApi = $metaModelOfTable?->getApiUrl('admin', 0);
                    $clsMeta = get_class($metaModelOfTable);
                }
                ?>
                <div class="row mb-2">
                    <div class="col-md-6">
                        <?php
                        echo "Chọn bảng <select id='select_table_model' class='form-control form-control-sm form-inline select_table_permission' onchange='window.location.href=this.value'>";
                        echo "<option value='$urlCurrent'> - Select table - </option>";
                        foreach ($mTable AS $tblName) {

                            if (!\LadLib\Common\Database\MetaTableCommon::getModelFromTableName($tblName)) {
                                continue;
                            }

                            $select = '';
                            if ($tableModelSelecting == $tblName)
                                $select = 'selected';
                            echo "<option $select value='$urlCurrent?table=$tblName'> $tblName </option>";
                        }
                        echo "</select> ";
                        echo "<br/>\n";
                        echo "<span class='span_api_text'> Model-API : " . $urlApi . " (Class: " . $clsMeta . ") ";

                        echo "\n <a target='_blank' href='/tool/common/get-api-info.php?table=$tableModelSelecting'> GET API INFO </a> | ";

                        echo "\n <a target='_blank' href='/tool/common/language_edit_fields.php?table=$tableModelSelecting'> Language Fields </a> ";


                        echo "\n</span>";


                        ?>
                    </div>


                    <div class="col-md-6">
                        <span class="btn btn-info float-right" id="save_all_form_button"
                              style="margin-left: 10px; position: fixed; top: 160px; right: 20px">SAVE</span>

                        <input style='font-size: smaller; width: 80px' class='' id='replace_str' value='' type='text'>
                        <input style='font-size: smaller; width: 80px' class='' id='replace_by' value='' type='text'>
                        <button style='font-size: smaller; width: auto ' class='float-right1' id="replace_all_input" > Replace </button>

                        <?php


                        echo "<input style='font-size: smaller' class='float-right' id='copyFromTable' value='order_infos' type='text'>";
                        echo "<button style='font-size: smaller' class='float-right' onclick='copyFromTableToTable(\"$tableModelSelecting\")'> Copy From Table</button";
                        ?>
                        <div style="clear: both"></div>
                    </div>
                </div>
                <?php


                //Lấy ra API show lên
                if (!$metaModelOfTable) {
                    echo "<br/>\n May be not found MetaClass of $tableModelSelecting";
                    goto _NEXT;
                }

                /////////////////////
                $objMetaCommon = new MetaTableCommon();
                $mmFieldOfTable = [];
                if (!$tableModelSelecting)
                    goto _NEXT;

                //zzzzzzzz
                //$mmFieldOfTable = \LadLib\Common\Database\DbHelper::getTableColumns($connectionDb, $tableModelSelecting);

                $mmFieldOfTable = \LadLib\Laravel\Database\DbHelperLaravel::getTableColumns(null, $tableModelSelecting);

                    //Lấy tên bảng meta, để truyền vào API update Meta bên dưới
//                $tableNameMetaInfo = $tableModelSelecting . "_meta_info";
                //                $objMetaCommon->setTableName($tableNameMetaInfo);


                //Phải delete ở đây, để nếu có 1 trường mới ko bị lỗi load meta cũ
                \LadLib\Common\Database\MetaOfTableInDb::deleteClearCacheMetaApi($tableModelSelecting);

                //Lấy ra All Meta data của Bảng đã chọn
                //$objMetaOfTable = new \LadLib\Common\Database\MetaOfTableInDb();

                $objMetaOfTable = \LadLib\Common\Database\MetaTableCommon::getMetaObjFromTableName($tableModelSelecting);
                $objMetaOfTable->setDbInfoGetMeta($tableModelSelecting, $connectionDb);
                $mmAllMetaDb = $objMetaOfTable->getMetaDataApi();


//                clang::exportFieldDescriptionToJson($mmAllMetaDb, $tableModelSelecting, ['en']);


                ?>
                <form action="" id="form_post_data" method="post">
                    <div class="divContainer" id="div_container_meta"
                         data-api-url="/api/common/save-meta-data2?table_name=<?php echo $tableModelSelecting ?>">
                        <div class="divTable2" style=";border: 1px solid #000;">
                            <div data-code-pos="ppp1668292971271" class="divTable2Body">
                                <div class="divTable2Row">
                                    <?php
                                    //for($j = 0; $j < 4; $j++)
                                    //Show hàng Title TH
                                    foreach ($objMetaCommon AS $field=>$val) {

                                    if (!$objMetaCommon->isShowIndexField($field))
                                        continue;

                                    if(is_string($val) || is_numeric($val))
                                        $val = htmlentities($val);

                                    ?>
                                    <div data-code-pos="ppp1678890691807" class="divTable2Cell cellHeader" title="<?php echo $field ."\n\n - ". $objMetaCommon->getFullDescField($field) . "\n $strHintGid";?>">
                                        <?php
                                        //echo $field;
                                        echo ucfirst(str_replace("_", ' ', $objMetaCommon->getDescOfField($field)));
                                        ?>
                                    </div>
                                    <?php
                                    }
                                    ?>
                                </div>
                                <?php
                                $row = 0;
                                $t1 = microtime(1);

                                //Lấy ra từng Field của bảng, và show từng Hàng Meta tương ứng
                                //foreach ( $mmFieldOfTable AS $fieldInTable)
                                foreach ( $mmAllMetaDb AS $fieldInTable => $objMetaOfFieldDb)
                                {
                                    if(!isset($mmAllMetaDb[$fieldInTable])){
//                                        continue;
                                    }

//                                $objMetaOfFieldDb = $mmAllMetaDb[$fieldInTable];

                                if ($objMetaOfFieldDb instanceof \LadLib\Common\Database\MetaOfTableInDb);

                                //for($row = 0; $row < 5; $row++){
                                ?>
                                <div data-code-pos="ppp1667528951220" class="divTable2Row">
                                    <?php
                                    //for($col = 0; $col < 4; $col++){
                                    $col = 0;
                                    //Mỗi fieldIntable, show ra hết các giá trị Meta thành 1 hàng
                                    foreach ($objMetaCommon AS $fieldMeta=>$tmp2){

                                    //Ẩn đi các CELL mà trường Meta không show index
                                    if (!$objMetaCommon->isShowIndexField($fieldMeta))
                                        continue;

                                    $disabledOK = null;

                                    $styleEx = '';

                                    ?>
                                    <div data-code-pos="ppp1667528959601" autocomplete="off" data-tablerow="<?php echo $row ?>" data-tablecol="<?php echo $col ?>"
                                         class="divTable2Cell">
                                        <?php
                                        $padHiden = null;
                                        $val = null;

                                        //Giá trị Edit:
                                        $val = $objMetaOfFieldDb->$fieldMeta;
                                        if(is_string($val) || is_numeric($val))
                                            $val = htmlentities($val);
                                        else
                                            $val = '';


                                        $valHardCode = $objMetaOfTable->getHardCodeMetaObj($fieldInTable)?->$fieldMeta;
                                        if(!$val && $valHardCode)
                                            $val = $valHardCode;

                                        //Nếu field là hardcode (ko cho phép user sửa, như join_api) thì ko lấy từ db, mà lấy từ Class_Meta của Class
                                        if (MetaTableCommon::isHardCodeMetaField($fieldMeta)) {
                                            $val = $fieldInTable;

                                            $objMetaOfTable = \LadLib\Common\Database\MetaTableCommon::getMetaObjFromTableName($tableModelSelecting);
                                            //Val sẽ chui vào, lấy từ trong hardcode ở ClassMeta của đối tượng
                                            $val = $objMetaOfTable->getHardCodeMetaObj($fieldInTable)?->$fieldMeta;
                                            //vd $val =  $objMetaOfTable->getHardCodeMetaObj('user_id')?->dataType;
                                            $disabledOK = 'disabled';
                                            $styleEx = ';background-color: #eee;';
                                        }
                                        $displayInput = null;
                                        $id = $objMetaOfFieldDb->id;
//                                        if ($fieldMeta == '_id') {
//                                            echo "<span data-code-pos='ppp16661474481011' class='spanField'> $objMetaOfFieldDb->_id </span>";
//                                            $padHiden = 'hidden';
//                                            $displayInput = ";display: none;";
//                                        } else
                                            if ($fieldMeta == 'sname') {
                                            $displayInput = ";display: none;";
                                            echo "<span data-code-pos='ppp16661474428011' class='spanField'> $objMetaOfFieldDb->sname </span>";
                                            $padHiden = 'hidden';
                                        } elseif ($fieldMeta == 'field') {
                                            $displayInput = ";display: none;";
                                            $fieldInTable1 = $fieldInTable;

                                            //Kiểm tra xem có là HÀM ko:
                                            if($fieldInTable[0] == '_'){
                                                $fieldInTable1 = $fieldInTable.'()';
                                            }
                                            echo "<span data-code-pos='ppp16661474438011' class='spanField'>$fieldInTable1</span>";
                                            $padHiden = 'hidden';
                                            $val = $fieldInTable;
                                        }

                                        if (!$objMetaCommon->isEditableField($fieldMeta)) {
                                            $disabledOK = 'readonly';
                                        }

                                        if ($objMetaCommon->isStatusField($fieldMeta)) {
                                            if ($val == 0)
                                                $clsOnOff = "fa-toggle-off";
                                            else
                                                $clsOnOff = "fa-toggle-on";

                                            echo " <div class='text-center '> <i title='" . $objMetaCommon->getFullDescField($fieldMeta, $fieldInTable) . "' data-name='" . $fieldInTable . "[$fieldMeta]" . "' class='fa $clsOnOff change_status_item'></i> </div>";
                                            $displayInput = ";display: none;";
                                        } elseif ($funcMap = $objMetaCommon->isSelectField($fieldMeta)) {

                                            if ($disabledOK) {
                                                if (is_array($funcMap)) {
                                                    if ($val) {



                                                        echo "<span data-code-pos='ppp16666147448011' data-join-func='' class='spanField'>" . ($funcMap[$val] ?? ' not_found ') . "</span>";
                                                    }
                                                }
                                                //echo "ABC";
                                            } else
                                                if (is_array($funcMap)) {
                                                    $displayInput = ";display: none;";
                                                    $padHiden = 'hidden';
                                                    $padSelect = '';
                                                    $slOption = "<select $disabledOK name='" . $fieldInTable . "[$fieldMeta]" . "' dbp0e5455 title='field = $field' class='sl_option td_field_sfield td_field_$field' id='to_update_$id-$field' >";
                                                    $slOption .= "<option value=''> --- </option>";
                                                    $slOption .= "<option value='null' $padSelect> - not set - </option>";
                                                    $slVal = '';
                                                    foreach ($funcMap as $k => $v) {
                                                        $padSelect = "";
                                                        //sẽ là  ==, hay  === ?
                                                        //2021-08-25: đổi thành ==, cho ncbd layout
                                                        if ($val == $k) {
                                                            $padSelect = " selected ";
                                                            $slVal = $v;
                                                        }
                                                        $slOption .= "<option class='sl_option_item' $padSelect value='$k' dbp1 > $v </option>";
                                                    }
                                                    $slOption .= "</select>";
                                                    echo $slOption;
                                                }
                                        }
                                        ?>
                                        <input data-lpignore="true" <?php echo $disabledOK?>
                                               style="<?php echo $displayInput . $styleEx ?>" class="td_input input_value_to_post"
                                               placeholder="<?php echo $fieldMeta ?>"
                                               <?php echo $padHiden ?> name="<?php echo $fieldInTable ?>[<?php echo $fieldMeta ?>]"
                                               data-field="<?php echo $fieldInTable ?>"
                                               data-meta-field="<?php echo $fieldMeta ?>"
                                               title="<?php echo "VALUE = $val \n"; if(!$disabledOK) echo "- Quyền '$fieldMeta', trên trường '$fieldInTable'\n- Các RoleId được cấp quyền: $val" . "\n\n- Mô tả: ". $objMetaCommon->getFullDescField($fieldMeta, $fieldInTable) . $strHintGid ?>" type="text"
                                               value="<?php echo $val; ?>">
                                    </div>
                                    <?php
                                    $col++;
                                    }
                                    ?>
                                </div>
                                <?php
                                $row++;
                                }
                                $t2 = microtime(1);
                                //                                echo "<br/>\n DTIME = " . ($t2 - $t1);
                                ?>
                            </div>
                        </div>
                    </div>
                </form>

                <?php

                _NEXT:
                $modelName = \LadLib\Common\Database\MetaTableCommon::getModelFromTableName($tableModelSelecting);
                if($tableModelSelecting[0] != '_' ){
                    $ret = \LadLib\Laravel\Database\DbHelperLaravel::getRelationshipsBaseModel($modelName);
                    echo "<br>\n Relations: $urlApi";



                }
                ?>
            </div>
        </section>
        <!-- /.content -->
    </div>

    <script>

        window.addEventListener('load', function () {
            //khi click vào replace_all_input
            $('#replace_all_input').click(function () {
                let replace_str = $('#replace_str').val();
                let replace_by = $('#replace_by').val();
                console.log("replace_str = ", replace_str, " replace_by = ", replace_by);
                if (replace_str && replace_by) {
                    $('input.input_value_to_post').each(function () {
                        let val = $(this).val();
                        if (val.includes(replace_str)) {
                            val = val.replace(replace_str, replace_by);
                            $(this).val(val);
                        }
                    });
                }
            });

        });



        function copyFromTableToTable(toTable) {
            let user_token = jctool.getCookie('_tglx863516839');

            let fromTbl = $("#copyFromTable").val()
            let url = '/api/common/copyFromTable?fromTbl=' + fromTbl;
            console.log(" urlx = ", url);
            $.ajax({
                url: url,
                type: 'GET',
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('Authorization', 'Bearer ' + user_token);
                },
                data: {},
                success: function (data, status) {
                    let pl = data.payload
                    console.log("Data: ", data, " \npl: ", pl);
                    console.log(" typeof pl " , typeof pl);
                    for(let key in pl){
                        // console.log(" V = " ,key, pl[key]);
                        $('input[name="' + key + '"]').attr('value', pl[key])
                        $('input[name="' + key + '"]').prop('value', pl[key])
                        $('input[name="' + key + '"]').val(pl[key])
                    }
                },
                error: function () {
                    console.log(" Eror....");
                    alert("Error: no permission?");

                },
            });

        }
    </script>

@endsection
