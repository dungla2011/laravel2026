<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Repositories\MonitorConfigRepositoryInterface;
use Illuminate\Http\Request;
use App\Helpers\TelegramHelper;
use App\Models\MonitorUser;
use Exception;
class MonitorConfigControllerApi extends BaseApiController
{
    public function __construct(MonitorConfigRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }


    function checkValidTypeConfig($request)
    {

        if($request->alert_type) {
            $request->alert_config = trim($request->alert_config);
            $request->alert_config = str_replace([",,"], ',', $request->alert_config);
            $request->alert_config = str_replace([",,"], ',', $request->alert_config);
            $request->alert_config = str_replace([",,"], ',', $request->alert_config);
            $request->merge(['alert_config' => $request->alert_config]);
            $m = trim($request->alert_config, ',');

            $teleError = "Error1: not valid Telegram-Bot Token or Chat ID, format like:

-12345678012,AAH-BCDEFGHIJKLMNOPQRSTUVWXYZ123456

- Telegram Chat ID: -12345678012
- Bot Token: 123456789:AAH-BCDEFGHIJKLMNOPQRSTUVWXYZ123456
";


            if ($request->alert_type == 'email') {
                if (!$request->alert_config)
                    loi2("Error: not valid email in Alert Config");

                if (empty($m) || !filter_var($m, FILTER_VALIDATE_EMAIL)) {
                    loi2("Error: not valid email in Alert Config: $m");
                }
            }
            if ($request->alert_type == 'telegram') {

                //Kiểm tra xem tài khoản chỉ được tối đa 2 alert_type = telegram thôi
                $count = MonitorUser::countAlertType($request->user()->id, 'telegram');

                if ($count >= 3) {
                    loi2("Error: You can only have a maximum of 3 Telegram alert types.");
                }

                if (!$request->alert_config)
                    loi2($teleError);
                if (!str_contains($m, ','))
                    loi2($teleError);
                $mm = explode(',', $m);

                if (!isValidTelegramBotToken($mm[1])) {
                    loi2($teleError);
                }
                if (!is_numeric($mm[0])) {

                    loi2("Not valid Telegram-ChatID in Alert Config: " . $mm[1]);
                }

                $domain = getDomainHostName();
                // Gửi tin nhắn đơn

                //Bỏ qua email test
                if(getCurrentUserEmail() != 'test001@gmail.com'){
                    $result = TelegramHelper::sendMessage(
                        $mm[1],
                        $mm[0],
                        "$domain: Test valid account bot telegram:

    This is test telegram message!"
                    );
                    $ret = ($result['success'] ?? '');
                    if (!$result || !$ret) {
                        loi2("Error sending Telegram message: " . ($result['error'] ?? 'Unknown error'));
                    } else {
    //                echo "Gửi tin Telegram thành công\n";
                    }
                }

                //Gửi thử 1 tin xem:

            }

            if ($request->alert_type == 'webhook') {
                if (!$request->alert_config)
                    loi2("Error: not valid webhook URL in Alert Config");


                //Kiểm tra xem tài khoản chỉ được tối đa 2 alert_type = webhook thôi
                $count = MonitorUser::countAlertType($request->user()->id, 'webhook');

                if ($count >= 3) {
                    loi2("Error: You can only have a maximum of 3 Webhook alert types.");
                }


                if (empty($m) || !filter_var($m, FILTER_VALIDATE_URL)) {
                    loi2("Error: not valid Webhook URL in Alert Config: $m");
                }


                // Test webhook connection
                try {
                    $context = stream_context_create([
                        'http' => [
                            'method' => 'GET',
                            'header' => 'Content-Type: application/json',
                            'timeout' => 10
                        ]
                    ]);
                    $response = file_get_contents($m, false, $context, 0, 1024); // Limit to 1KB
                } catch (Exception $e) {
                    loi2("Error: Cannot connect to webhook URL: " . $e->getMessage());
                }
            }

            if ($request->alert_type == 'sms') {
                if (!$request->alert_config)
                    loi2("Error: not valid phone number in Alert Config");

                // Remove spaces and validate phone number format
                $phone = preg_replace('/\s+/', '', $m);
                if (empty($phone) || !preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
                    loi2("Error: not valid phone number format in Alert Config: $m (Expected format: +84901234567)");
                }

                // Additional check for Vietnamese phone numbers
                if (preg_match('/^\+84/', $phone)) {
                    if (!preg_match('/^\+84[1-9]\d{8,9}$/', $phone)) {
                        loi2("Error: not valid Vietnamese phone number format: $m (Expected format: +84901234567)");
                    }
                }
            }
        }
    }

    public function add(Request $request)
    {
        $this->checkValidTypeConfig($request);
        return parent::add($request); // TODO: Change the autogenerated stub
    }

    public function update($id, Request $request)
    {
        try {
            $this->checkValidTypeConfig($request);
        }
        catch (Exception $e){
            return rtJsonApiError("". $e->getMessage()."");
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($request->all());
//        echo "</pre>";
//
//        die();
        return parent::update($id, $request); // TODO: Change the autogenerated stub
    }
}
