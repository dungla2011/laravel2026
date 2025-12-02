<?php

namespace App\Models;

use LadLib\Common\clsDateTime2;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrExtraCostEmployee_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-extra-cost-employee';

    protected static $web_url_admin = '/admin/hr-extra-cost-employee';

    protected static $api_url_member = '/api/member-hr-extra-cost-employee';

    protected static $web_url_member = '/member/hr-extra-cost-employee';

    //public static $folderParentClass = HrExtraCostEmployeeFolderTbl::class;
    public static $modelClass = HrExtraCostEmployee::class;
    //public static $folderParentClass = HrSalaryMonthUserFolderTbl::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'month') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;

        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrSalaryMonthUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            $em = $user->email;
            if ($obj) {

            }
            $hre = HrEmployee::where('user_id', $user->id)->first();
            if ($hre) {
                return " <div class='div_user_id_info' data-code-pos='ppp16182471' style='font-size: small; padding: 3px'> <a target='_blank' title='$user->name' ".
                    "href='/admin/hr-employee/edit/$hre->id'>$hre->last_name $hre->first_name  </a> <div class='' style='font-size: x-small'> Mã NS: $user->id </div>".
                    "<a class='hide1' title='xem bảng chấm công riêng user này' target='_blank' ".
                    "href='/admin/hr-salary-month-user/report-times?month=$obj->month&user_id_set=$user->id'> <i class='fa fa-plus'></i></a> </div> ";
            }
        }
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        echo "<div class='pb-3'> <button type='button' class='btn btn-info btn-sm' href='/admin/hr-salary-month-user/report' id='show_report'> Bảng Lương Tổng Hợp </button> ";

        $mm = $this->_month();
        foreach ($mm as $id => $month) {
            if (! $id) {
                continue;
            }
            $month1 = explode('-', $month)[1].'-'.explode('-', $month)[0];
            echo " <a style='display: inline; padding-left: 10px' href='/admin/hr-extra-cost-employee?seby_s9=$month'>
            <button style='float: right; margin-left: 10px' class=' btn  btn-sm btn-warning'> $month1 </button></a> &nbsp; ";
        }

        echo '</div>';
    }

    public function _month($objData = null, $value = null, $field = null)
    {

        $mm = clsDateTime2::getArrayMonthBetWeenDates(time() - 60 * _NSECOND_DAY, time() + 3);

        $ret = [];
        $ret[0] = '---';
        foreach ($mm as $month) {
            $ret[$month] = $month;
        }

        //Nếu có obj có thì mới trả lại Key=>id
        //Nếu ko, nghĩa là trường hợp Get all để chọn
        if ($objData) {
            if (isset($ret[$value]) && $value) {
                return [$value => $ret[$value]];
            } else {
                return null;
            }
        }

        return $ret;

    }

    public function _sparam1($obj, $val, $field)
    {

        $user = User::find($obj->user_id);
        $uid = $obj->user_id;
        if ($user) {
            $hre = HrEmployee::where('user_id', $user->id)->first();
            if (! $hre) {
                return;
            }

            $nDayMonth = clsDateTime2::getEndDayOfMonth($obj->month.'-01');

            $job = HrSalary::find($hre->job_title);
            if ($job) {
                return " <div style='padding: 5px; font-size: small'>$job->salary_month</div>";
            }
        }

    }

    public function _sparam7($obj, $val, $field)
    {
        $user = User::find($obj->user_id);
        $uid = $obj->user_id;
        if ($user) {
            //Tính tổng giờ làm user trong tháng
            $mts = HrTimeSheet::where('user_id', $uid)->where('time_frame', '>=', $obj->month.'-01')->where('time_frame', '<=', $obj->month.'-31')->get();
            $nTotal = 0;
            if ($mts) {
                foreach ($mts as $ts) {
                    if ($ts->meal > 0 && strstr($ts->meal, '_')) {
                        $nTotal += explode('_', $ts->meal)[1];
                    }
                }
            }

            return " <div data-code-pos='ppp16382471' style='font-size: small; padding: 3px'>  ($nTotal bữa)</div> ";
        }
    }

    public function extraJsInclude()
    {
        ?>

        <style>
            .hide1 {
                display: none;
            }
            .div_user_id_info:hover > * {
                display: block;
            }
        </style>
        <script>

            $("#show_report").on('click', function (){

                let mid = clsTableMngJs.getSelectingCheckBox();
                console.log(" MID ", mid);

                if(!mid || mid.length == 0){
                    alert("Cần chọn các thành viên muốn hiển thị bảng lương!");
                    return;
                }

                let strUid = '';
                let strMonth = ''
                for(let id of mid){
                    strUid += $("input[data-field=user_id][data-id="+id+"]").val() + ",";
                    strMonth += $("input[data-field=month][data-id="+id+"]").val() + ",";
                }
                console.log(" StrUID ", strUid);
                console.log(" strMonth ", strMonth);

                let link = "/admin/hr-salary-month-user/report?uid_list=" + strUid + "&month_list=" + strMonth;
                window.open(link, "_blank");

            })

        </script>
        <?php
    }
    //...
}
