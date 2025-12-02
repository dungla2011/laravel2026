<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrConfigSessionOrgIdSalary_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-config-session-org-id-salary';

    protected static $web_url_admin = '/admin/hr-config-session-org-id-salary';

    protected static $api_url_member = '/api/member-hr-config-session-org-id-salary';

    protected static $web_url_member = '/member/hr-config-session-org-id-salary';

    //public static $folderParentClass = HrConfigSessionOrgIdSalaryFolderTbl::class;
    public static $modelClass = HrConfigSessionOrgIdSalary::class;

    public static $disableAddItem = 1;

    public static $limitRecord = 40;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;

        }
        if ($field == 'num1') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrConfigSessionOrgIdSalary edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }
        if ($field == 'num3') {
            //            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        return $objMeta;
    }

    public function _num1($obj, $val, $field)
    {
        return [0 => '-Chọn-',
            //            1=>'-Lễ-',
            2 => 'Tính lương mặc định cho thêm giờ'];

    }

    public function _org_id($obj, $val, $field)
    {
        $name = HrOrgTree::find($val)?->name;

        return "<div class='mx-2 text-center'> $name </div>";
    }

    //    function _name($obj, $val, $field){
    //        $name = HrSampleTimeEvent_Meta::_num4()[$val] ?? '_error_not_found_'.$val;
    //        return "<span class='mx-2 '> $name </span>" ;
    //    }

    public function _session_type_id($obj, $val, $field)
    {
        //        $name = HrSampleTimeEvent_Meta::_num4()[$obj] ?? '_error_not_found_'.$val;
        $name = HrSessionType::find($val)->name ?? '_error_not_found_'.$val;
        $nH = HrSessionType::find($val)->hour ?? 0;

        return "<span title='$val' class='mx-2 ' data-code-pos='ppp16919861239011'> $name  <span style='color: #ccc'>($nH giờ) </span> </span>";
    }

    public function executeBeforeIndex($param = null)
    {

        $time = microtime(1);
        $k = $this->getSearchKeyField('org_id');

        $k2 = $this->getSearchKeyField('job_title_id');

        //        $m1 = HrSampleTimeEvent_Meta::_num4();

        if ($orgId = request($k)) {
            if (! HrOrgTree::find($orgId)) {
                bl("Not found Orgid: $orgId");

                return -1;
            }
        } else {
            return -1;
        }

        if ($jobId = request($k2)) {
            if (! HrJobTitle::find($jobId)) {
                bl("Not found jobId: $jobId");

                return -1;
            }
        } else {
            return -1;
        }

        $m1 = HrSessionType::all();
        //        $mm =  array_column($m1->toArray(), 'id');
        $orgList = null;
        //Tìm ít nhất một bản ghi có chứa 1 phần tử trong mm
        if ($m1) {
            foreach ($m1 as $hr) {
                //nếu ko có thì insert ngay:
                if (! HrConfigSessionOrgIdSalary::where(['org_id' => $orgId, 'job_title_id' => $jobId, 'session_type_id' => $hr->id])->first()) {
                    $obj = new HrConfigSessionOrgIdSalary();
                    $obj->org_id = $orgId;
                    $obj->job_title_id = $jobId;
                    $obj->session_type_id = $hr->id;
                    $obj->save();
                }
            }
        }

        //       dump("<br/>\n DTIME2 = " . (microtime(1) - $time));
        return 1;
    }

    //...

    public function extraJsInclude()
    {
        ?>

        <style>
            .input_value_to_post.readonly.org_id{
                display: none;
            }
            .input_value_to_post.session_type_id{
                display: none;
            }
            .select_org a, .select_org button{
                display: inline-block;
                margin-bottom: 5px;
                font-size: small;
                width: 100px;
                overflow: hidden;;
                white-space: nowrap;
            }
        </style>
<?php
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $time = microtime(1);
        $k = $this->getSearchKeyField('org_id');
        $mOrg = HrOrgTree::get();

        $x = request($k);
        $link = null;
        echo "<div class='mb-1 select_org'> <button class='btn btn-info btn-sm mx-1' > Chọn nhánh: </button> ";
        foreach ($mOrg as $og) {
            $pad = 'btn-outline-info';
            if ($x == $og->id) {
                $pad = 'btn-info';
            }
            //$link = "/admin/hr-config-session-org-id-salary?$k=$og->id";
            $link = UrlHelper1::setUrlParam($link, $k, $og->id);
            echo "\n <a title='$og->name' class='btn $pad btn-sm mx-1  text-left' href='$link'> ($og->id) $og->name  </a> ";
        }
        echo '</div>';

        $k = $this->getSearchKeyField('job_title_id');
        $mJob = HrJobTitle::get();

        $x = request($k);

        echo "<div class='mb-1 select_org'> <button class='btn btn-primary btn-sm mx-1' > Chức danh: </button> ";
        foreach ($mJob as $og) {
            $pad = 'btn-outline-primary';
            if ($x == $og->id) {
                $pad = 'btn-primary';
            }

            //$link = "/admin/hr-config-session-org-id-salary?$k=$og->id";
            $link = UrlHelper1::setUrlParamThisUrl($k, $og->id);
            echo "\n <a title='$og->name'  class='btn $pad btn-sm mx-1  text-left' href='$link'> ($og->id) $og->name  </a> ";
        }
        echo '</div>';

        //        echo "<br/>\n DTIME = " . (microtime(1) - $time);
    }
}
