@extends("layouts.adm")

@section("title")
     INDEX Demo
@endsection

@section('header')
    @include('parts.header-all')
@endsection

@section('css')
    <link rel="stylesheet" href="/vendor/div_table2/div_table2.css?v=<?php echo filemtime(public_path().'/vendor/div_table2/div_table2.css'); ?>">
    <link rel="stylesheet" href="/admins/table_mng.css?v=<?php echo filemtime(public_path().'/admins/table_mng.css'); ?>">
    <link rel="stylesheet" href="/assert/library_ex/jquery-ui/jquery-ui.css">
@endsection

@section('js')

    <script src="/admins/table_mng.js"></script>

    <script src="/vendor/div_table2/div_table2.js"></script>
    <script src="/vendor/jquery/jquery-ui-1.13.2.js"></script>

@endsection

@section("content")
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div><!-- /.col -->
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item" data-code-pos='ppp1725148551'><a href="#">Demo</a></li>
                            <li class="breadcrumb-item active"></li>
                        </ol>
                    </div><!-- /.col -->
                </div><!-- /.row -->
            </div><!-- /.container-fluid -->
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">

                <?php
                if(!isset($dataApiUrl)){
                    echo "<br/>\n *** Error: not found dataApiUrl";
                }
                if(!isset($mMetaAll)){
                    echo "<br/>\n *** Error: not found mMetaAll";
                }
                if (!isset($dataView) || !$dataView){
                    goto _END;
                    dd("*** Error: not found dataView");
                }

                ?>

                <div class="row">
                    <div class="col-md-12">
                        <a href="<?php echo 'abc123' ?>" class="btn btn-success float-right m-2"> ADD </a>
                        <a id="save-all-data"
                           class="btn btn-success float-right m-2"> SaveAll </a>
                    </div>
                    <div class="col-md-12">
                        <form id="form_data">
                            <div class="divContainer" id="div_container"
                                 data-api-url-update-multi="<?php echo $dataApiUrl ?>/update-multi"
                                 data-api-url-update-one="<?php echo $dataApiUrl ?>/update">
                                <div class="divTable2" style=";border: 1px solid #000;">
                                    <div class="divTable2Body">
                                        <div class="divTable2Row">
                                            <?php
                                            foreach ($mMetaAll AS $objMeta){
                                            if ($objMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;
                                            $field = $objMeta->field;

                                            if(!$objMeta->isShowIndexField($field,1)){
                                                continue;
                                            }
                                            //123

                                            ?>
                                            <div class="divTable2Cell cellHeader" title="">
                                                <?php
                                                echo $objMeta->getDescOfField($field);
                                                ?>
                                            </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                        $row = 0;
                                        foreach ($dataView AS $objData){

                                        ?>
                                        <div class="divTable2Row">
                                            <?php
                                            $col = 0;
                                            foreach ($mMetaAll AS $objMeta){

                                            $displayInput = null;
                                            $field = $objMeta->field;

                                            //Không in ra cell nếu ko show index
                                            if(!$objMeta->isShowIndexField($field,1)){
                                                continue;
                                            }

                                            $descField = $objMeta->getDescOfField($field);

                                            $valueField = $objData->$field;
                                            $isEdit = $objMeta->isEditableField($field, 1);

                                            $bgGray = null;
                                            $readlOnlyInput = $disabledInput = $ifClsTextCenter = null;
                                            //ID luôn cần có, để có thể post,  nên ko thể disable
                                            if ($field == 'id') {
                                                $ifClsTextCenter = " text-center ";
                                                $readlOnlyInput = 'readonly';
                                            } else
                                                if (!$isEdit) {
                                                    $disabledInput = 'disabled';
                                                    $readlOnlyInput = 'readonly';
                                                    $bgGray = ' bgGray ';
                                                }

                                            $multiValue = 0;
                                            if ($objMeta->isArrayStringField($field)
                                                || $objMeta->isArrayNumberField($field)) {
                                                $multiValue = 1;
                                            }
                                            ?>
                                            <div data-multi-value="<?php echo $multiValue ?>"
                                                 class="divTable2Cell <?php echo $objMeta->css_cell ; ?> <?php echo $bgGray ; ?>"
                                                 data-tablerow="<?php echo $row ?>"
                                                 data-tablecol="<?php echo $col ?>" title="">
                                                <?php
                                                if ($joinFunc = $objMeta->isSelectField($field)) {
                                                    $displayInput = "; display: none; ";
                                                    $mm = call_user_func($joinFunc);
                                                    echo "<select data-id='$objData->id' data-joinfunc='$joinFunc' class='sl_option' data-field='$field' >";
                                                    foreach ($mm AS $key => $val) {
                                                        $selected = '';
                                                        if ($objData->$field == $key)
                                                            $selected = 'selected';
                                                        echo "<option value='$key' $selected> $val </option>";
                                                    }
                                                    echo "</select>";
                                                } elseif ($objMeta->join_api) {
                                                    if ($objMeta->join_func) {
                                                        $joinSpan = null;
                                                        if (is_callable($objMeta->join_func)) {
                                                            if ($valJoin = call_user_func($objMeta->join_func, $objData, $valueField)) {
                                                                if ($multiValue) {
                                                                    if(is_array($valJoin))
                                                                    foreach ($valJoin AS $idJoin => $valJoin) {
                                                                        $joinSpan .= "<span data-autocomplete-id='$objData->id-$field' class='span_auto_complete' data-item-value='$idJoin' title='Remove this item'>$valJoin [x]</span>";
                                                                    }
                                                                } else
                                                                    $joinSpan = "<span data-autocomplete-id='$objData->id-$field' class='span_auto_complete' data-item-value='$valueField' title='Remove this item'>$valJoin [x]</span>";
                                                            }
                                                        } else {
                                                            $joinSpan = "Not callable: $objMeta->join_func()";
                                                        }
                                                        //echo "<br/>\n JOIN = $objMeta->join_func ";
                                                    }
                                                    $displayInput = "; display: none; ";

                                                    echo "<div class='search-auto-complete-tbl' style=''>$joinSpan</div>";
                                                    echo "<input placeholder='Search $descField'  data-autocomplete-id='$objData->id-$field' class='search-auto-complete-tbl' " .
                                                        "data-api-search='$objMeta->join_api' data-api-search-field='$objMeta->join_api_field' type='text' value=''>";
                                                } else
                                                    if ($objMeta->isStatusField($field)) {
                                                        $displayInput = "; display: none; ";
                                                        $cls = null;
                                                        if($isEdit)
                                                            $cls = 'change_status_item';
                                                        if ($objData->$field)
                                                            echo "<div class='text-center'><i data-id='$objData->id' data-field='$field' class='fa fa-toggle-on $cls'></i></div>";
                                                        else
                                                            echo "<div class='text-center'><i data-id='$objData->id' data-field='$field' class='fa fa-toggle-off $cls'></i></div>";
                                                    }

                                                if(1)
                                                {
                                                ?>
                                                <input <?php echo $readlOnlyInput . " " . $disabledInput ?>
                                                       class="hidden <?php echo $ifClsTextCenter . " $field " . $objMeta->getCssClass($field) ?>"
                                                       data-field='<?php echo $field?>' type="text"
                                                       data-autocomplete-id="<?php echo $objData->id . "-$field" ?>"
                                                       value="<?php echo($objData->$field) ?>"
                                                       name="<?php echo $field ?>[]"
                                                       data-id="<?php echo $objData->id ?>"
                                                       style="<?php echo $displayInput ?>"
                                                >
                                                <?php
                                                }
                                                ?>
                                            </div>
                                            <?php
                                            $col++;
                                            ?>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                        <?php
                                        $row++;
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-12">
                    {{ $dataView->links() }}
                </div>

                <?php
                _END:
                ?>
            </div>
        </div>
    </div>
    <!-- /.content -->
    </div>
@endsection
