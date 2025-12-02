<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\ConferenceInfo;
use App\Models\FileUpload;
use App\Repositories\ConferenceInfoRepositoryInterface;


class tmpTimeSheet
{

    public $type;
    public $timeString;
    public $title;
    public $summary;
    public $images = [];

    function __construct($obj, $timeString)
    {
        if ($obj->type ?? '')
            $this->type = $obj->type;
        if ($obj->title ?? '')
            $this->title = $obj->title;
        if ($obj->summary ?? '')
            $this->summary = $obj->summary;

        $this->timeString = $timeString;

        $domain = 'https://events.dav.edu.vn';
        for ($i = 0; $i < 100; $i++) {
            if ($obj->{"image$i"} ?? '') {

                $imgAndLink = $obj->{"image$i"};
                list($id, $text) = explode(',', $imgAndLink, 2);
                $text = isset($text) ? $text : ''; // Ensure text is not null

                $linkImg = '';
                if ($file = FileUpload::find($id)) {
                    $linkImg = $domain.$file->getCloudLinkImage();
                }

//                echo "<img src='$linkImg'>";
                $this->images[] = [
                    'img' => $linkImg,
                    'text' => $text
                ];
            }
        }


    }
}

function getFileLinkArrayFromListId($listId){
    $domain = 'https://events.dav.edu.vn';
    $ret = [];
    if($listId){
        $mm = explode(",", $listId);
        foreach ($mm AS $id)
            if($file = FileUpload::find($id)){
                $ret[] = ['name' => $file->name, "link" => $domain.$file->getCloudLinkImage()];
            }
    }
    if(!$ret)
        return null;
    return $ret;
}

class ConferenceInfoControllerApi extends BaseApiController
{
    public function __construct(ConferenceInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }

    function listConf()
    {
        $mm = ConferenceInfo::select(['name', 'cat', 'id'])->where("status", 1)->orderBy('orders', 'asc')->get();
        $meta = ConferenceInfo::getMetaObj();
        $mCat = $meta->_cat();

// Khởi tạo mảng mới
        $newArray = [];

// Duyệt qua mỗi item trong mm
        foreach ($mm as $item) {
            // Lấy tên từ mCat dựa trên id
            $name = $mCat[$item->cat] ?? null;

            // Nếu tên tồn tại, thêm item vào mảng tương ứng
            if ($name) {
                $newArray[$name][] = $item->toArray();
            }
        }
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($newArray);
//        echo "</pre>";
        return $newArray;
    }
    function get_conf_info()
    {

        if(request('list_conf')){
            $ret = $this->listConf();
            $js = json_encode($ret);
            ob_clean();
            echo $js;
            return;
        }


        $cid = request('id');

        $obj = ConferenceInfo::find($cid);

        if (!$obj) {

            return $this->responseError('Không tìm thấy thông tin hội nghị');
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj->toArray());
//        echo "</pre>";

        $obj1 = (object)$obj->toArray();
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj1);
//        echo "</pre>";
        $domain = 'https://events.dav.edu.vn';
        for ($i = 1; $i <= 3; $i++) {


//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj1);
//        echo "</pre>";
            $conf_timesheet = $obj->{"conf$i"."_timesheet"};
//            $conf_timesheet = addslashes($conf_timesheet);

//            $conf_timesheet = str_replace('&amp', '&', $conf_timesheet); // Fix lỗi &amp;

            $conf_timesheet = html_entity_decode($conf_timesheet); // Fix lỗi &amp;
            $ini_array = parse_ini_string($conf_timesheet, true);

            $ini_object = json_decode(json_encode($ini_array));
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($ini_object);
//        echo "</pre>";
            $obj1->{"conf$i"."_timesheet"} = [];
            $tmpConf = null;
            foreach ($ini_object as $time => $info) {
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($time);
//            print_r($info);
//            echo "</pre>";

//                if ($info->type ?? '')
                {
                    $tmpConf = new tmpTimeSheet($info, $time);
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($tmpConf);
//                echo "</pre>";
                }
                $obj1->{"conf$i"."_timesheet"}[] = $tmpConf;
            }


//            if ($tmpConf)


            $imgField = "conf$i"."_images";
            $obj1->$imgField = [];
            if($imgx = $obj->$imgField ?? ''){
                $m1 = explode(",", $imgx);
                if($m1){
                    foreach ($m1 AS $fid){
                        $file = FileUpload::find($fid);
                        if($file){
                            $obj1->$imgField[] = $domain.$file->getCloudLinkImage();
                        }
                    }
                }

            }
        }

        if($obj1->key_notes){
            $mm = explode("\n", $obj1->key_notes);
            $mm = array_filter($mm);

            $obj1->key_notes = [];
            $obj1->key_notes['title'] = trim($mm[0]);

            if(substr($obj1->key_notes['title'], 0, strlen("title:")) == "title:"){
                $obj1->key_notes['title'] = trim(substr($obj1->key_notes['title'], strlen("title:")));
            }
            if(substr($obj1->key_notes['title'], 0, strlen("title=")) == "title="){
                $obj1->key_notes['title'] = trim(substr($obj1->key_notes['title'], strlen("title=")));
            }

            array_shift($mm);
            $obj1->key_notes['videos'] = $mm;
        }

        if($obj->images){
            $first = explode(",", $obj->images)[0];

            if($file = FileUpload::find($first)){
                $obj1->images = $domain.$file->getCloudLinkImage();
            }
        }

        if($obj->supporters){
            $mm = explode(",", $obj->supporters);

            $obj1->supporters = [];
            foreach ($mm AS $id)
            if($file = FileUpload::find($id)){
                $obj1->supporters[] = $domain.$file->getCloudLinkImage();
            }
        }

        if($obj->right_column){

            $obj->right_column = html_entity_decode($obj->right_column); // Fix lỗi &amp;
            $ini = parse_ini_string($obj->right_column,1);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($ini);
//            echo "</pre>";

            $obj1->right_column = [];
            foreach ($ini AS $key => $value){
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($value);
//                echo "</pre>";
                $one = [];
                $one['title'] = $key;
                $linkImg = FileUpload::find($value['image'] ?? '' )?->getCloudLinkImage();
                $one['image'] = null;
                if($linkImg)
                    $one['image'] = $domain . $linkImg;

                $one['files'] = getFileLinkArrayFromListId($value['file'] ?? '');

                $obj1->right_column[] = $one;

//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($one);
//                echo "</pre>";
            }
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj1);
//        echo "</pre>";


        ob_clean();
        echo json_encode($obj1);

        if(request('debug') == 111){
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($obj1);
            echo "</pre>";
        }
    }
}
