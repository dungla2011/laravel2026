<?php
//echo "<br/>\n abc1232: $id_ui_block";

    if(!$obj = \App\Models\BlockUi::find($id_ui_block)){
        die("Not found block ui: '$id_ui_block'");
    }
    if($obj instanceof \App\Models\BlockUi);

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    <?php
    echo $obj->getName() . " : ".\LadLib\Common\UrlHelper1::getDomainHostName()
    ?>
@endsection

@section("og_title")<?php

echo $obj->getName() . " - ". strtoupper(\LadLib\Common\UrlHelper1::getDomainHostName())
?>
@endsection

@section("css")

@endsection

@section("content")
    <?php
    \App\Models\BlockUi::showCssHoverBlock();
    ?>
    <div class="container link_html idx_{{$obj->id}} qqqq1111" style="min-height: 500px">
        {!! $obj->showEditButton() !!}
        {!! $obj->getContent() !!}

    </div>
@endsection
