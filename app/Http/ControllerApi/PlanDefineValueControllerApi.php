<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\PlanDefine;
use App\Models\PlanDefineValue;
use App\Models\PlanDefineValue_Meta;
use App\Models\PlanName;
use App\Repositories\PlanDefineValueRepositoryInterface;

class PlanDefineValueControllerApi extends BaseApiController
{
    public function __construct(PlanDefineValueRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    function get_plan_info() {
        $plan_id = request('plan_id', 0);
        if($plan_id){
            PlanDefineValue_Meta::excuteInsertPlanDefineValue($plan_id);
            $pl1 = PlanDefineValue::where("plan_id", $plan_id)->where("plan_field_name", "input_gia_ban_du_kien")->first();
            $pl2 = PlanDefineValue::where("plan_id", $plan_id)->where("plan_field_name", "input_luong_ban_du_kien_thang")->first();

            $compare_name = $compare_rate  = '';
            if($pl = PlanName::find($plan_id)){
                $compare_name = $pl->compare_name;
                $compare_rate = $pl->compare_rate;
            }

            return rtJsonApiDone([
                'input_gia_ban_du_kien'=>$pl1->value,
                'input_luong_ban_du_kien_thang'=> $pl2->value,
                'compare_name'=>$compare_name,
                'compare_rate'=>$compare_rate,
            ],
                'Lấy thông tin kế hoạch thành công');
        }
    }

    function update_val()
    {
        $plan_id = request('plan_id', 0);
        if($plan_id){
            if($val = request('input_luong_ban_du_kien_thang', 0))
                PlanDefineValue::where("plan_id", $plan_id)->where("plan_field_name", "input_luong_ban_du_kien_thang")->first()?->update(['value' => $val]);
            if($val = request('input_gia_ban_du_kien', 0))
                PlanDefineValue::where("plan_id", $plan_id)->where("plan_field_name", "input_gia_ban_du_kien")->first()?->update(['value' => $val]);

            return rtJsonApiDone(['input_gia_ban_du_kien'=>123, 'input_luong_ban_du_kien_thang'=> 456], 'Lấy thông tin kế hoạch thành công');
        }
    }
}
