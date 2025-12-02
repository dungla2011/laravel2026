<?php

namespace App\Models;

use LadLib\Common\clsDateTime2;
use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrSampleTimeEvent_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-sample-time-event';

    protected static $web_url_admin = '/admin/hr-sample-time-event';

    protected static $api_url_member = '/api/member-hr-sample-time-event';

    protected static $web_url_member = '/member/hr-sample-time-event';

    //public static $folderParentClass = HrSampleTimeEventFolderTbl::class;
    public static $modelClass = HrSampleTimeEvent::class;

    public static $_num4_luong_ca;

    public static $_num4_luong_thang;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }

        if ($field == 'num1' || $field == 'num4' || $field == 'num3') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //HrSampleTimeEvent edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    //Phí tăng ca tính theo giờ nào:
    public static function configPhiTangCa()
    {
        return 'n_12';
    }

    public function _num1($obj, $field, $val)
    {
        $mm = [0 => '0 bữa',
            1 => '1 Bữa',
            2 => '2 Bữa',
            3 => '3 Bữa',
        ];

        return $mm;
    }

    public static function _num3()
    {
        $mm = [0 => 'muộn'];
        $mm[30] = '30p';
        $mm[60] = '60p';
        $mm[90] = '1.5h';
        $mm[120] = '2h';
        $mm[150] = '2.5h';
        $mm[180] = '3h';

        return $mm;
    }

    public static function _num4($obj = null, $field = null, $val = null)
    {

        $mm = [0 => '-Ca-'];

        $m1 = HrSessionType::orderBy('orders', 'desc')->get();
        foreach ($m1 as $obj) {
            $h = $obj->hour;
            if (! $h) {
                $h = 1;
            }
            $name = htmlentities($obj->name);
            //$mm[htmlentities($obj->name)."_".$h] = $name;
            $mm[$obj->id] = $name;
        }

        //
        //        $mm = [0=>'-Ca-'];
        //        $mm["n_12"] = 'N';
        //        $mm["d_12"] = 'Đ';
        //        $mm["n_10"] = 'N10';
        //        $mm['d_10'] = 'D10';
        //        $mm['hc1_8'] = 'HC1';
        ////        $mm['hc2_8'] = 'HC2';
        ////        $mm['hc3_8'] = 'HC3';
        //        $mm['hc_8'] = 'HC';
        //        $mm['n_4'] = 'HC';
        //        $mm['n10_5'] = 'HC';
        //
        //        $mm['c_24'] = 'C24';
        //        $mm['c1_24'] = '24';
        //
        //
        //        $mm['le_8'] = 'L8';
        //        $mm['le_10'] = 'L10';
        //        $mm['le_12'] = 'L12';
        //        $mm['le_24'] = 'L24';
        //
        ////        $mm['tet_-1'] = 'T-1';
        ////        $mm['le_-1'] = 'L-1';
        //
        //        $mm['tet_8'] = 'T8';
        //        $mm['tet_10'] = 'T10';
        //        $mm['tet_12'] = 'T12';
        //        $mm['tet_24'] = 'T24';

        return $mm;
    }

    //Lương ca phụ thuộc lương tháng
    public static function _num4_luong_ca($userId, $month, $orgId = 0)
    {

        $nDayMonth = clsDateTime2::getEndDayOfMonth($month);

        $nDayMonth = HrCommon::getNSessionCaHrEmployeeNeedWork($userId, $month);

        $mm = [];
        foreach (self::_num4() as $key => $val) {
            if ($key) {
                $mm[$key] = (self::_num4_luong_thang($userId, $month, $orgId)[$key] ?? 0) / $nDayMonth;
            }//232258;
        }

        return $mm;
    }

    //Lương tháng phụ thuộc cả vào kiểu số ngày làm
    //Cần month, để nhân lên nếu lương theo giờ, chứ ko phải lương theo tháng
    public static function _num4_luong_thang($userid, $month, $orgId)
    {

        $strId = $userid.'-'.$month.'-'.$orgId;
        if (self::$_num4_luong_thang[$strId] ?? 0) {
            return self::$_num4_luong_thang[$strId];
        }
        $mm = [];
        $user = HrEmployee::where('user_id', $userid)->first();
        if (! $user) {
            return null;
            loi("Not user ($userid)?");
        }
        $jobTitle = $user->job_title;
        //        $orgId = $user->parent_id;

        $m1 = HrConfigSessionOrgIdSalary::where('org_id', $orgId)->where('job_title_id', $jobTitle)->get();

        $nDayMonth = clsDateTime2::getEndDayOfMonth($month);

        if ($m1) {
            foreach ($m1 as $obj) {
                //            if($user->job_title == 4){
                //                $mm[$obj->session_type_id] = $obj->num2;
                //            }
                //            else
                $mm[$obj->session_type_id] = $obj->salary_month;
                if (! $obj->salary_month && $obj->num3) {
                    //Xem số giờ của session;
                    $nHour = HrCommon::getCacheAllSessionType()[$obj->session_type_id]['hour'];
                    $mm[$obj->session_type_id] = $obj->num3 * $nHour * $nDayMonth;
                }
            }
        }
        self::$_num4_luong_thang[$strId] = $mm;

        return $mm;
    }

    public static function tinhToan($mDataUidAndTime, $uid, &$mmTotalAll = [])
    {
        $mTotal = [];
        $mTotal['so_bua_an'] = 0;
        $mTotal['so_ca'] = 0;
        $mTotal['so_gio_ca'] = 0;

        $mmTotalAll['so_bua_an'] = $mmTotalAll['so_bua_an'] ?? 0;
        $mmTotalAll['so_ca'] = $mmTotalAll['so_ca'] ?? 0;

        if ($mDataUidAndTime[$uid] ?? '') {
            foreach ($mDataUidAndTime[$uid] as $obj) {

                //                echo " |$obj->num4| xxx " . strpos($obj->num4 , "_") ." //";
                //                echo "<br/>\n";
                $nCaHour = strpos($obj->num4, '_') ? explode('_', $obj->num4)[1] : 0;
                $nCa = strpos($obj->num4, '_') ? 1 : 0;
                $mTotal['so_ca'] += $nCa;
                $mTotal['so_gio_ca'] += $nCaHour;
                $mTotal['so_bua_an'] += $obj->num1;

                $mmTotalAll['so_ca'] += $nCa;
                $mmTotalAll['so_bua_an'] += $obj->num1;
            }
        }
        echo '<pre>';
        print_r($mTotal);
        echo '</pre>';

        return $mTotal;
    }
}
