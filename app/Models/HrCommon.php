<?php

namespace App\Models;

use LadLib\Common\clsDateTime2;

//Đối tượng chung tập trung sử lý các Hr Task
class HrCommon
{
    public $hrEmploy;

    public static $hrAllUserTimeSheet; //[userid][month] => []

    public static $tmpCacheArrayAllSessionType = [];

    public static $tmpCacheSessionType = [];

    public static $tmpCacheOrgSetting = []; //[$orgId."-".$job_title]

    public static $tmpCacheHrMember = [];

    public static $tmpCacheOrgDefaultSalary = []; //Mặc định giá giờ của org

    public function _construct($uid)
    {
        $this->user_id = $uid;
        $this->hrEmploy = HrEmployee::where('user_id', $uid);
    }

    public static function getSoGioCa($orgId = 0)
    {

        return 12;
    }

    /**
     * @param  int  $orgId
     * @return HrLuongTmp
     */
    public static function getTinhLuongUserMonthOrgId($uid, $month, $orgId = 0)
    {

        $tongGioCaTrongThang = clsDateTime2::getEndDayOfMonth($month) * self::getSoGioCa();

        self::getAllUserInfoInMonth($uid, $month, $orgId);

        $luong = new HrLuongTmp();
        $luong->orgId = $orgId;
        $luong->uid = $uid;

        //        $luong->objEmployee = HrEmployee::where('user_id', $uid)->first();

        $luong->month = $month;
        $luong->tongCaDaLam = self::getTotalCaUserMonth($uid, $month, $orgId);
        $luong->tongGioCa = self::getTotalHourSessionUserMonth($uid, $month, $orgId);
        $luong->tongBuaAn = self::getTotalMealUserMonth($uid, $month, $orgId);
        $luong->tongTienAn = self::getTotalMealUserMonth($uid, $month, $orgId) * HrCommon::getPriceMeal($orgId, $uid);

        $luong->tongGioCaLamThem = self::getTotalHourPlusUserMonthCa($uid, $month, $orgId);

        $luong->tongGioLeLamThem = self::getTotalHourPlusUserMonthLe($uid, $month, $orgId);

        $luong->tongGioLamThem = $luong->tongGioCaLamThem + $luong->tongGioLeLamThem;

        $luong->tongGioCaLamThemTinhLuongLamThem = $luong->tongGioCaLamThem;
        if ($luong->tongGioCa < $tongGioCaTrongThang) {
            $luong->tongGioCaLamThemBuCaThuong = $tongGioCaTrongThang - $luong->tongGioCa > $luong->tongGioCaLamThem ? $luong->tongGioCaLamThem : $tongGioCaTrongThang - $luong->tongGioCa;
            $luong->tongGioCaLamThemTinhLuongLamThem = $luong->tongGioCaLamThem - $luong->tongGioCaLamThemBuCaThuong;
        }

        $luong->tongCaPhaiLamTrongThang = self::getNSessionCaHrEmployeeNeedWork($uid, $month);

        $luong->tongCaDiMuon = self::getTotalLateCaUserMonth($uid, $month, $orgId);

        $mmGioMuon = self::getTotalLateHourUserMonth($uid, $month, $orgId);

        $luong->tongGioDiMuon = $mmGioMuon['all'];
        $luong->tongGioLeDiMuon = $mmGioMuon['muon_le'];
        $luong->tongGioDiMuon = $mmGioMuon['muon_thuong'];

        $mmTienDiMuon = self::getTotalLateHourUserMoney($uid, $month, $orgId);

        $luong->tongTienDiMuonAll = $mmTienDiMuon['all'];
        $luong->tongTienLeDiMuon = $mmTienDiMuon['muon_le'];
        $luong->tongTienDiMuon = $mmTienDiMuon['muon_thuong'];

        $luong->tongTienTruDiMuon = '';

        $luong->tongLuongCa = (self::getTongLuongCa($uid, $month, $orgId));
        $luong->mmPhanLoaiCa = self::getPhanLoaiCaLamViec($uid, $month, $orgId);

        $luong->tongTienAn = self::getPriceMeal($orgId, $uid) * $luong->tongBuaAn;

        $luong->tongLuongLamThem =
              $luong->tongGioLeLamThem * HrCommon::getGiaGioTangCaLe($orgId, $uid, $luong->tongGioCa + $luong->tongGioLamThem, $month)
            + $luong->tongGioCaLamThemTinhLuongLamThem * HrCommon::getGiaGioTangCa($orgId, $uid, $luong->tongGioCa + $luong->tongGioLamThem, $month)
        + $luong->tongGioCaLamThemBuCaThuong * HrCommon::getGiaGioTangCa($orgId, $uid, $luong->tongGioCa, $month);

        if ($luong->tongCaPhaiLamTrongThang <= $luong->tongCaDaLam) {
            $luong->thuongChuyenCan = HrCommon::getThuongChuyenCan($orgId, $uid);
        }

        $o2 = HrUserExpense::where(['user_id' => $uid, 'cat1' => $orgId, 'time_frame' => $month])->first();

        if ($o2) {
            $o2 = (object) $o2->toArray();
            foreach ($o2 as $field => $val) {
                if (! $val) {
                    continue;
                }
                if (str_starts_with($field, 'num_')) {
                    $luong->tongTruExpense += $val;
                } else {
                    if (str_starts_with($field, 'num')) {
                        $luong->tongCongExpense += $val;
                    }
                }
            }
        }

        $luong->tongTru = $luong->tongTienAn + $luong->tongTienDiMuonAll + $luong->tongTruExpense;
        $luong->tongCong = $luong->tongLuongCa + $luong->tongLuongLamThem + $luong->thuongChuyenCan + $luong->tongCongExpense;
        $luong->tongLuongFinal = $luong->tongCong - $luong->tongTru;

        return $luong;
    }

    public static function getCacheOrgSetting($orgId, $job_title)
    {
        if (isset(self::$tmpCacheOrgSetting[$orgId.'-'.$job_title])) {
            return self::$tmpCacheOrgSetting;
        }
        $m1 = HrOrgSetting::where('org_id', $orgId)->where('job_title_id', $job_title)->first()?->toArray();
        $m0 = HrOrgSetting::where('org_id', 0)->where('job_title_id', $job_title)->first()?->toArray();
        self::$tmpCacheOrgSetting[$orgId.'-'.$job_title] = $m1;
        self::$tmpCacheOrgSetting['0-'.$job_title] = $m0;

        return self::$tmpCacheOrgSetting;
    }

    //Số Ca làm việc tháng của user
    public static function getNSessionCaHrEmployeeWorked($uid, $monthY)
    {

    }

    //Số Ca làm việc tháng của user
    public static function getNSessionCaHrEmployeeNeedWork($uid, $monthY)
    {

        if (! $monthY) {
            loi('need input valid month!');
        }

        $nDayMonth = clsDateTime2::getEndDayOfMonth($monthY);
        $gTime = self::getCacheHrEmployee($uid)->group_time ?? 0;
        if (! $gTime) {
            return $nDayMonth;
        }
        //loi("getNSessionCaHrEmployeeNeedWork: Not found uid, month: $uid/$month");

        [$year, $month] = explode('-', $monthY);

        $m1 = clsDateTime2::countGetNumberDayWeekInMonth($month, $year);
        $nSun = $m1[0];
        $nSat = $m1[6];

        if ($gTime == 2) {
            return $nDayMonth - $nSun - $nSat;
        }
        if ($gTime == 3) {
            return $nDayMonth - $nSun - $nSat / 2;
        }
        if ($gTime == 4) {
            return $nDayMonth - $nSun;
        }

        return $nDayMonth;
    }

    public static function getCacheHrEmployee($uid)
    {
        if (isset(self::$tmpCacheHrMember[$uid])) {
            return self::$tmpCacheHrMember[$uid];
        }
        if ($u = HrEmployee::where('user_id', $uid)->first()) {
            $ret = json_decode(json_encode($u->toArray()));

            return self::$tmpCacheHrMember[$uid] = $ret;
        }

        return null;
    }

    public static function getSessionTypeCacheArray()
    {
        if (self::$tmpCacheArrayAllSessionType) {
            return self::$tmpCacheArrayAllSessionType;
        }
        $m1 = HrSessionType::all();
        foreach ($m1 as $item) {
            self::$tmpCacheArrayAllSessionType[$item->id] = json_decode(json_encode($item));
        }

        return self::$tmpCacheArrayAllSessionType;
    }

    public static function getTongLuongCa($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        $mret = [];
        $ttLuongCa = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num4) {
                    $ttLuongCa += HrSampleTimeEvent_Meta::_num4_luong_ca($uid, $month, $orgId)[$obj->num4] ?? 0;
                }
            }
        }

        return $ttLuongCa;
    }

    public static function getPhanLoaiCaLamViec($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        $mret = [];
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num4) {
                    $mret[$obj->num4] = $mret[$obj->num4] ?? 0;
                    $mret[$obj->num4]++;
                }
            }
        }

        return $mret;
    }

    //Là giá giờ theo ngày mặc định, kiểu num1 = 2
    //https://alc.mytree.vn/admin/hr-config-session-org-id-salary
    public static function getGiaGioMacDinh($orgId, $month)
    {
        $maxDateMonth = clsDateTime2::getEndDayOfMonth($month);
        $hr = HrConfigSessionOrgIdSalary::where('org_id', $orgId)->where('num1', 2)->first();
        if ($hr) {
            $luongThang = $hr->salary_month;
            if ($hrt = HrSessionType::find($hr->session_type_id)) {
                $nHour = $hrt->hour;
                if ($nHour) {
                    return $luongThang / $maxDateMonth / $nHour;
                }
            }
        }

        return 0;
    }

    public static function getGiaGioTangCaLe($orgId, $uid, $nHourDid = 0)
    {
        $jobId = HrEmployee::where('user_id', $uid)->first()->job_title ?? 0;

        return $price = self::getCacheOrgSetting($orgId, $jobId)[$orgId.'-'.$jobId]['num5'] ?? 0;
    }

    public static function getGiaGioTangCa($orgId, $uid, $nHourDid, $month)
    {

        $jobId = HrEmployee::where('user_id', $uid)->first()->job_title ?? 0;

        $tongGioCaTrongThang = clsDateTime2::getEndDayOfMonth($month) * self::getSoGioCa();

        $price = self::getCacheOrgSetting($orgId, $jobId)[$orgId.'-'.$jobId]['num7'] ?? 0;
        if ($price) {
            return $price;
        }

        if ($nHourDid <= $tongGioCaTrongThang) {
            $price = self::getCacheOrgSetting($orgId, $jobId)[$orgId.'-'.$jobId]['num3'] ?? 0;
            if (! $price) {
                $price = self::getCacheOrgSetting($orgId, $jobId)['0-'.$jobId]['num3'] ?? 0;
            }
        }

        if ($nHourDid > $tongGioCaTrongThang) {
            $price = self::getCacheOrgSetting($orgId, $jobId)[$orgId.'-'.$jobId]['num4'] ?? 0;
            if (! $price) {
                $price = self::getCacheOrgSetting($orgId, $jobId)['0-'.$jobId]['num4'] ?? 0;
            }
        }

        return $price;

        //        $maxDateMonth = clsDateTime2::getEndDayOfMonth($month);
        //        $cf = HrSampleTimeEvent_Meta::configPhiTangCa();
        //        return HrSampleTimeEvent_Meta::_num4_luong_thang($orgId)[$cf] ?? 0 / $maxDateMonth / 12;
    }

    //Month: '2023-01', '2022-10' ...
    public static function getAllUserInfoInMonth($uid, $month, $orgId = 0)
    {
        if (! $uid || ! $month) {
            return;
        }

        $index = $uid.'-'.$orgId;

        if (isset(self::$hrAllUserTimeSheet[$index]) && isset(self::$hrAllUserTimeSheet[$index][$month])) {
            return self::$hrAllUserTimeSheet[$index][$month];
        }

        if (! self::$hrAllUserTimeSheet) {
            self::$hrAllUserTimeSheet = [];
        }
        if (! isset(self::$hrAllUserTimeSheet[$index])) {
            self::$hrAllUserTimeSheet[$index] = [$month];
        }

        $m1 = HrSampleTimeEvent::where(['user_id' => $uid])->where('time_frame', '>=', $month.'-01')->where('time_frame', '<', $month.'-32');
        if ($orgId) {
            $m1->where('cat1', $orgId);
        }

        $m1 = $m1->get();

        return self::$hrAllUserTimeSheet[$index][$month] = json_decode(json_encode($m1->toArray()));
    }

    public static function getTotalMealUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                $tt += $obj->num1;
            }
        }

        return $tt;
    }

    public static function getThuongChuyenCan($orgId, $uid)
    {
        $objEmp = HrEmployee::where('user_id', $uid)->first();

        return HrCommon::getCacheOrgSetting($orgId, $objEmp?->job_title)[$orgId.'-'.$objEmp?->job_title]['num10'] ?? 0;
    }

    public static function getPriceMeal($orgId, $uid)
    {

        $hrE = HrEmployee::where('user_id', $uid)->first();
        if (! $hrE) {
            return 0;
        }
        //            loi("getPriceMeal : Not found uid: $uid ");

        $jobId = $hrE->job_title;

        $priceM = HrOrgSetting::where('org_id', $orgId)->where('job_title_id', $jobId)->first()?->num1;
        //Lay mac dinh:
        if (! $priceM) {
            $priceM = HrOrgSetting::where('org_id', 0)->where('job_title_id', $jobId)->first()?->num1;
        }

        return $priceM;
    }

    //n_session sẽ có dạng <name>_x_y, với name là tên gợi nhớ, x là số giờ của ca, y là số ca được tính
    public static function getTotalCaUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num4 > 0) {

                    if (self::getCacheAllSessionType()[$obj->num4]['hour'] ?? 0) {
                        $tt += 1;
                    }

                    //&& strstr($obj->num4, '_')){
                    //$mm = explode('_', $obj->num4);
                    //<name>_x_y , số y là [2], được coi là số cả nếu có
                    //                if($mm && isset($mm[2]) && is_numeric($mm[2]))
                    //                    $tt+= $mm[2];
                    //                else

                }
            }
        }

        return $tt;
    }

    public static function getCacheAllSessionType()
    {

        if (self::$tmpCacheSessionType) {
            return self::$tmpCacheSessionType;
        }
        $mm = HrSessionType::all();
        $m1 = [];
        //$tmpCacheOrgSessionConfig
        foreach ($mm as $obj) {
            $m1[$obj->id] = $obj->toArray();
        }

        return self::$tmpCacheSessionType = $m1;
    }

    public static function getTotalHourSessionUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;

        if ($mm) {
            foreach ($mm as $obj) {

                if ($obj->num4) {
                    $tt += self::getCacheAllSessionType()[$obj->num4]['hour'];
                }
            }
        }

        return $tt;
    }

    public static function getTotalLateCaUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num3) {
                    $tt++;
                }
            }
        }

        return $tt;
    }

    public static function getTotalLateHourUserMoney($uid, $month, $orgId = 0)
    {
        $nH = self::getTotalLateHourUserMonth($uid, $month, $orgId);
        $hMuonLe = $nH['muon_le'];
        $hMuonThuong = $nH['muon_thuong'];

        $jobId = HrEmployee::where('user_id', $uid)->first()->job_title ?? 0;

        //        $priceThuong = self::getCacheOrgSetting($orgId, $jobId)[$orgId."-".$jobId]['num8'];
        //        $priceLe = self::getCacheOrgSetting($orgId, $jobId)[$orgId."-".$jobId]['num9'];

        $cacheMM = self::getCacheOrgSetting($orgId, $jobId);
        $priceThuong = $cacheMM[$orgId.'-'.$jobId]['num8'] ?? 0;
        $priceLe = $cacheMM[$orgId.'-'.$jobId]['num9'] ?? 0;

        return ['muon_le' => $hMuonLe * $priceLe, 'muon_thuong' => $priceThuong * $hMuonThuong, 'all' => $hMuonLe * $priceLe + $priceThuong * $hMuonThuong];

    }

    public static function getTotalLateHourUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        $ret = ['muon_le' => 0, 'muon_thuong' => 0];
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num3) {
                    $tt += $tmp1 = $obj->num3 / 60;
                    //HrSessionType:: $obj->num3
                    if ($type = HrSessionType::find($obj->num4)) {
                        if ($type->num1 == 1) {
                            $ret['muon_le'] += $tmp1;
                        } else {
                            $ret['muon_thuong'] += $tmp1;
                        }
                    }
                }
            }
        }
        $ret['all'] = $tt;

        return $ret;
    }

    public static function getTotalHourPlusUserMonth($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num2 > 0) {
                    $tt += $obj->num2;
                    if (HrCommon::getSessionTypeCacheArray()[$obj->num4]->num1 ?? 0) {
                        echo "<br/>\n Num4 = $obj->num4 ($uid) ";
                        echo "<br/>\n Ca lễ OK! ";
                    }
                }
            }
        }

        return $tt;
    }

    public static function getTotalHourPlusUserMonthLe($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num2 > 0) {
                    if (HrCommon::getSessionTypeCacheArray()[$obj->num4]->num1 ?? 0) {
                        $tt += $obj->num2;
                    }
                }
            }
        }

        return $tt;
    }

    public static function getTotalHourPlusUserMonthCa($uid, $month, $orgId = 0)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month, $orgId);
        $tt = 0;
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->num2 > 0) {
                    //if(HrCommon::getSessionTypeCacheArray()[$obj->num4]->num1 ?? 0)
                    //                    if(isset(HrCommon::getSessionTypeCacheArray()[$obj->num4]) && isset(HrCommon::getSessionTypeCacheArray()[$obj->num4]->num1))
                    //Todo: ... sao trên ko chạy?
                    //https://hr.mytree.vn/admin/hr-salary-month-user/report2?month=2023-07&tree_id=7&startMonth=2023-07
                    if (isset(HrCommon::getSessionTypeCacheArray()[$obj->num4])) {
                        if (! HrCommon::getSessionTypeCacheArray()[$obj->num4]->num1) {
                            $tt += $obj->num2;
                        }
                    }
                }
            }
        }

        return $tt;
    }

    //Lấy ra 1 ngày của tháng trong time sheet có num4 trong đó
    //Nếu ko thấy trả về 8 (mặc định 8h/ngày)
    //Chú ý: nếu user làm ở nhiều mục tiêu, thì vẫn sẽ bị tính theo mục tiêu đầu tiên trong tháng
    //Nên có thể sẽ phải sử dụng cấu hình số Giờ theo từng mục tiêu (org id)
    //khi đó ko dùng hàm này được, mà sẽ có cho từng ngày, ví mỗi ngày 1 mục tiêu khác nhau
    public static function getNumberHourPerSessionInMonth($uid, $month)
    {
        $mm = self::getAllUserInfoInMonth($uid, $month);
        if ($mm) {
            foreach ($mm as $obj) {
                if ($obj->time_frame && str_starts_with($obj->time_frame, $month)) {
                    if ($obj->num4 && strstr($obj->num4, '_') !== false) {
                        return explode('_', $obj->num4)[1];
                    }
                }
            }
        }

        return 8;
    }

    public static function getSalaryBaseUser($uid)
    {
        if ($obj = HrEmployee::where('user_id', $uid)->first()) {
            if ($j = HrSalary::find($obj->job_title)) {
                return $j->salary_month;
            }
        }

        return 0;
    }

    public static function getUsersIdInOrgId($orgId)
    {
        $mm = HrEmployee::where('parent_id', $orgId)->orderBy('orders', 'ASC')->get();

        return $mUid = $mm->map(function ($obj) {
            return $obj->user_id;
        })->toArray();
    }

    public static function getOrgIdOfUserId($userid)
    {
        $obj = HrEmployee::where('user_id', $userid)->first();
        if ($obj) {
            return $obj->parent_id;
        }

        return 0;
    }

    /**
     * Tìm mảng orgid có ít nhất 1 nhân viên
     */
    public static function getAllOrgIdHasEmployee()
    {
        $mm = HrOrgTree::all();
        $mRet = [];
        foreach ($mm as $one) {
            //            echo "<br/>\n OneId = $one->id ";
            $found = HrEmployee::where('parent_id', $one->id)->first();
            if ($found) {
                $mRet[$one->id] = $one;
            }
        }

        return $mRet;
    }

    public static function getUsersIdInsertBeforeInTimeOrg($orgId)
    {

    }

    //Nếu quá 60p thì là cả ngày ko làm việc
    public static function getLateHour($nLate, $nHourCa = 8)
    {
        if (! $nLate) {
            return 0;
        }
        if ($nLate < 30) {
            return 0.5;
        }
        if ($nLate < 60) {
            return 1;
        }

        return $nHourCa;
    }

    public static function getListOrg($mUserAdminTree, $setUidMng)
    {
        $userid = getCurrentUserId();
        echo " <div style='margin-bottom: 10px' class='hide_print'>  <b> Danh sách Bộ phận thuộc quyền Quản lý của bạn (ID: $userid) </b></div>";
        //                    if(!isAdminACP_())

        echo "\n <div class='hide_print'>";
        $mSubTree = $mUserAdminTree[$setUidMng] ?? [];
        foreach ($mSubTree as $treeId) {
            $tree = \App\Models\HrOrgTree::find($treeId);
            $link = "?tree_id=$treeId&startMonth=".request('startMonth');

            if (request('tree_id') == $treeId) {
                echo " <a href='$link'> <button data-code-pos='ppp16861145904161' type='button' class='btn btn-sm btn-warning btn1'>[$tree->id] $tree->name  </button> </a>  ";
            } else {
                echo " <a href='$link'> <button data-code-pos='ppp16861145955801' type='button' class='btn btn-sm btn-info btn1'> [$tree->id] $tree->name   </button> </a>  ";
            }
        }
        echo "\n </div>";
    }

    public static function userInfoCol($uid)
    {
        $hrUser = \App\Models\HrEmployee::where('user_id', $uid)->first();
        if (! $hrUser) {
            return "Not found HR user: ($uid)";
        }
        $ret = '';
        if ($hrUser) {
            $hrTitle = \App\Models\HrJobTitle::find($hrUser ? $hrUser->job_title : null);
            $uidTxt = $uid;
            if (! $uid) {
                $uidTxt = " <span style='color: red'> Empty </span> ";
            }
            $ret .= " <b> $hrUser->last_name $hrUser->first_name </b> ".
                "<br><a target='_blank' href='/admin/hr-employee/edit/$hrUser->id'> Mã: $uidTxt </a> - ";

            $ret .= $hrTitle?->name ?? ' <span style="color: red">(Nhập chức danh?)</span>';

            //Show tên nhánh:
            if (isset($uidSet) && isset($indexOrOrgId)) {
                if ($orgX = \App\Models\HrOrgTree::find($indexOrOrgId)) {
                    $ret .= "<br/>\n <b> Bộ phận: $orgX->name ($indexOrOrgId) </b>";
                }
            } else {
                $stM = (request('startMonth'));

                $ret .= "<a title='xem riêng user này' target='_blank' href='/admin/hr-cham-cong2?user_id=$uid&startMonth=$stM'><br/>\n <i class='fa fa-plus hide_print'></i></a>";
            }
        }

        return $ret;
    }

    public static function getHtmlCell($meta, $mKeyAndValOfField, $dataObj, $field, $timeF)
    {

        $valKey = $dataObj->$field;

        $valKey1 = $valKey;
        if (! $valKey) {
            $valKey1 = '.';
        }

        $valText = '-';
        $des = $valText = $meta->getDescOfField($field);

        $clsSelect = $isSelect = '';
        if (isset($mKeyAndValOfField[$field])) {
            $isSelect = 1;
            $clsSelect = 'select_ok';
        }

        if (isset($mKeyAndValOfField[$field]) && isset($mKeyAndValOfField[$field][$valKey])) {
            $valText = $mKeyAndValOfField[$field][$valKey];
        } else {

        }

        $cls = '';
        if (! $valKey) {
            $cls = 'gray';
        }

        $descSession = '';

        if ($isSelect && isset(HrCommon::getSessionTypeCacheArray()[$valKey])) {
            $descSession = HrCommon::getSessionTypeCacheArray()[$valKey]->desc;
        }

        echo "<div title='$timeF | $field | $des | $descSession | $valKey '  class='value_cell $clsSelect' data-field='$field' data-key='$valKey'> ";

        if ($isSelect) {
            echo "<span class='$cls blue'> $valText </span>";
        } else { //                                    echo  "<input placeholder='$des'  class='inp_val_cell' data-field='$field' value='$valKey'/>";
            echo "<input placeholder='$des' class='inp_val_cell' data-field='$field' value='$valKey' type='text'/>";
        }

        echo '</div>';
    }

    public static function sumDay($treeId, $day)
    {

        $ssType = HrCommon::getCacheAllSessionType();
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($ssType);
        //        echo "</pre>";

        $mmUid = \App\Models\HrCommon::getUsersIdInOrgId($treeId);
        //Lấy ra các UID của all timesheet tháng này:
        $mNhanVienDaInsertVaoTimeSheet = array_column(\App\Models\HrSampleTimeEvent::select('user_id')->distinct()
            ->where('cat1', $treeId)
            ->where('time_frame', $day)
            ->get()?->toArray(), 'user_id');
        $mmUid = array_unique(array_merge($mmUid, $mNhanVienDaInsertVaoTimeSheet));
        $mmUid = array_filter($mmUid);

        $month = substr($day, 0, 7);
        $totalHour = 0;
        foreach ($mmUid as $uid) {
            //Lấy ra cache
            $m1 = self::getAllUserInfoInMonth($uid, $month);
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($m1);
            //            echo "</pre>";

            foreach ($m1 as $objTimeF) {
                if ($objTimeF->time_frame == $day && $objTimeF->cat1 == $treeId) {
                    //                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    //                    print_r($objTimeF);
                    //                    echo "</pre>";

                    if ($objTimeF->num4) {
                        $nH = $ssType[$objTimeF->num4]['hour'] ?? 0;
                        $totalHour += $nH;
                    }

                }

            }

        }
        echo "$totalHour Giờ";
    }
}

class HrLuongTmp
{
    public $uid;

    /**
     * @var HrEmployee
     */
    public $objEmployee;

    public $month;

    public $orgId;

    public $tongLuongFinal;

    public $tongLuongCa;

    public $tongCong;

    public $tongTru;

    public $tongCaDaLam;

    public $tongCaPhaiLamTrongThang;

    public $thuongChuyenCan;

    public $tongGioCa;

    public $tongGioLamThem;

    public $tongGioLeLamThem; //Ngay le

    public $tongGioCaLamThem; //Ngay thuong

    public $tongGioCaLamThemTinhLuongLamThem; //Ngay thuong

    public $tongGioCaLamThemBuCaThuong; //Bù cho ngày thường nếu ngày thường ko đủ 360h

    public $tongLuongLamThem;

    public $tongCaDiMuon;

    public $tongGioDiMuon;

    public $tongTienDiMuon;

    public $tongGioLeDiMuon;

    public $tongTienLeDiMuon;

    public $tongTienDiMuonAll;

    public $tongBuaAn;

    public $tongTienAn;

    public $mmPhanLoaiCa = [];

    public $tongCongExpense = 0;

    public $tongTruExpense = 0;
}
