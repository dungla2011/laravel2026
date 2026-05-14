<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\EventUserInfo;
use Example\Common\HTTPStatus;
use Illuminate\Support\Facades\Crypt;

class EventUserInfoController extends BaseController
{
    protected EventUserInfo $data;

    public function __construct(EventUserInfo $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    function update_user_info(){

        $eventUserInfo = '';
        if ($updateUI = request('update_user_info')) {
//            die("ABC123");
            $updateUI = substr($updateUI, 0, 100);
            $info = dfh1b($updateUI);

            $evUid = explode("-", $info)[0];
            $timeStamp = explode("-", $info)[1] ?? '';

            if(!is_numeric($evUid) || ! is_numeric($timeStamp)){
                die("Update userinfo: Not valid info?");
            }

            if($timeStamp < time() - 10800){
//                bl("Link cập nhật quá hạn, vui lòng Click vào đây để gửi lại link!");
                return view('member.ncbd.form-send-mail-event-update', compact('eventUserInfo'));
            }

            if(!$eventUserInfo = EventUserInfo::where('command_ex', $info)->first()){
                return view('member.ncbd.form-send-mail-event-update', compact('eventUserInfo'));
            }

            return view('member.ncbd.event-update', compact('eventUserInfo'));
        }

        return view('member.ncbd.form-send-mail-event-update', compact('eventUserInfo'));

    }


    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }
}
