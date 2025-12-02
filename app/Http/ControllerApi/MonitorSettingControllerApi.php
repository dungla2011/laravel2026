<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MonitorSettingRepositoryInterface;
use Illuminate\Http\Request;

class MonitorSettingControllerApi extends BaseApiController
{
    public function __construct(MonitorSettingRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }

    public function delete(Request $request)
    {
        die("Not allow delete!");
    }

    function update($id, Request $request)
    {
        if($request->global_stop_alert_to && is_numeric($request->global_stop_alert_to)){
            $time = nowyh(time() + 3600 * $request->global_stop_alert_to);
            $request->merge(['global_stop_alert_to' => $time]);
        }

        if(!$request->global_stop_alert_to || $request->global_stop_alert_to == '0')
            $request->merge(['global_stop_alert_to' => null]);

        if($timeRange = $request->alert_time_ranges){
            //Kiểm tran timeRange phải có dạng HH:MM-HH:MM,HH:MM-HH:MM

            $strError = "Error: Time range must be in format HH:MM-HH:MM, for example: 05:30-23:00";

            $timeRange = str_replace(' ', '', $timeRange);
            $parts = explode(',', $timeRange);
            foreach ($parts as $part){
                if(!str_contains($part, '-')){
                    die($strError);
                }
                $subParts = explode('-', $part);
                if(count($subParts) != 2){
                    die($strError);
                }
                $start = $subParts[0];
                $end = $subParts[1];
                if(!preg_match('/^(2[0-3]|[01][0-9]):([0-5][0-9])$/', $start) || !preg_match('/^(2[0-3]|[01][0-9]):([0-5][0-9])$/', $end)){
                    die($strError);
                }
            }

        }

        return parent::update($id, $request);
    }



}
