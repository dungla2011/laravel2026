<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "test2023.mytree.vn";

require "/var/www/html/public/index.php";



$file = "/var/glx/weblog/zalo-test-mess-log.txt";

$lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach ($lines AS $l1){
//    echo "\n $l1";

    $mess = trim(explode("# ", $l1)[1]);

    if($mm = json_decode($mess)){
        $obj1 = (object) $mm;

        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($mm);
        echo "</pre>";
//        getch("..");
//
        echo "\n";
//        echo "";
        print_r($obj1->threadId);
        echo "\n";
        //Kiem tra thread id co trong DB khong
        if($ox = \App\Models\CrmMessage::where("thread_id", $obj1->threadId)->first()){
            echo "\n Have TID " . $mm->data->msgId;

            $chanelName = $ox->channel_name ?? '';

            if($old = \App\Models\CrmMessage::where("msg_id", $mm->data->msgId)->first()){
                if(!$old->channel_name){
//                    getch("Update channel name: " . $chanelName . " for msg_id: " . $mm->data->msgId . "\n");
                    $old->channel_name = $chanelName;
                    $old->save();
                }
//                getch(" have msg ignore ");
                continue;
            }

//            getch("... " . $mm->data->msgId);


            $message = new \App\Models\CrmMessage();
            // Map JSON fields to model fields
            $message->content = $mm->data->content?? null;
            if(!$message->content){
                return;
            }

            if(isset($message->content->thumb) || is_object($message->content) || is_array($message->content)){
                $message->content = json_encode($message->content);
            }
//    $message->content = mb_strtolower($message->content);

            $message->channel_name = $chanelName;
            $message->type = $mm->type?? null;
            $message->action_id = $mm->data->actionId?? null;
            $message->action_id = $mm->data->actionId?? null;
            $message->msg_id = $mm->data->msgId?? null;
            $message->cli_msg_id = $mm->data->cliMsgId?? null;
            $message->msg_type = $mm->data->msgType?? null;
            $message->uid_from = $mm->data->uidFrom?? null;
            $message->id_to = $mm->data->idTo?? null;
            $message->d_name = $mm->data->dName?? null;
            $message->ts = $mm->data->ts?? null;
            $message->status = $mm->data->status?? null;

            $message->notify = $mm->data->notify?? null;
            $message->ttl = $mm->data->ttl?? null;
            $message->user_id_ext = $mm->data->userId?? null;
            $message->uin = $mm->data->uin?? null;
            $message->cmd = $mm->data->cmd?? null;
            $message->st = $mm->data->st?? null;
            $message->at = $mm->data->at?? null;
            $message->real_msg_id = $mm->data->realMsgId?? null;
            $message->thread_id = $mm->threadId?? null;
            $message->is_self = $mm->isSelf?? false;

            // Handle JSON objects by converting them to JSON strings
//            $message->property_ext = isset($mm->data->propertyExt ? json_encode($mm->data->propertyExt)) : null;
//            $message->params_ext = isset($mm->data->paramsExt ? json_encode($mm->data->paramsExt)) : null;

            // Set any additional fields or defaults
//    $message->name = 'Zalo Message';
//    $message->user_id = 1; // Set appropriate user ID or default
            $message->log = $mess; // Store the entire raw JSON

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($message->toArray());
//            echo "</pre>";
//            getch("...");
            // Save to database
            $message->save();
        }
    }
}
