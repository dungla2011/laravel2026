<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\VpsBillingReportService;
use Illuminate\Http\Request;

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

        $data = $this->billingService->generateReportData($user, $options, $fromTime = null, $request->get('to_time') );

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
        if ($usage->price_month && $usage->price_month > 0) {
            // Fixed monthly price calculation
            $pricePerMonth = floatval($usage->price_month);
            $createdTime = new \DateTime($usage->created_at);
            $timestampTime = new \DateTime($usage->timestamp_minute);
            $interval = $createdTime->diff($timestampTime);
            $durationMinutes = ($interval->days * 1440) + ($interval->h * 60) + $interval->i;

            $fee = $pricePerMonth * ($durationMinutes / 43200);
            $fee = round($fee, 0);

            // Convert duration to text
            $days = floor($durationMinutes / 1440);
            $remainingMinutes = $durationMinutes % 1440;
            $hours = floor($remainingMinutes / 60);
            $minutes = $remainingMinutes % 60;

            $details = [
                'type' => 'fixed_monthly',
                'price_month' => $pricePerMonth,
                'duration_minutes' => $durationMinutes,
                'duration_days' => $days,
                'duration_hours' => $hours,
                'duration_mins' => $minutes,
                'fee' => $fee,
                'formula' => sprintf('%s K/tháng × %d phút / 43200 phút = %s K',
                    number_format($pricePerMonth, 0, ',', '.'),
                    $durationMinutes,
                    number_format($fee, 0, ',', '.')
                )
            ];
        } else {
            // Use calculateFee() method
            $feeResult = $usage->calculateFee();
            $details = array_merge($feeResult['details'], [
                'type' => 'config_based',
                'fee' => $feeResult['fee'],
                'formula_text' => $feeResult['text']
            ]);
        }

        // POWERED_OFF = no charge
        if (strtoupper($usage->power_state) === 'POWERED_OFF') {
            $details['fee'] = 0;
            $details['powered_off'] = true;
        }

        return view('vps.billing-report-detail', [
            'usage' => $usage,
            'instance' => $instance,
            'details' => $details
        ]);
    }

    /**
     * Send billing report via email
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendEmail(Request $request)
    {
        // Get user by email or ID
        $email = $request->get('email', 'khanhdh389@gmail.com');
        $user = User::where('email', $email)->firstOrFail();

        // Send email
        $options = [];
        if ($request->has('date_from')) {
            $options['date_from'] = $request->get('date_from');
        }
        if ($request->has('date_to')) {
            $options['date_to'] = $request->get('date_to');
        }

        $success = $this->billingService->sendEmail($user, $options);

        if ($success) {
            return response()->json([
                'success' => true,
                'message' => 'Billing report sent to ' . $user->email
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send billing report'
            ], 500);
        }
    }
}
