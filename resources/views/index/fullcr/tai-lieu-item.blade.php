@extends(getLayoutNameMultiReturnDefaultIfNull())

<style>
    .row1 {
        border-bottom: 2px solid darkorange;
    }

    .heading1 {
        background-color: darkorange;
        color: white;
        display: inline-block;
        font-weight: bold;
        padding: 7px 30px 7px 15px;
        font-size: 20px;
        text-transform: uppercase;
    }
    .brc_path {
        font-weight: bolder;
        padding-bottom: 10px;
        border-bottom: 1px solid #ccc;
    }
</style>

<?php

if($fid = request('fid')){
if($obj = \App\Models\MyDocument::find($fid)){

?>
@section('title') {{$obj->name}}  @endsection
<?php
}
}
?>
@section('content')

    <div class="container py-3">


        <?php
        if($fid = request('fid'))
        if($obj = \App\Models\MyDocument::find($fid)){

        if($obj->file_list){

        $file = \App\Models\FileUpload::find($obj->file_list);
        if ($file instanceof \App\Models\FileUpload) ;
        if ($obj instanceof \App\Models\MyDocument) ;

        $img = null;
        if($file){
            $img = $file->getCloudLink(0);
            $imgThumb = $obj->getThumbInImageList();

            echo $obj->getBreakumPathHtml(0, 1);
        }

        if($img){
        ?>

        <div class="row" data-code-pos="qqq17067577812190">
            <div class="col-sm-8">
                <?php

                echo "<h1 style='font-size: 20px' class='my-3'> $obj->name ";

                if (isSupperAdmin_()) {

                    echo "\n <a href='/admin/my-document/edit/$obj->id' target='_blank'> <i class='fa fa-edit'></i> </a>";
                }

                echo "\n</h1>";
                ?>

                    <img style="border: 1px solid #ccc; width: 100%" src="{{$imgThumb}}" alt="">

                    <div data-code-pos="qqq1706777815668" class="txt-center pt-3" style="text-align: center">


                        <a href="{{$img}}" target="_blank" >
                    <button class="btn btn-info text-white">Xem sách</button>
                        </a>
                    </div>
            </div>
            <div class="col-sm-4 txt-center">


                <h2 style="margin: auto; text-align: center; font-size: 20px" class="my-3">
                    Xem thêm
                </h2>
                <?php

                $mDoc = \App\Models\MyDocument::where(['parent_id' => $obj->parent_id])->whereNotNull('file_list')->where(function ($query) {
                    $query->where('name', 'like', "%toán%")->orWhere('name', 'like', "%tin học%");
                })->limit(10)->get();

                foreach ($mDoc AS $doc){
                ?>

                <p>
                    <a href="/tai-lieu/chi-tiet?fid={{$doc->getId()}}">
                        <img src="{{$img = $doc->getThumbSmall(300)}}"
                             style="width: 200px; display: block; margin: auto" alt="">
                    </a>
                </p>

                <?php
                }
                ?>

            </div>
            <?php
            }
            }


            }

            ?>


        </div>
    </div>

    <p></p>
    <p></p>
@endsection
