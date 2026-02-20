<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use App\Models\GiaPha;
use App\Models\GiaPhaUser;
use App\Repositories\EventUserInfoRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventUserInfoControllerApi extends BaseApiController
{
    public function __construct(EventUserInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;
    }

    public function add(Request $request)
    {
        // Convert email to lowercase if present
        if ($request->has('email') && $request->email) {
            $request->merge(['email' => strtolower($request->email)]);
        }

        // Validate using model rules
        $validated = $request->validate(
            (new \App\Models\EventUserInfo())->getValidateRuleInsert()
        );

        // nếu ko phaải member add, là admin add, thì user  =-123
        $userId = -123;
        if(Helper1::isMemberModuleApi($request)) {
            $userId = getCurrentUserId();
        }
        $request->merge(['user_id' => $userId]);

        return parent::add($request);
    }

    public function update($id, Request $request)
    {
        // Convert email to lowercase if present
        if ($request->has('email') && $request->email) {
            $request->merge(['email' => strtolower($request->email)]);
        }

        // Set user_id if this is from member module
//        $userId = -1;

        //Nếu member update thì ấy uid của mình
        //Nếu admin update thì không tự gán lại uid ở đây:
        if(Helper1::isMemberModuleApi($request)) {
            $userId = getCurrentUserId();
            $request->merge(['user_id' => $userId]);
        }

        // Validate using model rules
        $validated = $request->validate(
            (new \App\Models\EventUserInfo())->getValidateRuleUpdate($id)
        );

        return parent::update($id, $request);
    }
}
