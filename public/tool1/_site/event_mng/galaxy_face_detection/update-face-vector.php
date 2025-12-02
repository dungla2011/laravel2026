<?php

use App\Models\FileUpload;

$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isCli()){
    die('Only run in CLI mode');
}

function ol1($str)
{
    echo "\n$str";
    $file = "/var/glx/weblog/face_relearn.log";
    file_put_contents($file, date('Y-m-d H:i:s') . " " . $str . "\n", FILE_APPEND);
}

try {

    $cc = 0;

    $startTime = nowyh();

    while(1) {
        sleep(1);

        //Tìm tất cả các image_list của user:
        //Lấy tất cả EventUserInfo có image_list không rỗng
        $mUe = \App\Models\EventUserInfo::whereNotNull('image_list')->where('image_list', '!=', '')->get();

        $image_list = '';
        $totalImage = 0;
        //Lấy tất cả uid của cloud id image file:
        foreach ($mUe as $ue) {
            if ($ue instanceof \App\Models\ModelGlxBase) ;
            $image_list0 = $ue->image_list;
            $m1 = explode(',', $image_list0);
            $totalImage += count($m1);
            if($m1)
                foreach ($m1 AS $idf)
                    if(is_numeric($idf)){
                        if($file = FileUpload::find($idf)) {
                            if ($file instanceof FileUpload) ;
                            if (!\App\Models\EventFaceInfo::where('file_cloud_id', $file->cloud_id)->first()) {
                                $image_list .= "," . $idf;
                            }
                        }
                    }

//            $mFileCloudId = $ue->getAllImageListCloudId();
//            foreach ($mFileCloudId as $file_cloud_id) {
//                //Nếu file_cloud_id không có trong DB thì bỏ qua
//                if (!\App\Models\EventFaceInfo::where('file_cloud_id', $file_cloud_id)->first()) {
//                    $image_list .= "," . $ue->image_list;
//                }
//            }
        }


        $nFace = 0;
        if (!$image_list) {
            echo "\n Total Img = $totalImage, No new image to re-learn at " . nowyh();
//            ol1(" Not found any new image to re-learn");
        } else{
            $nFace = reLearnFace(trim($image_list, ','));
            ol1("\n Total Img = $totalImage, New FACE = $nFace");
        }
        sleep(10);

        if($cc % 360 == 0){
            ol1(" Total Img = $totalImage, Still running... $cc times, From $startTime ");
        }
        $cc++;
    }


} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
