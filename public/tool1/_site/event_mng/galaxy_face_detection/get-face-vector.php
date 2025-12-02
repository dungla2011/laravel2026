<?php

$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'events.dav.edu.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


try {

    //Lấy tất cả EventUserInfo có image_list không rỗng
    $mUe = \App\Models\EventUserInfo::whereNotNull('image_list')->where('image_list', '!=', '')->get();

    $mCloudIdAndUserEventId = [];
    //Lấy tất cả uid của cloud id image file:
    foreach ($mUe AS $ue){
        if($ue instanceof \App\Models\ModelGlxBase);
        $mCId = $ue->getAllImageListCloudId();
        foreach ($mCId AS $cid){
            $mCloudIdAndUserEventId[$cid] = $ue->id;
        }
    }

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($mCloudIdAndUserEventId);
//    echo "</pre>";
//    die();


    // EventFaceInfo, tìm ra cac bản ghi có face_vector
    $faces = \App\Models\EventFaceInfo::whereNotNull('face_vector')
        ->select('id', 'file_cloud_id', 'face_vector')
        ->get() ;

    $mFace = [];
    foreach ($faces AS $face){
        $file_cloud_id = ($face->file_cloud_id);
        if(!isset($mCloudIdAndUserEventId[$file_cloud_id])){
            continue;
        }
        $mFace[] = [ 'id'=>$face->id, 'user_event_id'=>$mCloudIdAndUserEventId[$file_cloud_id] , 'face' => $face->face_vector ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $mFace
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_NUMERIC_CHECK | JSON_PRESERVE_ZERO_FRACTION | JSON_UNESCAPED_SLASHES);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
