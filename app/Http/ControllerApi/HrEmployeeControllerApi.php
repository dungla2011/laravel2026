<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\HrEmployee;
use App\Models\SiteMng;
use App\Models\User;
use App\Repositories\HrEmployeeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HrEmployeeControllerApi extends BaseApiController
{
    public function __construct(HrEmployeeRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function change_order(Request $request)
    {

        //        echo "<pre>";
        //        print_r($request->all());
        //        echo "</pre>";
        $uid = getUserIdCurrentInCookie();
        $treeId = $request->tree_id;
        if (! $treeId) {
            return rtJsonApiError('Not found treeid');
        }

        if (! isSupperAdmin_()) {
            $allowOK = \App\Models\HrOrgTree_Meta::FCheckTreeBelongUidMng($uid, $treeId);
            if (! $allowOK) {
                return rtJsonApiError("Tree not belong your acc: $uid/ $treeId");
            }
        }

        if ($request->idList) {
            $mm = explode(',', $request->idList);
            $cc = 10;
            foreach ($mm as $fid) {
                $oneHr = HrEmployee::where('user_id', $fid)->first();
                if (! $oneHr) {
                    return rtJsonApiError("Not found hr $fid");
                }
                $oneHr->orders = $cc;
                $oneHr->update();
                $cc += 10;
            }
        }

    }

    public function search_user(Request $request)
    {

        $str = $request->search_str;
        //            $field = @$param['field'];
        //            if (!$field){
        //                //return rtJsonApiError("Not found 'field' in request (get,post)!");
        //                return null;
        //            }
        $mret = [];
        //Tìm kiếm

        $cls = User::class;
        $ret = $cls::where('id', $str)->orWhere('username', 'like', "%$str%")->orWhere('email', 'like', "%$str%")->limit(50)->get();
        //DB::enableQueryLog();
        if ($ret) { //if ($ret = $this->model->whereFieldLike($field, $str)->get())
            //                $mm = $ret->toArray();
            foreach ($ret as $obj) {
                $mret[] = ['value' => $obj->id, 'label' => "<img src='/images/downloadFile.png' style='width: 30px'> <span style='color: red'> <br>($obj->id) $obj->email </span> | $obj->username"];
            }
        }
        //dd(DB::getQueryLog());

        //return response()->json(['errorCode' => 0, 'dataRet' => $mret], 200);
        return rtJsonApiDone($mret);
        //
        //        //return \response()->json(['errorCode' => 1, 'dataRet' => "Not found input value"], 400);
        //        return rtJsonApiError("Not found input value");
    }

    public static function getHtmlPrintCardStatic($idf)
    {

        $obj = HrEmployee::find($idf);
        if ($obj instanceof HrEmployee);
        $img = $obj->getThumbInImageListWithNoImg();

        $jobTitle = $obj->getChucVu();

        $logo = SiteMng::getLogo();

        $siteCode = SiteMng::getSiteCode();

        $titleEn = SiteMng::getTitleEn();
        $titleSite = SiteMng::getTitle();
        $html = '';
        $html .= '<style> table {background-color: ; width: 100%} body {    padding: 0px; margin: 0px; box-sizing: border-box; } '.
            '.single_card {display: inline-block; width: 100mm; height: 66mm; padding: 10px 10px; margin-top: 10px; border: 10px solid dodgerblue; border-radius: 10px;} </style>';
        $html .= "<div data-code-pos='ppp16868989932671' class='single_card' style=''> ".
            "<table class='glx00' style=' font-size: 19px; font-family: Arial; width: 100% '>";
        $html .= "<tr style=''> <th style='width: ' > <img style='max-width: 60px; max-height: 60px' src='$logo' alt=''> </th> ".
            "<th style='padding: 0px 15px 0px 0px; color: darkred;' >".
            " $titleSite <div style='font-size: 15px; padding-top: 5px'> $titleEn </div> </th>  </tr> ".
            '</table>';
        $html .= '';
        $html .= "<table style='margin-top: 20px; font-size: 14px; font-family: Arial; '> ".
            "<tr data-code-pos='ppp16868989958601' style=''>".
            "<td> <img style='padding-top: 0px; max-width: 90px; max-height: 120px; margin-right: 0px' src='$img' alt=''> </td> ".
            "<td style='padding-top: 5px; padding-left: 5px'> Họ Tên: <b style='text-transform: uppercase'> $obj->last_name $obj->first_name </b> ".
            "<br> <br>  Chức vụ: <b> $jobTitle </b>  ".
            "<br>  <br> Số Hiệu : <b> $siteCode-$idf </b> </td> </tr>";

        $html .= '</table>'.
            '</div>';

        return $html;

    }

    public function getHtmlPrintProfile(Request $request, $returnHtmlOnly = 0)
    {

        $idf = $request->datax;
        if (! $idf && ! $request->print_multi_card) {
            return rtJsonApiError('Chưa chọn thành viên để In?');
        }

        if ($request->print_multi_card) {

            if ($request->all_id) {

                $html = '<style>.div_card {margin: 0px 0px 5px 10px; display: inline-block; width: 101mm; height: ;} </style>';

                $tmp = $cc = 0;
                foreach ($request->all_id as $idf) {
                    //                    echo "<br/>\n xxx $idf ";

                    if ($cc % 2 == 0 && $tmp == 0) {
                        //                        $html .= "<div class='ok123'>";
                        $tmp = 1;
                    }
                    $html .= "<div class='div_card' style=''>".self::getHtmlPrintCardStatic($idf).'</div>';
                    $cc++;
                    if ($cc % 2 == 0 && $tmp == 1) {
                        //                        $html .= "</div>";
                        $tmp = 0;
                    }
                }

                //                print_r($request->toArray());
                return rtJsonApiDone($html, 'html_ready');
            }

            echo "<br/>\n";

            return 'ABC123345345';
        }

        //        $html = HrEmployee::getDataHtmlToPrint($mId);

        $obj = HrEmployee::find($idf);
        if ($obj instanceof HrEmployee);
        $img = $obj->getThumbInImageListWithNoImg();

        $html = '';

        $jobTitle = $obj->getChucVu();

        $logo = SiteMng::getLogo();

        $siteCode = SiteMng::getSiteCode();

        if ($request->print_card) {

            $html = self::getHtmlPrintCardStatic($idf);

            return rtJsonApiDone($html, 'html_ready');
        }

        $html .= "<style>\r\n table {width: 100% }\r\n td span {font-style: } \r\n ".
            "b.info {float: left; margin-right: 10px}\r\n ".
            '.glx0112 td span {font-weight: normal; font-size: small} '.
            ' '.
            '</style>';
        $html .= '<page>';
        $html .= "<div data-code-pos='ppp16868988436571' style='text-align: center'> ".
            "<div data-code-pos='ppp16868987856111' style='font-size: large; margin-bottom: 10px; text-transform: uppercase; font-weight: bold; display: flex ;  align-items: center;justify-content: center;'> ".
            "<div style=' display: flex'> ".
            "<img src='$logo' style='width: 50px; margin-right: 10px ' alt=''> ".
            '</div>'.
            "<div style='display: flex ; flex-direction: column  '> <div>".SiteMng::getTitle().'</div>'.
            ''.
            "<div style='font-size: small; margin-top: 5px'>".SiteMng::getTitleEn().'</div>'.
            '</div>'.
            '</div>'.
            '</div>'.
            "<div style='margin: 0px 5%'>Trụ sở: ".SiteMng::getAddress1().
            '<BR>'.
            'VPGD: '.SiteMng::getAddress2().
            '<BR>'.
            'Hot Line: '.SiteMng::getPhoneAdmin().' - Email: '.SiteMng::getEmailAdmin().
            '<BR>'.
            'WebSite: https://'.SiteMng::getDomain1().
            '</div>'.
            '';
        $html .= '<hr>';
        $html .= "<table data-code-pos='ppp16868987945241' class='glx0112' style='width: 100%'>";
        $html .= "<tr> <th> <H1 style='margin: 0px'> PHIẾU NHÂN SỰ (PERSIONAL SHEET) </H1> </th>  </tr>";
        $html .= '</table>';

        $obj->father_birthday = (($obj->father_birthday));
        $obj->mother_birthday = (($obj->mother_birthday));
        $obj->spouse_birthday = (($obj->spouse_birthday));
        $obj->relatives_birthday = (($obj->relatives_birthday));
        $obj->birth_day = nowy_vn2_null(strtotime($obj->birth_day));
        $obj->idcard_date = nowy_vn2_null(strtotime($obj->idcard_date));

        $obj->father_birthday = substr($obj->father_birthday, 0, 4);
        $obj->mother_birthday = substr($obj->mother_birthday, 0, 4);
        $obj->spouse_birthday = substr($obj->spouse_birthday, 0, 4);
        $obj->relatives_birthday = substr($obj->relatives_birthday, 0, 4);

        $html .= "<table class='glx0112' style='width: 100%'>";
        $html .= '<tr> '.
            "<td style='width: 20%' > <img style='height: 140px' src='$img' alt=''> </td>   ".
            "<td style='text-align: center'><h2> $jobTitle </h2> </td>   ".
            "<td style='text-align: center'><h2> $obj->last_name $obj->first_name </h2></td>   ".
            "<td style='text-align: center'><h2>  </h2></td>   ".
            '</tr>';
        $html .= '</table>';

        $sex = $obj->getSex();

        $html .= "<table class='glx0112'>";
        $html .= "<tr> <td style='padding-top: 10px'><b class='info'> Số hiệu <span> (Name card No)</span>  :  $siteCode-$idf  </b>  </td>  </tr>";
        $html .= '</table>';

        $html .= '<u> <b>1. Bản thân </u></b>';
        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Giới tính <span>(Sex) </span>:  </b>  $sex </td> ".
            "<td><b class='info'> Chiều cao <span>(Height) </span>:  </b> $obj->height cm </td> ".
            "<td><b class='info'>Cân nặng <span>(Weight)</span>): </b> $obj->weight kg </td> ".
            '</tr>';
        $html .= '</table>';

        $html .= "<table data-code-pos='ppp16868988020921' class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Ngày Sinh: <span>(Birthday) </span> </b> $obj->birth_day </td> ".
            "<td><b class='info'>Dân Tộc: <span>(Nation) </span> </b>  $obj->nation </td>  </tr>";
        $html .= '</table>';

        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Quê quán: <br><span>Home town </span> </b> $obj->home_town  </td> ".
            "<td><b class='info'>Địa chỉ tạm trú: <br><span>Contact Address</span></b>  $obj->address  </td>".
            '</tr>';
        $html .= '</table>';

        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Hộ khẩu thường trú: <br> <span> Permanent address </span> </b> $obj->address_permanent  </td> ".
            "<td><b class='info'>Điện thoại: <br><span>Phone number</span></b> $obj->phone_number </td>".
            '</tr>';
        $html .= '</table>';

        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Số CMT:  <br><span>ID Number</span>  </b> $obj->id_card</td> ".
            "<td><b class='info'>Ngày cấp: <br><span>Issued on</span></b> $obj->idcard_date   </td>".
            "<td><b class='info'>Nơi cấp: <br><span>Issued by</span></b>$obj->idcard_place  </td>".
            '</tr>';
        $html .= '</table>';

        $html .= "<table data-code-pos='ppp16868988067751' class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Bằng cấp cao nhất: <br><span>Education</span>  </b> $obj->certificate </td> ".
            "<td><b class='info'>Qua bảo vệ chuyên nghiệp chưa: <br><span>Special Skill</span></b> $obj->skill   </td>".
            '</tr>';
        $html .= '</table>';

        ////////////////////////////////////////
        $html .= ' <u><b>2. Gia đình </u></b>';

        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td><b class='info'>Họ Tên Bố: <br><span>Father'sname</span> </b> $obj->father_name </td> ".
            "<td><b class='info'>Họ Tên Mẹ: <br><span>Mother's name</span></b> $obj->mother_name   </td>".
            '</tr>';
        $html .= '<tr> '.
            "<td><b class='info'>Năm sinh: <br><span>Year of birth</span>  </b> $obj->father_birthday </td> ".
            "<td><b class='info'>Năm sinh: <br><span>Year of birth</span></b> $obj->mother_birthday  </td>".
            '</tr>';
        $html .= '<tr> '.
            "<td><b class='info'>Nghề nghiệp:<br><span>Occupation</span> </b> $obj->father_occupation  </td> ".
            "<td><b class='info'>Nghề nghiệp:<br><span>Occupation</span></b> $obj->mother_occupation   </td>".
            '</tr>';
        $html .= '<tr> '.
            "<td><b class='info'>Nơi làm việc: <br><span>Place of work</span></b> $obj->father_work_place  </td> ".
            "<td><b class='info'>Nơi làm việc:  <br><span>Place of work</span></b>$obj->mother_work_place </td>".
            '</tr>';
        $html .= '</table>';

        $html .= "<table  data-code-pos='ppp16868988109061' class='glx0112'>".
            '<tr>'.
            "<td style='width: 50%'> <b class='info'>Họ Tên vợ (chồng): <br><span>Husband/Wife's name</span> </b> $obj->spouse_name   </td> ".
            "<td><b class='info'>Năm sinh: <br><span>Year of birth</span> </b> $obj->spouse_birthday  </td>".
            '</tr>'.
            '</table>';

        $html .= "<table class='glx0112'>".
            '<tr>'.
            "<td style='width: 50%'> <b class='info'>Nghề nghiệp: <br><span>Occupation</span></b>$obj->spouse_occupation   </td>".
            "<td><b class='info'>Nơi làm việc: <br><span>Place of word</span></b>$obj->spouse_work_place  </td>".
            '</tr>';
        $html .= '</table>';

        $html .= "<table class='glx0112'>";
        $html .= '<tr> '.
            "<td style='width: 50%'> <b class='info'>Người thân: <br><span></span> </b> $obj->relatives_name  </td> ".
            "<td><b class='info'>Năm sinh: <br><span>Birthday</span> </b>$obj->relatives_birthday  </td>".
            '</tr>';
        $html .= '</table>';
        $html .= '<hr>';
        $html .= " <div data-code-pos='ppp16868988161031' style='text-align: center'>   (Những thông tin trên đây là đúng theo hồ sơ nhân viên do Công ty quản lý) </div>";
        $html .= '</page>';

        return rtJsonApiDone($html, 'html_ready');
    }
}
