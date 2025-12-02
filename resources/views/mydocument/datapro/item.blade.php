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

if ($fid = request('fid'))
{
    if(!is_numeric($fid)){
        $fid = qqgetIdFromRand_(request('fid'));
    }

if ($obj = \App\Models\MyDocument::find($fid)){

    ?>
@section('title')
    {{$obj->name}}
@endsection
    <?php
}
}
?>
@section('content')
    <td valign="top" style="padding:0px 10px;" class="center-panel">

        <div class="container py-3">


            <?php

            {

            {



                if ($obj instanceof \App\Models\MyDocument) ;

                $img = null;
                {

                    $imgThumb = $obj->getThumbInImageList();

                    echo $obj->getBreakumPathHtml(0, 1);
                }

            {
                ?>

            <div class="row" data-code-pos="qqq17067377812190">
                <div class="col-sm-8">
                        <?php

                        echo "<h1 style='font-size: 20px' class='my-3'> $obj->name ";

                        if (isSupperAdmin_()) {

                            echo "\n <a href='/admin/my-document/edit/$obj->id' target='_blank'> <i class='fa fa-edit'></i> </a>";
                        }

                        echo "\n</h1>";
                        ?>

                    <img style="text-align: center; border: 1px solid #ccc; width: 100%; max-width: 200px; margin: 0 auto " src="{{$imgThumb}}" alt="">

                <div style="margin-top: 10px">
                    <b>
                    {{ $obj->summary  }}
                    </b>
                </div>
                    <hr>
                <div>
                    {{ $obj->content  }}
                </div>


                </div>

                <div class="col-sm-4 txt-center">
                </div>

                    <?php
                }
                }


                }

                ?>


            </div>
        </div>
    </td>
    <p></p>
    <p></p>
@endsection
