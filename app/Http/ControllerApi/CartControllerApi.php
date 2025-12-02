<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\Cart;
use App\Models\CartItem;
use App\Repositories\CartRepositoryInterface;

class CartControllerApi extends BaseApiController
{
    public function __construct(CartRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }


    /**
     * User tao cart cho minh
     * Admin tao cart cho user
     * @param Request|\Illuminate\Http\Request $request
     * @return void
     */
    function add1(Request|\Illuminate\Http\Request $request)
    {
        //Kiểm tra xem có cart chưa, nếu chưa thì tạo mới

    }

    function add_to_cart()
    {
        $request  = request();

        if(!is_numeric($request->product_id) || !is_numeric($request->quantity)){
            return rtJsonApiError("Not valid data");
        }

        $product_id = $request->product_id;
        $quantity = $request->quantity;
        $uid = getCurrentUserId();

        $model = new Cart();

        $cart = $model->add_to_cart($product_id, $quantity,$uid);

        return rtJsonApiDone(1,"Add Cart done?", );

    }
}
