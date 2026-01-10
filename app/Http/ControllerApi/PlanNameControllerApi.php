<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\PlanName;
use App\Repositories\PlanNameRepositoryInterface;

class PlanNameControllerApi extends BaseApiController
{
    /**
     * Danh sách các tham số được lưu trong plan_params JSON
     */
    private static $planParamFields = [
        'compare_name',
        'compare_rate',
        'input_gia_ban_du_kien',
        'input_luong_ban_du_kien_thang',
        'input_san_luong_ban_du_kien_thang_1',
        'input_san_luong_ban_du_kien_thang_2',
        'input_san_luong_ban_du_kien_thang_3',
        'input_san_luong_ban_du_kien_thang_4',
        // Sản lượng 12 tháng
        'san_luong_thang_1', 'san_luong_thang_2', 'san_luong_thang_3', 'san_luong_thang_4',
        'san_luong_thang_5', 'san_luong_thang_6', 'san_luong_thang_7', 'san_luong_thang_8',
        'san_luong_thang_9', 'san_luong_thang_10', 'san_luong_thang_11', 'san_luong_thang_12',
        // Năng lực bán 12 tháng
        'nang_luc_nv_ban_thang_1', 'nang_luc_nv_ban_thang_2', 'nang_luc_nv_ban_thang_3', 'nang_luc_nv_ban_thang_4',
        'nang_luc_nv_ban_thang_5', 'nang_luc_nv_ban_thang_6', 'nang_luc_nv_ban_thang_7', 'nang_luc_nv_ban_thang_8',
        'nang_luc_nv_ban_thang_9', 'nang_luc_nv_ban_thang_10', 'nang_luc_nv_ban_thang_11', 'nang_luc_nv_ban_thang_12',
        // Tỷ lệ chuyển đổi 12 tháng
        'ty_le_chuyen_doi_thang_1', 'ty_le_chuyen_doi_thang_2', 'ty_le_chuyen_doi_thang_3', 'ty_le_chuyen_doi_thang_4',
        'ty_le_chuyen_doi_thang_5', 'ty_le_chuyen_doi_thang_6', 'ty_le_chuyen_doi_thang_7', 'ty_le_chuyen_doi_thang_8',
        'ty_le_chuyen_doi_thang_9', 'ty_le_chuyen_doi_thang_10', 'ty_le_chuyen_doi_thang_11', 'ty_le_chuyen_doi_thang_12',
        // Tỷ lệ chuyển đổi Marketing
        'ty_le_chuyen_doi_lien_lac', 'ty_le_chuyen_doi_chat', 'ty_le_chuyen_doi_like', 'ty_le_chuyen_doi_seen',
    ];

    public function __construct(PlanNameRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }

    /**
     * Lấy thông tin plan_params từ bảng plan_names
     */
    function get_plan_info() {
        $plan_id = request('plan_id', 0);
        if($plan_id){
            $plan = PlanName::find($plan_id);
            if (!$plan) {
                return rtJsonApiFail('Không tìm thấy kế hoạch');
            }

            // Lấy plan_params từ JSON field
            $planParams = [];
            if ($plan->plan_params) {
                $planParams = is_string($plan->plan_params)
                    ? json_decode($plan->plan_params, true) ?? []
                    : (array) $plan->plan_params;
            }

            // Đảm bảo tất cả các field đều có trong response (null nếu chưa có)
            $response = [];
            foreach (self::$planParamFields as $field) {
                $response[$field] = $planParams[$field] ?? null;
            }

            return rtJsonApiDone($response, 'Lấy thông tin kế hoạch thành công');
        }

        return rtJsonApiFail('Thiếu plan_id');
    }

    /**
     * Cập nhật giá trị vào plan_params JSON trong bảng plan_names
     */
    function update_val()
    {
        $plan_id = request('plan_id', 0);
        if(!$plan_id){
            return rtJsonApiFail('Thiếu plan_id');
        }

        $plan = PlanName::find($plan_id);
        if (!$plan) {
            return rtJsonApiFail('Không tìm thấy kế hoạch');
        }

        // Lấy plan_params hiện tại
        $planParams = [];
        if ($plan->plan_params) {
            $planParams = is_string($plan->plan_params)
                ? json_decode($plan->plan_params, true) ?? []
                : (array) $plan->plan_params;
        }

        // Cập nhật các giá trị từ request
        $updatedFields = [];
        foreach (self::$planParamFields as $field) {
            $value = request($field);
            if ($value !== null) {
                $planParams[$field] = $value;
                $updatedFields[$field] = $value;
            }
        }

        // Lưu lại plan_params
        $plan->plan_params = json_encode($planParams, JSON_UNESCAPED_UNICODE);
        $plan->save();

        return rtJsonApiDone($updatedFields, 'Update thông tin kế hoạch thành công');
    }
}
