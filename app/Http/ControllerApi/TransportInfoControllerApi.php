<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use App\Repositories\TransportInfoRepositoryInterface;
use Illuminate\Http\Request;

class TransportInfoControllerApi extends BaseApiController
{
    public function __construct(TransportInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function search(Request $request)
    {
        $mret = [];
        $data_autocomplete_id = $request->data_autocomplete_id;
        $value = $request->search_str;
        $field = $request->field;

//        die("Value: $value, Field: $field");

        if ($value && $obj = \App\Models\User::where($field, 'LIKE', "%$value%")->first()) {
            $x1 = $obj->$field;
            $ret = "<span data-code-pos='ppp1665496102584' data-autocomplete-id='$data_autocomplete_id'
class='span_auto_complete'
data-item-value='$x1' title='Remove this item'>$obj->email [x]</span>";
            $obj = json_decode($obj);
            //return 'abc';
            if (Helper1::isApiCurrentRequest()) {
                $mret = [];
                $mret[] = ['value' => $x1, 'label' => "$x1 / $obj->email"];

                return rtJsonApiDone($mret);
            }

            return $ret;
        }

        //            $mret = [];
        ////            $mret[] = ['value' => $obj['id'], 'label' => $obj[$field],];
        //            $mret[] = ['value' => $request->search_str, 'label' => $request->search_str];
        return rtJsonApiDone($mret);

        //return \response()->json(['errorCode' => 1, 'dataRet' => "Not found input value"], 400);
        //        return rtJsonApiError("Not found input value");
    }
}
