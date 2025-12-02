<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\FileUpload;
use App\Models\HrLogTask;
use App\Models\HrMessageTask;
use App\Models\HrMessageTask_Meta;
use App\Models\HrTask;
use App\Repositories\HrMessageTaskRepositoryInterface;
use Illuminate\Http\Request;

class HrMessageTaskControllerApi extends BaseApiController
{
    public function __construct(HrMessageTaskRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function uploadFileForHrTask(Request $request)
    {

        $uid = getUserIdCurrentInCookie();
        $task_id = $request->task_id;
        if (! $task = HrTask::find($task_id)) {
            return rtJsonApiError("Not found task id: $task_id");
        }

        //Nếu là nhóm admin, thì bỏ qua ko check quyền
        if (in_array(1, getGidCurrentCookie()) || in_array(2, getGidCurrentCookie())) {
        } elseif ($task->user_id_get != $uid) {
            return rtJsonApiError("Not your task? $task_id");
        }

        $mImg = [];
        $idImgList = '';

        $ret = FileUploadControllerApi::uploadStatic($request, 1);

        $retHtml = '';

        if ($ret) {
            foreach ($ret as $obj) {
                //            print_r($obj->toArray());
                $idImgList .= "$obj->id,";
                $mImg[] = $obj->id;
                if ($obj instanceof FileUpload);

                $link = $obj->getCloudLink();

                //            $fname = $obj->name;
                //            if(strlen($obj->name) > 50)
                //                $fname = substr($obj->name,0,50)." ...";
                //
                //            if(str_starts_with($obj->mime, 'image'))
                //                $retHtml .= "<a href='$link' target='_blank'> <img src='$link' style='width: 150px; margin: 3px'> <br> $fname </a>";
                //            else
                //                $retHtml .= "<a href='$link' target='_blank'> <div style='color: dodgerblue; border: 1px dashed #ccc; width: 150px; margin: 3px; text-align: center; padding: 5px '>  $fname </div> </a>";
            }
        }

        if ($request->send_file_on_chat_box) {
            $hrMess = new HrMessageTask();

            $hrMess->message = '[upload '.count($mImg).' file]';
            $hrMess->image_list = trim($idImgList, ',');
            $hrMess->type = DEF_HR_TYPE_MESSAGE_TASK_IMAGE;
            $hrMess->task_id = $task_id;
            $hrMess->user_id = $uid;
            $hrMess->save();

            //            $hrMess->message = $hrMess->getHtmlShowFileOfMessageInChatBox();

            //            HrMessageTask_Meta::pushMessageOfTaskHr($task_id, $uid, json_decode(json_encode(['message'=> $retHtml, 'created_at'=> nowyh()])));
            HrMessageTask_Meta::pushMessageOfTaskHr($task_id, $uid, $hrMess);

            return rtJsonApiDone('Done upload file!');
        }

        $idImgList = trim($idImgList, ',');

        if ($task->type == 2) {
            $last = HrLogTask::where(['user_id' => $uid])->latest()->first();
            //Nếu chưa có của hôm nay thì tạo mới
            if (! $last || $last->created_at < nowy()) {
                $newT = new HrLogTask();
                $newT->image_list = $idImgList;
                $newT->user_id = $uid;
                $newT->task_id = $task_id;
                $newT->save();

                return rtJsonApiDone($newT->toArray());
            } elseif ($last) {
                $last->image_list = ",$last->image_list,";
                foreach ($mImg as $fid) {
                    if (strstr($last->image_list, ",$fid,") === false) {
                        $last->image_list .= ",$fid,";
                        $last->image_list = str_replace(',,', ',', $last->image_list);
                    }
                }
                $last->image_list = str_replace(',,', ',', $last->image_list);
                $last->image_list = trim($last->image_list, ',');
                $last->save();
                $newT = $last;

                return rtJsonApiDone($newT->toArray());
            }
        }

    }

    public function getMessageOfTask(Request $request)
    {

        $uid = getUserIdCurrentInCookie();
        if (! $uid) {
            return rtJsonApiError('Not userid to insert message');
        }

        $all = $request->all();

        //Kiểm tra task đó của userid:
        $taskId = $request->task_id;
        $task = HrTask::find($taskId);
        if (! $task) {
            return rtJsonApiError("Not found task: $taskId");
        }

        //Todo: cần có policy  chỉ task của user, hoặc role mng mới có thể list message task
        //        if($task->user_id != getUserIdCurrentInCookie()){
        //            return rtJsonApiError("Not your task: $taskId");
        //        }

        $mm = HrMessageTask::where(['task_id' => $taskId])->latest('id')->take(500)->get()->reverse();

        $retMes = '';
        foreach ($mm as $obj) {

            $clsMs = 'task_message_l';
            if ($obj->user_id == $uid) {
                $clsMs = 'task_message_r';
            }
            $retMes .= HrMessageTask_Meta::getOneTaskMessageFormat($clsMs, $obj);

        }
        if (! $retMes) {
            $retMes = "<div style='font-size: small; text-align: center'>(Hãy nhập nội dung trao đổi)</div>";
        }

        $ret = $task->toArray();
        $ret['message_list'] = $retMes;

        return rtJsonApiDone($ret);

    }

    public function postTaskMessage(Request $request)
    {

        $uidSend = getUserIdCurrentInCookie();
        if (! $uidSend) {
            return rtJsonApiError('Not userid to insert message');
        }

        $taskId = $request->task_id;
        $task = HrTask::find($taskId);
        if (! $task) {
            return rtJsonApiError("Not found task: $taskId");
        }

        if (! $task->uid_list_chat || strstr($task->uid_list_chat, $uidSend) === false) {
            if ($task instanceof HrTask);
            $task->uid_list_chat .= ",$uidSend,";
            $task->uid_list_chat = str_replace(',,', ',', $task->uid_list_chat);
            $task->update();
        }

        if ($task->user_id_get || strstr($task->uid_list_chat, $task->user_id_get) === false) {
            $task->uid_list_chat .= ",$task->user_id_get,";
            $task->uid_list_chat = str_replace(',,', ',', $task->uid_list_chat);
            $task->update();
        }

        //Todo: cần có policy chỉ task của user, hoặc role mng mới có thể post message task

        $obj = new HrMessageTask();
        $obj->user_id = $uidSend;
        $obj->message = $request->info;
        $obj->task_id = $taskId;
        $obj->save();

        $mUid = explode(',', $task->uid_list_chat);
        if (! in_array($task->user_id_get, $mUid)) {
            $mUid[] = $task->user_id_get;
        }
        if (! in_array($task->user_id, $mUid)) {
            $mUid[] = $task->user_id;
        }

        $mUid = array_unique($mUid);

        $mUid = array_filter($mUid);

        foreach ($mUid as $uidGet) {
            if (! $uidGet) {
                continue;
            }

            //            echo "<br/>\n $uidGet";

            //            if($uidGet == $uidSend)
            //                $data['message_new'] = HrMessageTask_Meta::getOneTaskMessageFormat('task_message_r', $obj);
            //            else
            //                $data['message_new'] = HrMessageTask_Meta::getOneTaskMessageFormat('task_message_l', $obj);
            //            $pusher->trigger('glx-message-task', 'glx-message-task-id-' . $taskId."-".$uidGet, $data);

            HrMessageTask_Meta::pushOneMessageChatHr($taskId, $uidSend, $uidGet, $obj);

        }

        return rtJsonApiDone('DONE mess');
        //        die();
        //        return rtJsonApiDone(HrMessageTask_Meta::getOneTaskMessageFormat("task_message_r", $obj));

    }
}
