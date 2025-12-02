<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrOrgSetting_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-org-setting';

    protected static $web_url_admin = '/admin/hr-org-setting';

    protected static $api_url_member = '/api/member-hr-org-setting';

    protected static $web_url_member = '/member/hr-org-setting';

    //public static $folderParentClass = HrOrgSettingFolderTbl::class;
    public static $modelClass = HrOrgSetting::class;

    public static $disableAddItem = 1;

    public static $tangCaType = [
        0 => '---',
        1 => '-Không Đký-',
        2 => '<360h',
        3 => '<360h',
        4 => 'Lễ',
        5 => 'Tết',
    ];

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
            //HrOrgSetting edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        if ($field == 'num3') {
            //            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        return $objMeta;
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $k = $this->getSearchKeyField('job_title_id');
        $mJob = HrJobTitle::get();

        $x = request($k);

        echo "<div class='mb-1 select_org'> <button class='btn btn-default btn-sm mx-1' > Chức danh: </button> ";
        foreach ($mJob as $og) {
            $pad = 'btn-outline-primary';
            if ($x == $og->id) {
                $pad = 'btn-primary';
            }

            //$link = "/admin/hr-config-session-org-id-salary?$k=$og->id";
            $link = UrlHelper1::setUrlParamThisUrl($k, $og->id);
            echo "\n <a title='$og->name'  class='btn $pad btn-sm mx-1' href='$link'> ($og->id) $og->name  </a> ";
        }
        echo '</div>';

    }

    public function executeBeforeIndex($param = null)
    {
        $k = $this->getSearchKeyField('job_title_id');

        $jobId = request($k);

        if (! $jobId) {
            return -1;
        }

        $mm = HrOrgTree::all();
        foreach ($mm as $obj) {
            if (! HrOrgSetting::where('org_id', $obj->id)->where('job_title_id', $jobId)->first()) {
                $n = new HrOrgSetting();
                $n->org_id = $obj->id;
                $n->job_title_id = $jobId;
                $n->save();
            }
        }
        if (! HrOrgSetting::where('org_id', 0)->where('job_title_id', $jobId)->first()) {
            $n = new HrOrgSetting();
            $n->org_id = 0;
            $n->job_title_id = $jobId;
            $n->save();
        }
    }

    public function _org_id($obj, $val, $field)
    {
        $name = HrOrgTree::find($val)?->name;
        if (! $obj->org_id) {
            return "<div class='mx-2 '> <b> MẶC ĐỊNH </b></div>";
        }

        return "<div class='mx-2 '> $name ($obj->org_id) </div>";
    }

    //...
}
