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
class EventSendInfoLog_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/event-send-info-log';

    protected static $web_url_admin = '/admin/event-send-info-log';

    protected static $api_url_member = '/api/member-event-send-info-log';

    protected static $web_url_member = '/member/event-send-info-log';

    public static $titleMeta = 'Sự kiện :: Thống kê gửi tin từng thành viên';

    //public static $folderParentClass = EventSendInfoLogFolderTbl::class;
    public static $modelClass = EventSendInfoLog::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //EventSendInfoLog edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'parent_extra' || $field == 'parent_all') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_MULTI_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventSendInfoLogFolderTbl::joinFuncPathNameFullTree';
        }
        if ($field == 'content_sms' || $field == 'comment' ) {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
            //            $objMeta->join_func = 'App\Models\EventSendInfoLogFolderTbl::joinFuncPathNameFullTree';
        }

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/need_define';
            //            $objMeta->join_func = 'App\Models\EventSendInfoLogFolderTbl::joinFuncPathNameFullTree';
        }



        //Nếu không set thì lấy của parent default nếu có
        if (! $objMeta->dataType) {
            if ($ret = parent::getHardCodeMetaObj($field)) {
                return $ret;
            }
        }

        return $objMeta;
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_event_name'=>'event_infos.name',
            '_email'=>'event_user_infos.email',
            '_first_name'=>'event_user_infos.first_name',
            '_last_name'=>'event_user_infos.last_name',
            '_organization'=>'event_user_infos.organization',
        ];
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {

        if(Helper1::isMemberModule()){
            $mEventId = EventInfo::getEventIdListInDeparmentOfUser(getCurrentUserId());
            $x->whereIn('event_send_info_logs.event_id',  $mEventId);
        }

        return $x->leftJoin('event_infos', 'event_id', '=', 'event_infos.id')
            ->leftJoin('event_user_infos', 'event_user_id', '=', 'event_user_infos.id')
            ->addSelect([
                'event_user_infos.email AS _email',
                'event_infos.name as _event_name',
                'event_user_infos.first_name as _first_name',
                'event_user_infos.last_name as _last_name',
            ]);
    }

    public function getFullSearchJoinField()
    {
        return [
            'event_user_infos.first_name'  => "like",
            'event_user_infos.last_name'  => "like",
            'event_user_infos.email'  => "like",
        ];
    }


    public function executeBeforeIndex($param = null)
    {
        //Tìm các id của EventInfo được tạo bở userid này, sau đó
        $user_id = getCurrentUserId();
        $mmEv = EventInfo::where('user_id', $user_id)->get();
        foreach ($mmEv as $ev) {
            //ở EventAndUser, hãy SET user_id này cho mọi EventRegister có các event_id vừa tìm được, nếu userid khác
//            EventRegister::where('event_id', $ev->id)->where("user_id",'!=', $user_id)->update(['user_id' => $user_id]);
            EventSendInfoLog::where('event_id', $ev->id)->update(['user_id' => $user_id]);
        }
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

//        $mmEv = EventInfo::where("status", 1)->get();
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

        ?>

        <div class="row px-2 mb-1">
            <div class="col-sm-12 p-1 px-2" style="font-size: 80%; background-color:lavender;">

                - Note: Với các SMS đã gửi mà chưa thành công, có thể đặt lại Số lần gửi = 0 để App SMS thử <b>gửi lại</b> . Số lần gửi lại tối đa là 3 lần.
                Sms chưa gửi được có thể do bị chặn vì có Link, Sim hết tiền... Các tin đã ra lệnh quá 24h sẽ không được gửi

            </div>

        </div>
        <?php

    }


    public function extraContentIndexButton1($v1 = null, $v2 = null, $v3 = null)
    {


        $searchKey = $this->getSearchKeyField('status');
        $searchKey2 = $this->getSearchKeyField('type');
        $link = UrlHelper1::setUrlParamThisUrl([$searchKey => 0, $searchKey2 => 'sms']);

        ?>
        <a href="<?php echo $link ?>">
        <button class="btn btn-outline-danger btn-sm float-right mt-2 ml-3"> Lọc SMS Chưa/Lỗi gửi </button>
        </a>
        <?php
    }

    public function extraCssIncludeEdit()
    {
        ?>

        <style>
            div[data-field='content_sms'] textarea {
                height: 300px;
            }
        </style>
<?php
    }

    public function _event_user_id($obj, $valIntOrStringInt, $field)
    {
        $objU = EventUserInfo::find($valIntOrStringInt);
        if(!$objU)
            return;
        $ret = "<div data-code-pos='ppp17121128454641' style='font-size: small; padding: 5px; color: royalblue'>";
        $ret .= "$objU->last_name $objU->first_name <br>$objU->email <br> $objU->phone ";
        $ret .= '</div>';

        return $ret;
    }

    public function _event_id($obj, $valIntOrStringInt, $field)
    {
        if ($objU = EventInfo::find($valIntOrStringInt)) {
            $ret = "<div data-code-pos='ppp 1'style='font-size: small; padding: 5px; color: royalblue'>";
            $ret .= "$objU->name";
            $ret .= '</div>';

            return $ret;
        }

        return $valIntOrStringInt;
    }

    //...
}
