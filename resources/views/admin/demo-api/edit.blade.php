<?php
//ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
$objData = $data ?? '';
$objMeta = end($mMetaAll);


if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;

?>
@php
    $template = \App\Components\Helper1::isAdminModule(request()) ? "layouts.adm" : "layouts.member";
@endphp

@extends($template)
<?php
//ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
?>

@section('title_nav_bar')
    <span data-code-pos="ppp1734748424846" style="">
    <?php

        if(isDebugIp()){
//
//            dump($objData);
//
//            die();
        }

    $idx = request('id');
    //Breakum
    $module = \App\Components\Helper1::getModuleCurrentName(request());
    $linkIndex = '';
    if($urlThisIndex = $objMeta->getAdminUrlWeb($module) ){
        $titleModule = $objMeta::$titleMeta;
        if(!$titleModule){
            $tmp = str_replace("\\", "/", get_class($objData));
            $titleModule = basename($tmp);
        }
        $linkIndex = "<a data-pos='489757593745'  href='$urlThisIndex'> " .  $titleModule .  " </a>
        <i class='fa fa-fw fa-angle-right'> </i>  ";
    }
    $linkItem = '';
    if(($objData ?? '') && $objData instanceof App\Models\ModelGlxBase){
        $nameTitle = $objData?->getNameTitle() ?? '';
        if($nameTitle)
            $linkItem = "<a href='$urlThisIndex/edit/$idx'> $nameTitle </a>  ";
    }
    if(!$linkItem){
        $linkItem = " $idx ";
    }

    if(($objData ?? '') && isset($nameTitle))
        echo $linkIndex . $linkItem;



    ?>
    </span>
@endsection


@section("title")

        Edit: <?php

             if(($objData ?? '') && isset($objData->name))
                 echo $objData->name;
             else
                echo basename(str_replace("\\","/",request()->route()->getController()::class));






        ?>

@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
    <link rel="stylesheet" href="/vendor/lad_tree/clsTreeJs-v1.css?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v1.css'); ?>">
    <link rel="stylesheet"
          href="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.css">
    <link rel="stylesheet" href="/admins/upload_file.css?v=<?php echo filemtime(public_path().'/admins/upload_file.css'); ?>">
    <link rel="stylesheet" href="/admins/img_list.css">
    <link rel="stylesheet" href="/assert/js/date-time-picker/jquery.datetimepicker.css">
    <style>
        .mce-container, .mce-container *, .mce-widget, .mce-widget *, .mce-reset {
            vertical-align: inherit!important;
        }
        .block_btn  button, .block_btn a{
            font-size: small;
        }
        .upload_zone_glx{
            padding: 10px;
        }
        .content-header{
            padding: 10px;
        }
        .open_all_tab, .close_all_tab{
            display: none;
        }


    </style>


@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-0">
                    <div class="col-sm-10">
                        <?php


                        if(isIPDebug()){
                        }

                        $GLOBALS['data_debug_glx'] = $data;

                        if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
                        $objMeta->getTableName();
                        ?>

                        <?php
                        //ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
                        ?>
                        <div class="m-0" data-code-pos="ppp1676081871796" style="font-size: large; display: none">
                            <?php
                            if(isAdminACP_())
                            if(getCurrentActionMethod() == 'edit' && $objData && isset($objData->id)){

                                $metaLog = \App\Models\ChangeLog::getMetaObj();
                                $keyS = $metaLog->getSearchKeyField('id_row');
                                $sTableName = $metaLog->getSearchKeyField('table_name');


//                                $keyS = "123";
                            ?>

                            <a title="Xem lịch sử thanh đổi Bản ghi này1" target="_blank" href="/admin/change-log?{{$keyS}}={{$idx}}&seoby_s8=eq&{{$sTableName}}=<?php echo $objData->getTable() ?>">
                            <i  class="fa fa-history"></i>
                            </a>
                                <?php
                                }
                                ?>

                        <?php

                        if($objData instanceof \App\Models\FileUpload);
//                        echo $objData->getBreakumPathHtml();

//                            echo $objData->getStringLinkBreadcrumb();
                            if(0)
                            if($objMeta::$folderParentClass){
                                $objFolder = $objMeta::$folderParentClass;
                                $id0 = request('id');
                                if(!is_numeric($id0))
                                    $id0 = qqgetIdFromRand_($id0);
                                echo $objMeta->getPathHtml($id0, 'edit', '::');
                            }

                            if($objData && isset($objData->name))
                            echo " " . htmlentities($objData->name);
                        ?>

                        </div>
                    </div><!-- /.col -->
{{--                    <div class="col-sm-2">--}}
{{--                        <ol class="breadcrumb float-sm-right">--}}
{{--                            <li class="breadcrumb-item" data-code-pos='ppp17256353652701'><a href="#">Edit</a></li>--}}
{{--                        </ol>--}}
{{--                    </div><!-- /.col -->--}}
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->
        <?php
        //ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
        ?>
    <?php
    $objPr = new \App\Components\clsParamRequestEx();
    $objPr->setParamsEx(\request());

    if(isAdminCookie()){

    }

    $gid = $objPr->set_gid;

    if($objData instanceof Illuminate\Http\JsonResponse){
        bl("Lỗi not valid object");
        echo "<pre>";
        print_r($objData);
        echo "</pre>";
        return;
    }


    ?>
    <!-- Main content -->
        <section class="content">
            <div class="container-fluid">

                <?php
//                $objMeta->extraContentIndex1();
                ?>


                <div class="row" data-code-pos='ppp17331995989861'>
                    <div class="col-md-12 menu_action_edit" style="">

                        <div data-code-pos='ppp16922760733541'  class="block_btn"
                             style="">

                            <?php
                            $objMeta->extraHtmlIncludeEditButtonZone1($objData);
                            ?>

                            <a title="Ghi lại - Save" data-code-pos="ppp1677328964565" id="save-one-data" class="btn btn-primary  m-2">
                                <i class="fa fa-save"></i>
                            </a>


                            <a href="<?php echo $objMeta->getAdminUrlWeb($objPr->module) . "/create" ?>"
                               target="_blank" title="Tạo mới" data-code-pos="ppp1628964565" class="btn btn-outline-info  m-2">
                                <i class="fa fa-plus-circle"></i>
                            </a>



                            <?php
                            if($objData && ($objData->id ?? '')){
                            ?>
                            <a href="<?php echo $objMeta->getAdminUrlWeb($objPr->module)."/create?clone_from=$idx" ?>"
                               class="ctrl_btn btn btn-outline-primary  m-2" target="_blank"
                               title="Nhân bản mục này"> <i class="fa fa-copy"></i></a>

                            <br>
                            <?php
                            }
                            ?>

                        <?php
                        if((method_exists($objData, 'getLinkPublic') && $linkP = $objData->getLinkPublic())){
                        ?>
                            <a id="public_view_item" title="Xem xuất bản" class="ctrl_btn btn btn-outline-primary  m-2"
                               data-code-pos='ppp16899486143061' href="<?php echo $linkP ?>" target="_blank">
                            <i class="fa fa-share-alt"></i>
                            </a>

                        <?php
                        }
                        ?>
                            <?php
                            if(isAdminACP_())
                            if(getCurrentActionMethod() == 'edit' && $objData && isset($objData->id)){
                                $metaLog = \App\Models\ChangeLog::getMetaObj();
                                $keyS = $metaLog->getSearchKeyField('id_row');
                                $sTableName = $metaLog->getSearchKeyField('table_name');


//                                $keyS = "123";
                                ?>

                            <a title="Xem lịch sử thanh đổi Bản ghi này1" class="btn btn-outline-primary  m-2 ctrl_btn" target="_blank" href="/admin/change-log?{{$keyS}}={{$idx}}&seoby_s8=eq&{{$sTableName}}=<?php echo $objData->getTable() ?>">
                            <i  class="fa fa-history"></i>
                        </a>
                            <?php
                            }
                            ?>

                        <a title="Xóa vào thùng rác" id="delete_one_item"
                           data-api="<?php echo $objMeta->getApiUrl( \App\Components\Helper1::getModuleCurrentName(request()) )  ?>" data-id="<?php echo request('id') ?>"
                           class="btn btn-outline-primary  m-2 ctrl_btn" data-code-pos='ppp16896143061'>
                        <i class="fa fa-trash "></i>
                        </a>


                        <a  title="Trở lại" data-code-pos="ppp1677328968517" href="<?php
                        echo $objMeta->getAdminUrlWeb($objPr->module)
                        ?>" class="btn btn-outline-primary m-2 ctrl_btn">
                            <i class="fa fa-backward"></i>
                        </a>
                            <?php
                            $objMeta->extraHtmlIncludeEditButtonZone2($objData, $objMeta);
                            ?>
                    </div>
                    </div>
                </div>
                <?php

                $isCreateNew = 0;
                if(getCurrentActionMethod() == 'create'){
                    $isCreateNew = 1;
                }
                $mFieldShow = $objMeta->getShowGetOneAllowFieldList($objPr->set_gid);

                if(!$mFieldShow){
                    dump("NOT FOUND ANY FIELD TO SHOW Edit!");
                }

                if($objData::find($objData->id)?->deleted_at){
                    bl("This item is deleted you can not edit it!");
                }

                ?>

                <div class="extra_edit0">

                    <?php

                    $objMeta->extraHtmlIncludeEdit0($objData , $mMetaAll);

                    ?>
                </div>

                <div class="row">
                    <div data-code-pos="ppp1665495267978" class="col-md-12">
                        <form data-code-pos="ppp1665495271573" id="form_save_one" data-id="<?php echo $idx ?>">
                            <div class="divTable2Body" id="div_container"
                                 data-api-url-update-one="<?php echo $objMeta->getApiUrl($objPr->module) ?>">
                                <?php
                                //                                dump($objData);


                                $cloneObj = null;
                                if(request('clone_from')){
                                    $cloneObj = $objData::find(request('clone_from'));
                                }

                                $templateObj = null;
                                if(isDebugIp()){
//                                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                    print_r($objMeta);
//                                    echo "</pre>";
//                                    die();
//                                    if($objMeta instanceof \App\Models\FileUpload_Meta);
                                }

                                if($objMeta::getDefaultTemplateId())
                                    $templateObj = $objData::find($objMeta::getDefaultTemplateId());


                                $dataId = null;
                                if(isset($objData->id))
                                    $dataId = $objData->id;
                                $mRandField = null;

                                if($objData->hasField('ide__')){
                                    $dataId = $objData->ide__;
                                }
                                else
                                if($objMeta->isUseRandId()){
                                    $dataId = \App\Components\ClassRandId2::getRandFromId($dataId);
                                    $mRandField = $objMeta->getRandIdListField();
//                                    $objData->id = $dataId;
                                }

                                foreach ($mMetaAll AS $field=>$objMeta){
                                if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
                                if (!$objMeta->isShowGetOne($field, $gid) || ($isCreateNew && !$objMeta->isEditableFieldGetOne($field, $gid)))
                                    continue;
                                $desc = e($objMeta->getDescOfField($field, 0, $objPr->getLanguage()));
                                $displayInput = '';
                                $extraCss = $readOnly = '';

                                //                                $valueField = 'xxx';


//                                if(!method_exists($objData, $field))
                                if($field[0] == '_')
                                    $valueField00 =  $valueField = $objMeta->$field($data, null, $field);
                                else
                                    $valueField00 = $valueField = $objData->$field;



                                if($cloneObj)
                                    $valueField = $cloneObj->$field;

                                if($templateObj){

                                }

                                if($objMeta->isUseRandId() && $field == 'id' && $objData->hasField('ide__')){
                                    $valueField =  $objData->ide__;
                                }
                                else
                                if($mRandField && in_array($field, $mRandField) && $valueField && is_numeric($valueField)){
                                    //Không gán lại ở đây vì ảnh hưởng đến đối tượng
                                    //$objData->$field... =

                                    $valueField = \App\Components\ClassRandId2::getRandFromId($valueField);

                                }

                                if($objMeta->isPassword($field))
                                    $valueField = '';


                                if($isCreateNew){
                                    if(request($field))
                                        $valueField = request($field);
                                    else
                                        if($dfv = $objMeta->setDefaultValue($field))
                                            $valueField = $dfv;
                                }

                                $isRichText = $isTextArea = 0;
                                if($objMeta->isTextAreaField($field))
                                    $isTextArea = 1;

                                $cssRo = '';
                                if($objMeta->isDevAllowEdit($field))
                                {
                                    $cssRo = ' ; background-color: lavender ; ';
                                    if(!isDevEmail()
//                                    && getCurrentUserEmail() != env("AUTO_SET_ADMIN_EMAIL")
                                    ){
                                        $readOnly = ' disabled ';
                                    }
                                }

                                //                                /*if (is_array($valueField)) {
                                //                                    $valueField = implode(",", array_keys($valueField));
                                //                                }*/

                                $isEdit = $objMeta->isEditableFieldGetOne($field, $gid);
                                $padClassDate = '';
                                $divTable2CellRead = 'divTable2CellRead';
                                $hideInput = null;
                                if ($isEdit)
                                    $divTable2CellRead = null;
                                else {
                                    $hideInput = 'display: none';
                                }
                                $joinSpan = null;
                                $multiValue = 0;
                                if ($objMeta->isArrayStringField($field)
                                    || $objMeta->isArrayNumberField($field)) {
                                    $multiValue = 1;
                                }
                                $descField = $objMeta->getDescOfField($field, 0, $objPr->getLanguage());
                                $fullDes = $objMeta->getFullDescField($field);
                                $isShowJoin = '';

                                ?>

                                    <?php
                                    //ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
                                    ?>

                                <?php

                                if($prezone = $objMeta->preZoneFieldEdit($field)){
                                    echo "<div class='divTable2Row for_seperatorHeader  $field' style=' '>
                                        <div style='' class='seperatorHeader bg-light'>
                                           $prezone
                                           <i class='open_all_tab fa fa-arrow-down ml-2' style='color: brown' title='Mở tất cả các Tab khác'></i>

                                        </div>
                                    </div>";
                                }

                                ?>


                                <div data-field="<?php echo $field ?>" data-code-pos="ppp1665495258724" data-id="<?php echo $dataId ?>"
                                     class="divTable2Row <?php echo $field ?> " style="position: relative">
                                    <div class="divTable2Cell divTable2EditCell divTable2EditCellBold"
                                         title="<?php echo $fullDes . " / $field" ?>"
                                    >
                                        <div data-namex="<?php echo $field ?>">
                                        <?php
                                        echo $objMeta->getDescOfField($field,1, $objPr->getLanguage())
                                        ?>
                                        </div>
                                        <div data-namex1="<?php echo $field ?>" class="data_name_x1">
<?php
    echo $objMeta->getExtraDataEditFieldNameX1($field);
?>
                                        </div>
                                        <div data-namex2="<?php echo $field ?>" class="data_name_x2">

                                            <?php

                                                //Nếu edit được thì mới hiển thị nút copy mẫu
                                                if($objMeta->isEditableFieldGetOne($field, $gid))
                                                if($idMau = $objMeta::getDefaultTemplateId())
                                                if($idMau && $idMau != $dataId)
                                                {

                                                $module = \App\Components\Helper1::getModuleCurrentName(request());
                                                $action = getCurrentActionMethod();
                                                $controller = getCurrentController1();

                                                ?>
                                            <div class="mau_copy" style="display: none; position: absolute; top: 1px; right: 1px; z-index: 1000000">
                                                <a href="/{{$module}}/{{$controller}}/edit/{{ $idMau  }}" title="Edit mẫu" target="_blank">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-default btn_copy_temp" data-copy-field="{{$field}}"
                                                        title="Copy giá trị từ Sự kiện mẫu (id = {{$idMau}})"> Copy Mẫu </button>

                                            </div>
                                            <?php
                                            }
                                            ?>
                                        </div>


                                    </div>

                                    <div class='divTable2Cell <?php echo $divTable2CellRead ?> divTable2CellEdit'
                                         data-multi-value="<?php echo $multiValue ?>"
                                         data-edit-able="<?php echo $isEdit ?>"
                                         data-field-div="<?php echo $field ?>"
                                         data-id="<?php echo $dataId ?>"
                                         data-code-pos='ppp1665495279989'
                                    >

                                        <?php

                                            echo $objMeta->getPreHtmlValueEditField($objData, $field);

                                            $padOneValue = '';
                                        $allNodeNameImg = '';
                                        if ($objMeta->isMultiImagesField($field) || $objMeta->isOneImagesField($field)) {
                                            $extraCss = "; color: gray ; font-size: x-small;" ;

                                            $isShowJoin = 1;
                                            if ($objMeta->checkJoinFuncExistAndGetName() && $joinVal = $objMeta->callJoinFunction($objData, $objData->$field, $field)) {
                                                $allNodeNameImg = $joinVal;
//                                                if (is_array($joinVal))
//                                                    foreach ($joinVal AS $imgObj) {
//                                                        $allNodeNameImg .= "<span data-code-pos='ppp1668242218866' class='img_zone' data-img-id='$imgObj->id' ui-state-default'> <img src='$imgObj->link' alt='' title='$imgObj->name'> <span class='one_node_name fa fa-times' title='remove this: $imgObj->id' data-id='$imgObj->id' data-field='$field'>  </span> </span>";
//                                                    }
                                            }
                                        }

                                        if ($objMeta->isEditableFieldGetOne($field, $gid)) {
                                            $isTextArea = 0;


                                            if ($objMeta->isMultiImagesField($field) || $objMeta->isOneImagesField($field)) {

                                                \App\Models\FileUpload_Meta::includeUploadZoneHtmlSample("upload_id_$field");
                                                $isShowJoin = 1;
                                                echo "<div data-code-pos='ppp1495279989' style='display: inline-block'>
                                                <button data-field='$field' type='button' class='browse-img-btn'>Browse Files</button>
                                                </div>";

                                                if(!$objMeta->isOneImagesField($field))
                                                if(substr($field, 0, 5) == 'thumb' || substr($field, 0, 5) == 'image')
                                                    echo "<div class='guide_imgs'>&nbsp Kéo thả để sắp xếp thứ tự, ảnh đầu tiên là ảnh đại diện</div>";

                                                if($objMeta->isOneImagesField($field)){
                                                    $padOneValue = 'single_value';
                                                }

                                                echo "<div data-code-pos='ppp88989'  title='' data-field-img='$field' class='sort_able_imgs all_node_name'>$allNodeNameImg </div> ";

                                                ?>
                                            <?php
                                            } elseif ($objMeta->isTreeMultiSelect($field)) {
                                                $joinVal = $objMeta->callJoinFunction($objData, $objData->$field, $field);

                                                $allNodeName = $joinVal;
//                                                if ($joinVal && is_array($joinVal))
//                                                    foreach ($joinVal AS $nodeId => $nodeName) {
//                                                        $allNodeName .= "<span class='one_node_name' title='remove this: $nodeId' data-id='$nodeId' data-field='$field'> [x] $nodeName</span>";
//                                                    }

                                                $isShowJoin = 1;
                                                echo "
<button class='btn_open_dialog_tree btn btn-info btn-sm' data-api='$objMeta->join_api' data-multi-select='1' data-field='$field' type='button'>Select</button>";
                                                echo "<span data-code-pos='ppp16654889' title='' class='all_node_name' data-field='$field'> $allNodeName </span>";

                                                $displayInput = "; display: none; ";
                                            } elseif ($objMeta->isTreeSelect($field)) {
                                                $isShowJoin = 1;
                                                $fname1 = $objMeta->checkJoinFuncExistAndGetName();
//                                                if($fname1 && !is_callable($fname1))
//                                                    loi("Can not call: $fname1");


                                                $joinVal = $objMeta->callJoinFunction($objData, $objData->$field, $field);

//                                                echo "$fname1 / $field/ <pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                                print_r($joinVal);
//                                                echo "</pre>";
//                                                die();

                                                $padDel = null;
                                                if ($joinVal)
                                                    $padDel = '[x]';

//                                                if (is_array($joinVal)) {
//                                                    $joinVal = array_values($joinVal)[0];
//                                                }

                                                echo "
<button class='btn_open_dialog_tree  btn btn-primary btn-sm' data-api='$objMeta->join_api' data-field='$field' type='button'>Select</button>";
                                                //echo $joinVal;

                                                echo "<span title='' class='all_node_name' data-field='$field'> $joinVal </span>";

//"<span title='remove this: $valueField' class='full_node_path_name' data-id='$valueField' data-field='$field'>  $padDel $joinVal </span>";
                                                $displayInput = "; display: none; ";
                                            } elseif ($objMeta->isSelectField($field)) {

                                                $displayInput = "; display: none; ";
                                                //$mm = call_user_func($joinFunc);
                                                $joinFunc = $objMeta->checkJoinFuncExistAndGetName();

                                                $mm = $objMeta->callJoinFunction();
                                                if($mm) {
                                                    echo "<select $readOnly title='$fullDes / $field' data-code-pos='ppp1665495425433' data-id='$dataId' data-joinfunc='$joinFunc'
class='sl_option $field $objMeta->css_class' style='$objMeta->css;' data-field='$field' >";

                                                    foreach ($mm AS $key => $val) {
                                                        $selected = '';
                                                        if ($objData->$field == $key)
                                                            $selected = 'selected';
                                                        echo "<option value='$key' $selected> $val </option>";
                                                    }
                                                    echo "</select>";
                                                }
                                            } elseif ($objMeta->join_api) {
                                                $displayInput = "; display: none; ";
                                                //if ($objMeta->join_func) {
                                                $joinSpan = null;
                                                if ($fname = $objMeta->checkJoinFuncExistAndGetName()) {

                                                    //if (is_callable($objMeta->join_func))
                                                    if(1)
                                                    {
                                                        //if ($valJoin = call_user_func($objMeta->join_func, $objData, $valueField))
                                                        if ($valJoin = $objMeta->callJoinFunction($objData, $valueField))
                                                        {
                                                            $joinSpan = $valJoin;
                                                            //if ($multiValue)
//                                                                {
//                                                                    if(0)
//                                                                    if (is_array($valJoin))
//                                                                        foreach ($valJoin AS $idJoin => $valJoin) {
//                                                                            $joinSpan .= "<span data-code-pos='ppp1665496102584' data-autocomplete-id='$dataId-$field' class='span_auto_complete' data-item-value='$idJoin' title='Remove this item'>$valJoin [x]</span>";
//                                                                        }
//                                                                }
//                                                                    else
//                                                                        $joinSpan = "<span data-code-pos='ppp1665496105102' data-autocomplete-id='$dataId-$field' class='span_auto_complete' data-item-value='$valueField' title='Remove this item'>$valJoin [x]</span>";
                                                        }
                                                    } else {
                                                        $joinSpan = "Not callable3: $objMeta->join_func()";
                                                    }
                                                }
                                                //Join func đã được ưu tiên ở trên
                                                elseif($objMeta->join_relation_func){
                                                    $funcRl = $objMeta->join_relation_func;
                                                    $tmpValJoin = $objData->$funcRl;
                                                    $valueField = null;

                                                    foreach ($tmpValJoin as $objJoin) {
                                                        $tmpField = $objMeta->join_api_field;
                                                        $valJoin = $objJoin->$tmpField;
                                                        $valueField .="$objJoin->id,";
                                                        $joinSpan .= "<span data-autocomplete-id='$dataId-$field' class='span_auto_complete' data-item-value='$objJoin->id' title='Remove this item'>$valJoin [x]</span>";
                                                    }
                                                }
                                                $isShowJoin = 1;
                                                if(is_string($joinSpan) || is_numeric($joinSpan))
                                                    echo "<div data-code-pos='ppp16654951328' class='search-auto-complete-tbl' style=''>$joinSpan</div>";
                                                else{
                                                    $s1 = json_encode($joinSpan);
                                                    if(!$joinSpan)
                                                        $s1 = '';
                                                    echo "<div data-code-pos='ppp16651430328' class='search-auto-complete-tbl' style=''>$s1</div>";
                                                }

                                                echo "<input data-code-pos='ppp16658885589' style='$hideInput' title='$fullDes / $field' placeholder='Search $descField | $fullDes' data-field='$field'  data-autocomplete-id='$dataId-$field' class='search-auto-complete-tbl' " .
                                                    "data-api-search='$objMeta->join_api' data-api-search-field='$objMeta->join_api_field' type='text' value=''>";

                                            } elseif ($objMeta->isStatusField($field)) {
                                                $displayInput = "; display: none; ";
                                                $cls = null;
                                                if ($isEdit)
                                                    $cls = 'change_status_item';
                                                if ($objData->$field)
                                                    echo "<div data-code-pos='qqq1681816852441' class='status_edit_one'><i data-id='$dataId' data-field='$field' class='fa fa-toggle-on $field $cls'></i></div>";
                                                else
                                                    echo "<div data-code-pos='qqq1681816852441' class='status_edit_one'><i data-id='$dataId' data-field='$field' class='fa fa-toggle-off $field $cls'></i></div>";
                                            } elseif ($objMeta->isRichTextField($field)){
                                                $isRichText = $isTextArea = 1;
                                                $displayInput = "; display: none; ";
                                                echo "<div data-type='rich_text' data-field='$field' style='border: 0px solid red' class='' id='edit_rich_text_$field'>$valueField</div>";
                                            }
                                            elseif($objMeta->isTextAreaField($field)){
                                                $valueFieldOK = htmlspecialchars($valueField);
                                                echo "<textarea data-code-pos='ppp1677658885589' class='$field for_up_down_key input_value_to_post text_area_edit' $readOnly placeholder='Input $desc' name='$field' title='$fullDes / $field' data-type='text_area' data-field='$field' style='$cssRo' id='edit_text_area_$field'>$valueFieldOK</textarea>";
                                                $isTextArea = 1;
                                            }
                                            elseif($objMeta->isDateType($field)){
//                                                $displayInput = "; display: none; ";
                                                $valDateFormat = '';
                                                if($valueField)
                                                    $valueField = $valDateFormat = date("d/m/Y", strtotime($valueField));

                                                $padClassDate = 'edit_date';

                                                //echo "<input data-code-pos='ppp8858885589' type='text' value='$valDateFormat' placeholder='Input $desc' class='for_up_down_key edit_date' data-field='$field'/>";
                                            }
                                            elseif($objMeta->isDateTimeType($field)){
                                                $padClassDate = 'edit_date_time';
//                                                $displayInput = "; display: none; ";
                                                $valDateFormat = '';
                                                if($valueField)
                                                    $valueField = $valDateFormat = date("d/m/Y H:i:s", strtotime($valueField));
                                                //echo "<input data-code-pos='ppp169995589' type='text' value='$valDateFormat' placeholder='Input $desc' class='for_up_down_key edit_date_time' data-field='$field'/>";
                                            }

                                            {

                                                if($field[0] == '_'){
                                                    $displayInput = "; display: none; ";
                                                    if(is_array($valueField) && isset($valueField['value_show'])){
                                                        echo $valueField['value_show'];
                                                        $valueField = $valueField['value_post'];
                                                    }
                                                }

                                                $typeText='text';
                                                if($objMeta->isPassword($field))
                                                    $typeText = 'password';


                                                if($mRandField)
                                                if(!in_array($field, $mRandField))
                                                    //Đoạn này làm hỏng rand?
                                                if ($objMeta->isNumberField($field) || $objMeta->isNumberFieldDb($field))
                                                    $typeText = 'number';

                                                //Không hiển thị input nếu là area, rich
                                                if($isTextArea){
                                                }
                                                else{

                                                    //Nếu chưa showJoin ở trên thì ở đây mới show lần nữa, tránh bị 2 lần
                                                    if(!$isShowJoin)
                                                    {
                                                        if($objMeta->checkJoinFuncExistAndGetName())
                                                        if($valJoin = $objMeta->callJoinFunction($objData, $valueField, $field))
                                                        if(is_numeric($valJoin) || is_string($valJoin))
                                                            $isShowJoin = $valJoin;
                                                    }
                                                    else
                                                        $isShowJoin = '';

                                                    $valueField = htmlentities(($valueField));

                                                    if(!$objMeta->isShowGetOne($field, $gid)){
                                                        $displayInput = "; display: none; ";
                                                    }




//                                                    if(\App\Components\Helper1::getCurrentActionMethod() == 'create'){
//                                                        $valueField = $objMeta->setDefaultValue($field);
//                                                        if(isDebugIp()){
////                                                            if($field == 'parent_id')
////                                                                die("setDefaultValue: $field / $valueField");
//                                                        }
//                                                    }

                                                    $valueField0 = $valueField;
                                                    if($field[0] == '_'){
                                                        $valueField0 = "";
                                                    }

                                                    echo "\n

                                        <input data-lpignore='true' autocomplete='off' $readOnly data-code-pos='ppp1648374970' title='$fullDes / $field'
    class='$padOneValue input_value_to_post for_up_down_key $padClassDate' data-autocomplete-id='$dataId-$field' data-id='$dataId'
    style='$displayInput $extraCss $cssRo $objMeta->css'
    placeholder='Input $desc ($fullDes)' name='$field' data-field='$field' type='$typeText' value='$valueField0'>";
                                                    if($field[0] == '_' && $tmpx = $objMeta->$field($objData, null, null)){
                                                        if(!is_string($tmpx) && !is_numeric($tmpx))
                                                            $tmpx = '';

                                                        ?>
                                        {!!  $tmpx !!}
                                        <?php
                                                    }

//                                                    {{$isShowJoin}} <input data-lpignore='true' autocomplete='off' {{$readOnly}} data-code-pos='ppp1666148374970' title='{{$fullDes}} / {{$field}}'
//    class='input_value_to_post for_up_down_key' data-autocomplete-id='{{$dataId}}-{{$field}}' data-id='{{$dataId}}'
//    style='{{$displayInput}} {{$extraCss}} {{$cssRo}}'
//    placeholder='Input {{$desc}}' name='{{$field}}' data-field='{{$field}}' type='{{$typeText}}' value='{{$valueField}}'>
                                                    ?>



                                            <?php
                                                }
                                            }

                                        } else {

                                            if($objMeta->isRichTextField($field))
                                                $isRichText = 1;

                                            if($objMeta->checkJoinFuncExistAndGetName())
                                            {
                                                if(isIPDebug()){
//                                                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                                    print_r($objData);
//                                                    echo "</pre>";
                                                }
                                                //if ($valJoin = call_user_func($objMeta->join_func, $objData, $valueField, $field))
                                                if ($valJoin = $objMeta->callJoinFunction($objData, $valueField, $field))
                                                {
                                                    if (is_array($valJoin)) {
                                                        foreach ($valJoin as $idJoin => $valJoin) {
                                                            $joinSpan .= "<span data-autocomplete-id='$dataId-$field' class='span_auto_complete' data-item-value='$idJoin' title='Remove this item'>$valJoin [x]</span>";
                                                        }
                                                    }else{
                                                        $joinSpan = $valJoin;
                                                    }
                                                }
                                            }

                                            if($field[0] == '_'){
                                                $displayInput = "; display: none; ";
                                                if(is_array($valueField) && isset($valueField['value_show'])){
                                                    echo $valueField['value_show'];
                                                    $valueField = $valueField['value_post'];
                                                }
                                            }

//                                            if($joinSpan)
//                                                $valueField = $joinSpan;

                                            echo "<div data-pos='32453452345'  class='readonly_imgs'> $allNodeNameImg </div>";

                                            if($isRichText){
                                                echo "<div data-pos='32453452345'  class='_read_only_' data-type='rich_text' data-field='$field' style='border: 0px solid red' class='' id='edit_rich_text_$field'>$valueField</div>";
                                            }
                                            elseif($isTextArea)
                                                echo "<textarea data-pos='32453452345'  title='$fullDes / $field' readonly class='text_area_edit' >$valueField</textarea>";
                                            else
                                                echo "<div data-pos='32453452345' title='$fullDes / $field' class='one_item_edit'> $valueField $joinSpan </div>";


                                           // $isTextArea = 1;

                                        }


                                        ?>
                                    </div>
                                </div>


                                    <?php

                                    if($after_zone = $objMeta->afterZoneFieldEdit($field, $objData)){
                                        echo "<div class='divTable2Row for_seperatorHeader after_zone $field' style=' '>
                                        <div style='' class='seperatorHeader bg-light'>
                                           $after_zone
                                           <i class='open_all_tab fa fa-arrow-down ml-2' style='color: brown' title=''></i>
                                        </div>
                                    </div>";
                                    }

                                    ?>

                                <?php
                                }
                                ?>




                                <div class="divTable2Row dummy_item" style=" background-color: transparent;">
                                    <div class="divTable2Cell divTable2EditCell divTable2EditCellBold"
                                         style="  border: 0px solid white; padding: 0px">
                                    </div>
                                    <div class="divTable2Cell divTable2EditCell "
                                         style=" width: 100%;  border: 0px solid white;; padding: 0px">
                                    </div>
                                </div>

                            </div>

                        </form>


                    </div>
                </div>

                <div class="extra_edit">

                    <?php

                    $objMeta->extraHtmlIncludeEdit1($objData , $mMetaAll);

                    ?>
                </div>

            </div>


            <br>
            <br>
            <br>

            <?php
            //ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
            ?>
            <div id="dialog_tree" title="Tree Dialog" style="display: none">
                <div id="dialog_tree1" title="Tree Dialog" style="">
                </div>

                <div style="text-align: center" class="mb-2">
                    <button class="btn btn-primary btn-sm" id="close_select_tree"> Đóng </button>
                </div>
            </div>


            <div data-code-pos="ppp1678979596764" id="id-browse-file-dlg" title="Browse file" style="display: none; width: inherit">
                <iframe data-cmd="" data-field="" loading="lazy" data-field-iframe="need_set_field_on_js" id="id-iframe-browser-file" allowfullscreen
                        data-src="/<?php echo \App\Components\Helper1::getModuleCurrentName(request()) ?>/file?browse_file_iframe=1&limit=12" style="width: 100%; height: 99%">
                </iframe>
                {{--                <br>--}}
                {{--                <div style="text-align: center">--}}
                {{--                    <button class="btn btn-primary btn-sm" id="close_browse_file"> CLOSE</button>--}}
                {{--                </div>--}}
            </div>


        </section>
        <!-- /.content -->
        @include("parts.debug_info")
    </div>


@endsection



@section('js')

    <script src="/admins/table_mng.js?v=<?php echo filemtime(public_path().'/admins/table_mng.js');?>"></script>
    <script src="/vendor/div_table2/div_table2.js?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.js');?>"></script>

    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

    <script src="{{asset("vendor/select2/select2.min.js")}}"></script>
    <script src="{{asset("vendor/toastr/toastr.min.js")}}"></script>
    <script src="{{asset("admins/demo/add.js")}}?v=<?php echo filemtime(public_path().'/admins/demo/add.js');?>"></script>

    <script src="/assert/library_ex/js/jquery-context-menu2/jquery.contextMenu.js"></script>
    <script src="/vendor/lad_tree/clsTreeJs-v2.js?v=<?php echo filemtime(public_path().'/vendor/lad_tree/clsTreeJs-v2.js');?>"></script>
    <script src="{{asset("admins/tree_selector.js")}}?v=<?php echo filemtime(public_path().'/admins/tree_selector.js');?>"></script>

    <script src="/admins/upload_file.js?v=<?php echo filemtime(public_path().'/admins/upload_file.js');?>"></script>

    <script src="/vendor/tinymce/tinymce48.min.js"></script>
{{--    <script src="https://cdn.tiny.cloud/1/g1vtvjtaddckm3y3k3f1n3xbnphhlzvdw6vkjff03o8ixx1u/tinymce/5/tinymce.min.js"></script>--}}
{{--    <script src="/vendor/tinymce/tinymce5.10.9.min.js"></script>--}}
    <script src="/assert/js/date-time-picker/php-date-formatter.js"></script>
    <script src="/assert/js/date-time-picker/jquery.datetimepicker.js"></script>



    <?php

    foreach ($mMetaAll AS $field=>$objMeta){
        if($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb);

        if($objMeta->isRichTextField($field)){
            \App\Components\Helper1::tinyMceEditorInit("#edit_rich_text_$field", \App\Components\Helper1::getModuleCurrentName(request()), $objMeta, $objPr);
        }

    }


    ?>

    <script data-code-pos="ppp1679904073123">

        let  user_token = jctool.getCookie('_tglx863516839');
        //onliad document:
        $(document).ready(function () {

            $('.btn_copy_temp').on('click', function (ev) {

                ev.stopPropagation();
                console.log(" btn_copy_temp = ");
                //Get data-copy-field
                let dataCopyField = $(this).attr('data-copy-field');
                console.log(" dataCopyField = " + dataCopyField);
                //Gọi API để lấy giá trị mẫu điền vào ô input.input_value_to_post có dataCopyField tương ứng
                //
    //        lấy api trong: div có id = div_container , api trong: data-api-url-update-one
                let urlPost = $('#div_container').attr("data-api-url-update-one") + "/copyTemp/?field=" + dataCopyField;
                console.log(" API URL " + urlPost);
                showWaittingIcon();
                $.ajax({
                    url: urlPost,
                    type: 'GET',
                    headers: {
                        'Authorization': 'Bearer ' + user_token
                        // 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (result) {
                        hideWaittingIcon();
                        console.log(" RET222 = ", result);
                        if(!result.code){
                            alert("Có lỗi api 5:\n" + (result));
                        }else{
                            if(result.payload){
                                // showToastInfoTop(result.payload);
                                console.log(" result.payload = ", result.payload);


                                $(".input_value_to_post[data-field='"+dataCopyField+"']").attr('value', result.payload);
                                $(".input_value_to_post[data-field='"+dataCopyField+"']").prop('value', result.payload);

                                if (tinymce.get('edit_rich_text_' +dataCopyField)) {
                                    tinymce.get('edit_rich_text_' +dataCopyField).setContent(result.payload);
                                }
                            }
                            else
                                alert("Giá trị mẫu rỗng!")
                            //
                        }
                    },
                    error: function (result) {

                        hideWaittingIcon();
                        alert("Error: " + result.responseJSON.message)
                        console.log(" RET33 = ", result);
                    },
                });




            });

        })

        <?php
        foreach ($mMetaAll AS $field=>$objMeta){

            if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
            if($objMeta->isEditableFieldGetOne($field , $objPr->set_gid) &&
            ($objMeta->isMultiImagesField($field) || $objMeta->isOneImagesField($field))
        ){
            ?>
                let file_<?php echo $field?> = new clsUploadV2()
                file_<?php echo $field?>.url_server = '/api/member-file/upload';
                file_<?php echo $field?>.bind_selector_upload = 'upload_id_<?php echo $field?>';
                file_<?php echo $field?>.upload_done_call_function = 'uploadDone1'
                file_<?php echo $field?>.initUpload();
                file_<?php echo $field?>.bearerToken = jctool.getCookie('_tglx863516839');
                file_<?php echo $field?>.maxSizeUpload = <?php echo \App\Models\SiteMng::getMaxSizeUpload() ?>;
            <?php
            }
        }

        ?>
    </script>

    <script>

        $(function (){
            // $( "[name=created_at], input[data-field=end_time], [name=modified_at], [name=time], [name=time_expired], [name=time_start],input[data-field=start_time], input[data-field=done_at]").datetimepicker({
            //     // format:'Y-m-d H:i:s',
            // });

            $(".open_all_tab").click(function (event){
                //Các tab nào đang đóng thi mo:
                $(".divTable2Row").css('display', 'table-row');
                //Chan su kien di len parent
                event.stopPropagation();

            });

            $(".close_all_tab").click(function (event){
                //Các tab nào đang đóng thi mo:
                $(".divTable2Row").not('.for_seperatorHeader').css('display', 'none');
                $('.for_seperatorHeader').css('display', 'table-row');

//Chan su kien di len parent
                //Chan su kien di len parent
                event.stopPropagation();

            });


            $(".edit_date").datetimepicker({
                format:'d/m/Y',
                mask:true,
            });

            $(".edit_date_time").datetimepicker({
                format:'d/m/Y H:i:s',
                mask:true,
            });





            ///ScanInDB
        <?php

        if(0)
        if($mMetaAll){
            foreach ($mMetaAll AS $mt){

                if($mt instanceof \LadLib\Common\Database\MetaOfTableInDb);

                if($mt->data_type_in_db == 'date'){
                ?>
                $( "[name=<?php echo $mt->field ?>]").datetimepicker({
                    format:'d-m-Y',
                });
                    <?php
                    }
                elseif($mt->data_type_in_db == 'datetime' || $mt->data_type_in_db == 'timestamp'){
               ?>
                    $( "[name=<?php echo $mt->field ?>]").datetimepicker({
                        format:'d/m/Y H:i:s',
                    });
                <?php
                }
                else
                if($mt->isDateType($mt->field) || $mt->isDateTimeType($mt->field)){
            ?>
            $( "[name=<?php echo $mt->field ?>]").datetimepicker({
                format:'d/m/Y',
            });
            <?php
                }
            }
        }

        ?>
        })

    </script>

    <script>

        //Paste img from clliboard
        document.onpaste = function (e) {
            var items = e.clipboardData.items;
            for( var i = 0, len = items.length; i < len; ++i ) {
                var item = items[i];
                if( item.kind === "file" ) {
                    submitFileForm(item.getAsFile(), "paste");
                }
            }
        };

        function submitFileForm(file, type) {
            var extension = file.type.match(/\/([a-z0-9]+)/i)[1].toLowerCase();
            var formData = new FormData();
            formData.append('file_data', file, "paste." + Date.now());
            formData.append('extension', extension );
            formData.append("mimetype", file.type );
            formData.append('submission-type', type);
            let urlPost = "/api/member-file/upload";
            let xhr = new XMLHttpRequest();
            xhr.responseType = "json";
            xhr.open('POST', urlPost);
            xhr.onload  = function(e) {
                var jsonResponse = this.response;
                if (this.status == 200) {
                    console.log(" Upload ret = ", jsonResponse, e);
                    let objBind = {};
                    objBind.bind_selector_upload = 'upload_id_image_list_file';
                    uploadDone1(jsonResponse, objBind)
                }
                else{
                    console.log(" Upload ret = ", this.response);
                    if(this.response && this.response.payload)
                        alert("Có lỗi upload " + this.response.payload);
                    else
                        alert("Có lỗi upload : lỗi không xác định!");
                }
            };
            xhr.send(formData);




        }
    </script>


    <?php
    echo $objMeta->extraJsIncludeEdit($objData , $mMetaAll);
    echo $objMeta->extraCssIncludeEdit($objData , $mMetaAll);
    ?>
    <?php
    //ladDebug::addTime(__FILE__ . " edit blade ", __LINE__);
    ?>
@endsection
