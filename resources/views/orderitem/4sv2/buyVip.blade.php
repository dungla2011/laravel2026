<?php
$uid = getCurrentUserId();
?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('content')

    <style>
        .doiTac img {
            width: 120px;
            height: 60px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>

    <div class="container mt-5">
        <div class="my-4 p-3 rounded" style="background-color: lavender; text-align: center; font-size: 130% " data-code-pos='ppp17395182412771'>
            @include('parts.buyVip')
        </div>

    </div>

@endsection
