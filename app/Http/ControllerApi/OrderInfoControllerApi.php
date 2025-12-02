<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\OrderItem;
use App\Models\ModelGlxBase;
use App\Models\OrderInfo;
use App\Models\OrderShip;
use App\Models\PartnerInfo;
use App\Models\Product;
use App\Models\Product_Meta;
use App\Models\ProductVariant;
use App\Models\ProductVariantOption;
use App\Models\Sku;
use App\Models\Telesale;
use App\Models\Telesale_Meta;
use App\Models\TransportInfo;
use App\Models\TransportInfo_Meta;
use App\Repositories\OrderInfoRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Shopee\Nodes\Order\Order;

class OrderInfoControllerApi extends BaseApiController
{
    public function __construct(OrderInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function call_back_ghtk(Request $request)
    {

        $rq = $request->all();
        $label_id = $rq['label_id'];
        $partner_id = $rq['partner_id'];
        $action_time = $rq['action_time'];
        $status_id = $rq['status_id'];
        $reason_code = $rq['reason_code'];
        $reason = $rq['reason'];
        $weight = $rq['weight'];
        $fee = $rq['fee'];
        $return_part_package = $rq['return_part_package'];
        //        echo "<br/>\n $label_id";
        if ($od = OrderShip::where('remote_label', $label_id)->first()) {
            if ($od instanceof OrderShip);
            if ($od->status != $status_id) {
                $od->status = $status_id;
                $od->addLog("update status=$status_id,json=".json_encode($rq));
            } else {
                $od->addLog("update, not change status=$status_id");
            }
            $od->update();
        }
    }

    public function getHtmlPrintOrder(Request $request)
    {

        $mId = $request->datax;
        if (! $mId) {
            return rtJsonApiError('Chưa chọn đơn in?');
        }

        $html = Telesale_Meta::getDataHtmlToPrint($mId);

        return rtJsonApiDone($html, 'html_ready');

    }

    public function sendToGhtk(Request $request)
    {

        $userid = getUserIdCurrentInCookie();

        $fid = $request->get('dataId');
        if (! $fid) {
            return null;
        }

        $objOrder = OrderInfo::find($fid);
        if (! $objOrder) {
            return rtJsonApiError("Not found obj order $fid");
        }

        $objTele = Telesale::find($fid);
        if ($objTele instanceof Telesale);
        if (! $objTele) {
            return rtJsonApiError("Not found obj tele $fid");
        }

        if ($odShip = OrderShip::where('order_id', $fid)->first()) {
            if ($objTele->order_status != DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP) {
                $tmp = $objTele->order_status;
                $objTele->order_status = DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP;
                $objTele->updateAndTag(" Cập nhật lại trạng thái đã ship, vì giá trị cũ không đúng: $tmp -> ".DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP);
                $tmp2 = $objOrder->order_status;
                $objOrder->order_status = DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP;
                $objOrder->updateAndTag(" Cập nhật lại trạng thái đã ship, vì giá trị cũ không đúng: $tmp2 -> ".DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP);
            }

            return rtJsonApiDone('Đơn đã gửi trước đây: '.$odShip->created_at, '', 1, ['fid_done' => $fid]);
        }

        $postUpdate = $request->post();
        $objTele->update($postUpdate, [], 'update telesale');
        //$objTele = $objTele->find($objTele->id);

        $error = '';
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($objTele->toArray());
        //        echo "</pre>";

        $objTele->phone_request = trim($objTele->phone_request, '0+');
        if (! $objTele->phone_request || strlen($objTele->phone_request) < 9 || ! is_numeric($objTele->phone_request)) {
            $error .= ("- Cần có số điện thoại hợp lệ: $objTele->phone_request\n");
        }
        if (! $objTele->to_address && ! $request->to_address) {
            $error .= ("- Cần có địa chỉ nhận đầy đủ: $objTele->to_address\n");
        }

        //Kiểm tra trạng thái đơn, nếu chưa Thành công thì ko thể gửi đơn
        if ($objTele->order_status != 1) {
            $error .= ("- Trạng thái phải chuyển về 'Khách đã chốt đơn' để có thể gửi đơn sang bên Ship!\n");
        }

        if (! $objTele->api_key_ship) {
            $error .= ("- Cần chọn Ship-Api-Key cho đơn hàng!\n");
        }

        if ($error) {
            return rtJsonApiError($error);
        }

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($obj->toArray());
        //        echo "</pre>";

        $mBillP = OrderItem::where('order_id', $objTele->id)->get();

        if (! $mBillP || $mBillP->count() <= 0) {
            return rtJsonApiError('Bạn chưa chọn hàng hóa dịch vụ?');
        }
        $ret = '';
        $totalPr = 0;

        $mProd = [];
        if ($mBillP && $objTele->to_address && $json = json_decode($objTele->to_address)) {
            foreach ($mBillP as $billP) {
                $prod = Product::find($billP->product_id);
                $sku = Sku::find($billP->sku_id);
                $pr = $billP->price * $billP->quantity;
                $totalPr += $pr;
                echo " \n @$prod->name , $billP->sku_string : <br> SL: $billP->quantity x $billP->price = $pr ";

                $mProd[] = ['name' => $prod->name.' - '.$billP->sku_string,
                    'weight' => $sku->weight / 1000,
                    'quantity' => $billP->quantity,
                    'product_code' => $billP->sku_id,
                ];
            }

            echo "\n";
            //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($json);
            //        echo "</pre>";

            $tinhThanh = $json->l1;
            $quanHuyen = $json->l2;
            $phuongXa = $json->l3;
            $soNha = $json->detail;

            echo "<br/>\n $soNha / $tinhThanh/ $quanHuyen / $phuongXa";

            $orderGhtk = new \clsExOrderGHTK();
            $orderGhtk->id = $fid;
            $orderGhtk->pick_name = ' Công ty DH';
            $orderGhtk->pick_address = ' Linh Đàm';
            $orderGhtk->pick_province = ' Hà Nội';
            $orderGhtk->pick_district = ' Thanh Xuân';
            $orderGhtk->pick_ward = ' Phường ABC';
            $orderGhtk->pick_tel = ' 0999999999';

            $orderGhtk->tel = $objTele->phone_request;
            $orderGhtk->name = $objTele->name;
            $orderGhtk->address = $json->detail;
            $orderGhtk->province = $json->l1;
            $orderGhtk->district = $json->l2;
            $orderGhtk->ward = $json->l3;
            $orderGhtk->hamlet = 'khác';
            $orderGhtk->is_freeship = 0;
            $orderGhtk->pick_date = '';
            $orderGhtk->pick_money = $totalPr;
            $orderGhtk->note = '';
            $orderGhtk->value = $totalPr;
            $orderGhtk->transport = 'fly';
            //Todo: cần cấu hình cách chuyển hàng fly ...
            $orderGhtk->transport = 'road';
            $orderGhtk->pick_option = 'cod';
            $orderGhtk->pick_session = '2';
            $orderGhtk->tags = '';

            $mAll = ['products' => $mProd, 'order' => $orderGhtk];
            $strM = json_encode($mAll);
            echo "RET = $strM";

            $apiKey = PartnerInfo::find($objTele->api_key_ship)->token_api;

            $ret = \clsExOrderGHTK::postDon($strM, $apiKey);

            ob_clean();
            if (! $ret || ! isset($ret->success)) {
                return rtJsonApiError('Có lỗi post đơn hàng, không thể gửi đơn1?');
            }
            if (! isset($ret->message)) {
                return rtJsonApiError('Có lỗi post đơn hàng, không thể gửi đơn2, không có trường message?');
            }
            if ($ret->success) {
                if ($label = $ret?->order?->label) {
                    $os = new OrderShip();
                    $os->vendor_id = 2;
                    $os->order_id = $fid;
                    $os->user_id = $userid;
                    $os->fee = $ret?->order?->fee;
                    $os->remote_label = $ret?->order?->label;
                    $os->remote_tracking_id = $ret?->order?->tracking_id;
                    $os->pick_time = $ret?->order?->date_to_delay_pick;
                    $os->delive_time = $ret?->order?->date_to_delay_deliver;
                    $os->json_send = json_encode($orderGhtk);
                    $os->json_get = json_encode($ret);
                    if (! $os->save()) {
                        return rtJsonApiError('Can not save!');
                    }
                } else {
                    return rtJsonApiError('Có lỗi: không có trường order?->label');
                }

                $objTele->setStatusTele_ThongtinDonDaGuiDenShip();

                $objTele->updateAndTag('auto chuyển trạng thái đã gửi đơn đến bên ship');

                $objOrder->order_status = $objTele->order_status;
                $objOrder->updateAndTag('auto chuyển trạng thái đã gửi đơn đến bên ship');

                return rtJsonApiDone('Đơn hàng đã post thành công');
            } else {

                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($ret);
                //                echo "</pre>";
                //                die();

                if (isset($ret->error) && isset($ret->error->code) && $ret->error->code == 'ORDER_ID_EXIST') {
                    $ghtk_label = $ret?->error?->ghtk_label;
                    $os = new OrderShip();
                    $os->vendor_id = 2;
                    $os->order_id = $fid;
                    //                    $os->fee = $ret?->order?->fee;
                    $os->remote_label = $ghtk_label;
                    //                    $os->remote_tracking_id = $ret?->order?->tracking_id;
                    //                    $os->pick_time = $ret?->order?->date_to_delay_pick;
                    //                    $os->delive_time = $ret?->order?->date_to_delay_deliver;
                    if (! $os->save()) {
                        return rtJsonApiError('Can not save!');
                    }

                    if (isset($ret->message)) {
                        return rtJsonApiDone("$ret->message / $ret->error_code / $ret->log_id "."\n\n".serialize($ret));
                    }

                    return rtJsonApiDone("Đơn đã post thành công? Mã đối tác: $ghtk_label "."\n\n".serialize($ret));
                }

                return rtJsonApiError('Có lỗi: '.$ret->message."\n\n".serialize($ret));
            }
        }

        return rtJsonApiError("Tham số không hợp lệ? Kiểm tra lại thông tin khác hoặc địa chỉ: $objTele->to_address");

    }

    public function postBill(Request $request)
    {

        $userid = auth()->id();

        $rq = $request->all();
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($rq);
        //        echo "</pre>";

        $clsData = $request->globalDataClass;

        if (! $clsData) {
            return rtJsonApiError('Not class data!');
        }

        $mmSku = $request->bill_info;
        $client_session_time = $request->client_session_time;
        $billId = $request->get('order_id');

        if (! $billId) {
            return rtJsonApiError('Not billId1?');
        }
        //        die("xxx $clsData");

        $isNewOrder = [];
        if ($billId <= 0) {
            $tmp = $billId;
            $newBill = new $clsData;
            if ($newBill instanceof OrderInfo);
            $newBill->insert(['user_id' => $userid]);
            $billId = DB::getPdo()->lastInsertId();
            $isNewOrder[$tmp] = $billId;

            //return rtJsonApiError("Not valid billId : $billId ?");
        }
        else{
            if($odi1 = OrderInfo::find($billId)){
                $userid = $odi1->user_id;
            }
        }

        if (! $billId) {
            return rtJsonApiError('Not bill id?');
        }

        //Xóa hết bill cũ đi
        $nBill = OrderItem::where('order_id', $billId)->count();
        OrderItem::where('order_id', $billId)->delete();

        if ($request->delete_all_product_in_bill) {
            ob_clean();

            return rtJsonApiDone(1, "Đã xóa hết $nBill hàng hóa trong Bill: $billId!");

            return;
        }



        $totalPrice = 0;
        if ($mmSku) {
            foreach ($mmSku as $mSku) {
                $sku = $mSku['sku'] ?? 0;
                $quantity = $mSku['quantity_input'] ?? 0;
                $price = $mSku['price_input'] ?? 0;
                $productId = $mSku['productId'] ?? 0;
                $quantity = intval($quantity);
                $price = intval($price);

                echo "<br/>\n $price x $quantity";
                $totalPrice += $price * $quantity;
                if (! $sku) {
//                    continue;
                }

                if (! $oSku = Sku::find($sku)) {
//                    continue;
                }

                //                echo "<br/>\n $oSku->sku";
                //
                //                 continue;

                if (OrderItem::where(['user_id' => $userid, 'order_id' => $billId, 'sku_id' => $sku, 'client_session_time' => $client_session_time])->first()) {
                    echo "<br/>\n Đã có session này, có thể cần update nếu thay đổi";

                    continue;
                }

//                echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
//                print_r($mSku);
//                echo '</pre>';

                $billP = new OrderItem();
                if($sku){
                    $billP->sku_id = $sku;
                    $billP->sku_string = $oSku->sku;
                    $billP->product_id = $oSku->product_id;
                    $billP->price_org = $oSku->price;
                }
                else{
                    if($proObj = Product::find($productId)){
                        $billP->product_id = $productId;
                        $billP->param1 = $proObj->param1;
                    }
                }

                $billP->quantity = $quantity;
                $billP->price = $price;
                $billP->user_id = $userid;

                $billP->order_id = $billId;
                $billP->client_session_time = $client_session_time;

                $billP->save();
            }
//            echo "<br/>\n $billId";
        }

        if ($obj = $clsData::find($billId)) {
            $obj->money = $totalPrice;
            $obj->update();

            if ($clsData instanceof ModelGlxBase);
            $meta = $clsData::getMetaObj();
            //            $meta = new TransportInfo_Meta();

            $ret = $meta->_service_require($obj, 'get_only_ret_html');

            ob_clean();

            $ret0 = ['html_ret' => $ret];
            if ($isNewOrder) {
                $ret0['new_bill'] = $isNewOrder;
            }

            return rtJsonApiDone($ret0, 'return service info', 1, $totalPrice);
        }

    }

    public function getListProductSelect(Request $request)
    {

        $billId = $request->billId;
        if (! $billId) {
            return rtJsonApiError('Not billId?');
        }

        $mm = Product::where('status', 1)->limit(100)->latest('id')->get();
        foreach ($mm as $prod) {
            echo "\n<div style='border-bottom: 1px solid #ccc;
background-color: lavender; margin-bottom: 10px;
padding: 10px; color: green ; font-weight: bold'>";

            $editBtn = " <a href='/admin/product/edit/$prod->id' target='_blank'> <i class='fa fa-edit'></i> </a> ";

            echo "<div data-code-pos='ppp1679324443797' style=''> $editBtn $prod->id. $prod->name </div>";

            $meta = new Product_Meta();
            echo $meta->_sku_list($prod, $billId,'select123');
            echo "<br data-code-pos='ppp1679273733271' />\n";
            echo "\n</div>";
        }

        //        return "ABC123 BID = " . \request('billId');
    }

    public function setOptionProduct(Request $request)
    {

        $pr = $request->post();

        $productId = $request->productId;

        if (!$productId || !is_numeric($productId)) {
            return rtJsonApiError('Not product id?');
        }
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($pr);
//                echo "</pre>";
//
//                die();
        //        Sku::where(['product_id'=>$productId])->where('product_opt_list', ",,")->forceDelete();
        //        return;

        $haveOpt = 0;
        foreach ($pr as $m1) {
            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($m1);
            //            echo "</pre>";
            //            if(0)
            foreach ($m1 as $opt) {
                $haveOpt = 1;

                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($opt);
                //                echo "</pre>";

                $idOpt = intval(trim($opt['id']));
                $nameOpt = trim($opt['name']);

                $del1 = 0;
                if ($idOpt < 0) {
                    $idOpt = -1 * $idOpt;
                    $del1 = 1;
                }

                if ($obj = ProductVariant::find($idOpt)) {
                    if ($del1) {
                        //Xóa hết các child
                        if ($mmPVO = ProductVariantOption::where('product_variant_id', $idOpt)->get()) {
                            foreach ($mmPVO as $pvo) {
                                //Xóa hết các SKU tương ứng
                                Sku::where(['product_id' => $productId])->where('product_opt_list', 'LIKE', "%,$pvo->id,%")->forceDelete();
                                $pvo->forceDelete();
                            }
                        }
                        $obj->delete();
                    } elseif ($obj->name != $nameOpt) {
                        $obj->name = $nameOpt;
                        $obj->update();
                    }
                }

                //                echo "<br/>\n--- ID,NAME = $idOpt | $nameOpt";

                if (! $idOpt && $nameOpt) {
                    $idOpt = ProductVariant::insert(['name' => $nameOpt, 'product_id' => $productId]);
                    $idOpt = DB::getPdo()->lastInsertId();
//                    echo "<br/>\n + Insert new $idOpt";
                }

                if (! isset($opt['all_opt'])) {
                    continue;
                }
                $allOpt = $opt['all_opt'];

                //                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                //                print_r($allOpt);
                //                echo "</pre>";
                foreach ($allOpt as $m2) {
                    $idOptVar = intval(trim($m2['id']));
                    $nameOptVar = trim($m2['name']);
//                    echo "<br/>\n + IDsub = $idOptVar / name=$nameOptVar";

                    $del = 0;
                    if ($idOptVar < 0) {
                        $idOptVar = -1 * intval($idOptVar);
                        $del = 1;
                    }
                    if ($obj = ProductVariantOption::find($idOptVar)) {
                        if ($del) {
                            Sku::where(['product_id' => $productId])->where('product_opt_list', 'LIKE', "%,$obj->id,%")->forceDelete();
                            $obj->forceDelete();
                        } elseif ($obj->name != $nameOptVar) {
                            $obj->name = $nameOptVar;
                            $obj->update();
                        }
                    } else {
                        //Xóa SKU này đi
                    }

                    if (! $idOptVar && $nameOptVar) {
                        //Tìm name xem có chưa, có rồi thì thôi:
                        if (! ProductVariantOption::where(['product_variant_id' => $idOpt, 'name' => $nameOptVar])->first()) {
                            ProductVariantOption::insert(['name' => $nameOptVar, 'product_variant_id' => $idOpt]);
                            $idOptVar = DB::getPdo()->lastInsertId();
//                            echo "<br/>\n + Insert new2 $idOptVar";
                        }
                    }
                }
            }
        }

        if(!$haveOpt){
        }
        Product_Meta::updateInsertSKUString($productId);

        return rtJsonApiDone("Done Update sku? $haveOpt New Opt");

    }
}
