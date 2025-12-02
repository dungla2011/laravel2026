<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class EventSendAction_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/event-send-action';

    protected static $web_url_admin = '/admin/event-send-action';

    protected static $api_url_member = '/api/member-event-send-action';

    protected static $web_url_member = '/member/event-send-action';

    //public static $folderParentClass = EventSendActionFolderTbl::class;
    public static $modelClass = EventSendAction::class;

    public static $titleMeta = "Danh sách Ra Lệnh gửi tin Sự kiện";

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status' || $field == 'done') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //EventSendAction edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'parent_extra' || $field == 'parent_all') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventSendActionFolderTbl::joinFuncPathNameFullTree';
        }



        if ($field == 'list_uid_send_done') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventSendActionFolderTbl::joinFuncPathNameFullTree';
        }

        if (! $objMeta->dataType) {
            if ($ret = parent::getHardCodeMetaObj($field)) {
                return $ret;
            }
        }

        return $objMeta;
    }
    //...


    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isMemberModule()){
            $mEventId = EventInfo::getEventIdListInDeparmentOfUser(getCurrentUserId());
            $x->whereIn('event_send_actions.event_id',  $mEventId);
        }
    }

    function _count_success($obj, $val, $field)
    {
        if($val)
            return;

        //Tìm các EventSendInfoLog có session_id bằng id này
        $count = EventSendInfoLog::where('session_id', $obj->id)->where('status',1)->count();
//        $count = EventSendInfoLog::where('session_id', $obj->id)->whereNotNull('done_at')->count();

        //Số EventUserInfo của Event:
        $count2 = EventAndUser::where('event_id', $obj->event_id)->count();

        $meta = EventSendInfoLog::getMetaObj();
        $ss = $meta->getSearchKeyField('event_id');
        $type = $meta->getSearchKeyField('type');


        $detail = "<a href='/admin/event-send-info-log?$ss=$obj->event_id&$type=sms' target='_blank'>Xem danh sách từng lệnh</a>";

        return "<div data-code-pos='ppp17339955857451' style=' font-size: 90%; padding-left: 10px'> $count  / $count2 <br> $detail  </div> ";
    }


    public function extraCssIncludeEdit()
    {
        ?>

        <style>
            div[data-field='log'] textarea.text_area_edit {

                height: 400px;
            }
        </style>

<?php
    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }
    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $uid = getCurrentUserId();
        if(Helper1::isMemberModule()){
//            $mmEv = EventInfo::where('user_id', $uid)->latest()->get();
            $mmEv = EventInfo::getEventIdListInDeparmentOfUser($uid, 1);
        }
        else
            $mmEv = EventInfo::latest()->get();

        $linkOpt = UrlHelper1::getUriWithoutParam();
        $sname = $this->getSNameFromField('event_id');
        $key = "seby_$sname";

        EventInfo::getHtmlSelectEvent($linkOpt, $mmEv, $key);

    }

    public function getSqlOrJoinExtraEdit(\Illuminate\Database\Eloquent\Builder &$x = null, $params = null)
    {
        //Kiem tra xem User hien tai co quyen khong:
        EventInfo::checkEventBelongUser($params['id'], self::$modelClass);

    }

}
