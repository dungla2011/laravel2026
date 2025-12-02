<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\DonViHanhChinh;
use App\Repositories\DonViHanhChinhRepositoryInterface;
use Illuminate\Http\Request;

class DonViHanhChinhControllerApi extends BaseApiController
{
    public function __construct(DonViHanhChinhRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function search_address(Request $rq)
    {

        $pr = $rq->all();

        $str = @$pr['term'];
        $pid = @$pr['pid'];
        //        if(!$str)
        //            return null;

        if ($pid !== 0 && ! is_numeric($pid)) {
            return rtJsonApiError('Not valid pid!');
        }

        $mm = DonViHanhChinh::where('parent_id', $pid)->where('name', 'LIKE', "%$str%")->orderBy('name', 'ASC')->limit(100)->get();

        $ret = [];
        foreach ($mm as $obj) {
            $ret[] = ['id' => $obj->id, 'label' => $obj->name, 'value' => $obj->name, 'full_value' => $obj->id];
        }

        ob_clean();

        echo json_encode($ret);
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($pr);
        //        echo "</pre>";

    }
}
