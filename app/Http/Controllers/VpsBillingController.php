<?php

namespace App\Http\Controllers;

use App\Models\PartnerInfo;
use App\Models\User;
use App\Models\VpsUsage;
use App\Services\VpsBillingReportService;
use Illuminate\Http\Request;
use LadLib\Common\cstring2;

class VpsBillingController extends Controller
{
    protected $billingService;

    public function __construct(VpsBillingReportService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Display billing report for a user
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {

        // Get user by email or ID
        $user = getCurrentUserId(1);
        if(!$user){
            bl("Login please!");
            return;
        }

        // Generate report data
        $options = [];
        if ($request->has('date_from')) {
            $options['date_from'] = $request->get('date_from');
        }
        if ($request->has('date_to')) {
            $options['date_to'] = $request->get('date_to');
        }
        if ($request->has('limit_vps_id')) {
            $options['limit_vps_id'] = $request->get('limit_vps_id');
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($options);
//        echo "</pre>";
//        die();

        $data = $this->billingService->generateReportData($user, $options, $fromTime = $request->get('from_time'), $request->get('to_time') );

        return view('vps.billing-report', $data);
    }

    /**
     * Download billing report as PDF
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadPdf(Request $request)
    {

        // Get current user
        $user = getCurrentUserId(1);
        if (!$user) {
            bl("Login please!");
            return redirect()->route('login');
        }

        // Generate PDF
        $options = [];
        if ($request->has('date_from')) {
            $options['date_from'] = $request->get('date_from');
        }
        if ($request->has('date_to')) {
            $options['date_to'] = $request->get('date_to');
        }
        if ($request->has('limit_vps_id')) {
            $options['limit_vps_id'] = $request->get('limit_vps_id');
        }

        $pdf = $this->billingService->generatePdf($user, $options, $request->from_time, $request->to_time);

        // Download PDF
        $fileName = sprintf(
            'vps-billing-glx-%s-%s.pdf',
            $user->id,
            now()->format('Y-m-d')
        );

        return $pdf->download($fileName);
    }

    /**
     * Download billing report as Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function downloadExcel(Request $request)
    {
        // Get current user
        $user = getCurrentUserId(1);
        if (!$user) {
            bl("Login please!");
            return redirect()->route('login');
        }

        // Generate Excel
        $options = [];
        if ($request->has('date_from')) {
            $options['date_from'] = $request->get('date_from');
        }
        if ($request->has('date_to')) {
            $options['date_to'] = $request->get('date_to');
        }
        if ($request->has('limit_vps_id')) {
            $options['limit_vps_id'] = $request->get('limit_vps_id');
        }

        return $this->billingService->generateExcel($user, $options, $request->from_time, $request->to_time);
    }

    /**
     * Show detailed calculation for a single vps_usages record
     *
     * @param int $id VpsUsage ID
     * @return \Illuminate\View\View
     */
    public function showDetail($id)
    {
        $usage = \App\Models\VpsUsage::findOrFail($id);

        // Get instance info
        $instance = \App\Models\VpsInstance::find($usage->instance_id);

        // Calculate fee with full details
//        if(0)
//        if ($usage->price_month && $usage->price_month > 0) {
//            // Fixed monthly price calculation
//            $pricePerMonth = floatval($usage->price_month);
//            $createdTime = new \DateTime($usage->created_at);
//            $timestampTime = new \DateTime($usage->timestamp_minute);
//            $interval = $createdTime->diff($timestampTime);
//            $durationMinutes = ($interval->days * 1440) + ($interval->h * 60) + $interval->i;
//
//            $fee = $pricePerMonth * ($durationMinutes / 43200);
//            $fee = round($fee, 0);
//
//            // Convert duration to text
//            $days = floor($durationMinutes / 1440);
//            $remainingMinutes = $durationMinutes % 1440;
//            $hours = floor($remainingMinutes / 60);
//            $minutes = $remainingMinutes % 60;
//
//            $details = [
//                'type' => 'fixed_monthly',
//                'price_month' => $pricePerMonth,
//                'duration_minutes' => $durationMinutes,
//                'duration_days' => $days,
//                'duration_hours' => $hours,
//                'duration_minutes' => $minutes,
//                'fee' => $fee ,
//                'formula' => sprintf('%s K/tháng × %d phút / 43200 phút = %s K',
//                    number_format($pricePerMonth, 0, ',', '.'),
//                    $durationMinutes,
//                    number_format($fee, 0, ',', '.')
//                )
//            ];
//        }
//        else
        $details = [];
        {
            // Use calculateFee() method
            if($usage instanceof  VpsUsage);
            $feeResult = $usage->calculateFee(\request('from_time'), \request('to_time'));
            $details = array_merge($feeResult['details'], [
//                'type' => 'config_based',
                'fee' => $feeResult['fee'] ,
                'formula_text' => $feeResult['text']
            ]);
        }

        // POWERED_OFF = no charge
        if (strtoupper($usage->power_state) === 'POWERED_OFF') {
            $details['fee'] = 0;
            $details['powered_off'] = true;
        }

//        dump($details);
//        return;

        return view('vps.billing-report-detail', [
            'usage' => $usage,
            'instance' => $instance,
            'details' => $details
        ]);
    }

    /**
     * Show form to edit email before sending
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function sendEmailForm(Request $request)
    {
        // Get current user
        $user = getCurrentUserId(1);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login');
        }

        $sv = new VpsBillingReportService();
        $data = $sv->generateReportData($user, $request->toArray(), $request->from_time, $request->to_time);

        // Handle limit_vps_id filter if provided
        if ($request->has('limit_vps_id')) {
            $options = ['limit_vps_id' => $request->get('limit_vps_id')];
            $data = $sv->generateReportData($user, $options, $request->from_time, $request->to_time);
        }

        $totalRecharge = $data['totalRecharge'];
        $totalCost = $data['totalCost'];

        $pnName = $user->email;
        if($pn = PartnerInfo::where('user_id', $user->id)->first()){
            if($pn->name){
                $pnName = $pn->name;
            }
            else
            if($pn->partner_name ){
                $pnName = $pn->partner_name ;
            }
        }

        $data['user'] = '';
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($data);
//        echo "</pre>";
//
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($options);
//        echo "</pre>";

//        die();
        $totalRechargeNoVat = $totalRecharge / 1.1;

        $needPay = $totalCost - $totalRechargeNoVat;
        $needPayDot = number_formatvn0($needPay);
        $needPayStrVn = cstring2::toTienVietNamString3($needPay);

        $needPayVAT = $needPay / 10;
        $needPayFullVAT = $needPay * 1.1;
        $needPayVATDot = number_formatvn0($needPayVAT);
        $needPayFullVATDot = number_formatvn0($needPayFullVAT);
        $needPayFullVATDotStr = cstring2::toTienVietNamString3($needPayFullVAT);
        $toTimeVN = nowy_vn(null, "/");
        if($request->to_time)
            $toTimeVN = nowy_vn(strtotime($request->to_time), "/");

//        die("$needPay , $toTimeVN , $needPayDot, $needPayStrVn , $user->email, Total = $totalRechargeNoVat ,  $totalCost, toTime = $request->to_time");

        // Default email content
        $title = "GalaxyCloud - Thanh toán dịch vụ - $toTimeVN - $pnName";
        $content = "Kính chào Quý khách!\n\nGalaxyCloud xin thông báo phí dịch vụ Server!" .
            "\nChi phí thanh toán: $needPayDot + $needPayVATDot (VAT) = $needPayFullVATDot VND\n($needPayFullVATDotStr)".
            "\nThời gian sử dụng: $toTimeVN".

            "\n\nQuý khách vui lòng thanh toán qua Số tài khoản thanh toán:".
            "\n110389898, Công ty TNHH Công nghệ số Galaxy Việt nam".
            "\nTại: Ngân hàng Thương mại Cổ phần Á Châu (ACB)".
            "\n\nChi tiết vui lòng xem trong File đính kèm email.".
            "\n\nXin trân trọng cảm ơn Quý khách!"
           ;

//        die($title . $content);
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($request->all());
//        echo "</pre>";
//
//        die();
        return view('vps.send-email-form', [
            'user' => $user,
            'title' => $title,
            'content' => $content,
            'filters' => $request->all(),
            'report_url' => route('vps.billing.report', $request->all())
        ]);
    }

    /**
     * Send billing report via email with custom title and content
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function sendEmailPage(Request $request)
    {
        // Get current user
        $user = getCurrentUserId(1);
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login');
        }

        // Get title and content from form
        $title = $request->get('title', 'GalaxyCloud - Thanh toán dịch vụ');
        $content = $request->get('content', '');

        // Get filter options
        $options = [];
        if ($request->has('date_from')) {
            $options['date_from'] = $request->get('date_from');
        }
        if ($request->has('date_to')) {
            $options['date_to'] = $request->get('date_to');
        }
        if ($request->has('limit_vps_id')) {
            $options['limit_vps_id'] = $request->get('limit_vps_id');
        }

        if ($request->has('debug')) {
            $options['debug'] = 1;
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($request->all());
//        echo "</pre>";


        // Send email with custom title and content
        $success = $this->billingService->sendEmail($user, $options, $title, $content, $request->from_time, $request->to_time);

//        die("OK  = $success");

        if ($success) {
            return view('vps.send-email-success', [
                'user' => $user,
                'message' => 'Billing report has been sent to ' . $user->email,
                'report_url' => route('vps.billing.report', $request->only(['date_from', 'date_to', 'to_time']))
            ]);
        } else {
            return view('vps.send-email-error', [
                'user' => $user,
                'message' => 'Failed to send billing report. Please try again.',
                'report_url' => route('vps.billing.report', $request->only(['date_from', 'date_to', 'to_time']))
            ]);
        }
    }
}
