<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class UserRecharge_Meta extends MetaOfTableInDb
{
    public static $modelClass = UserRecharge::class;
    public static $modelName = 'UserRecharge';

    public static function getCoreFields()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Người dùng',
            'amount' => 'Số tiền (đ)',
            'payment_method' => 'Hình thức',
            'status' => 'Trạng thái',
            'paid_at' => 'Thanh toán lúc',
            'completed_at' => 'Hoàn tất lúc',
        ];
    }

    public static function _name($item, $typeGet = '')
    {
        if (!$item) return '';

        $statusColor = match($item->status) {
            'completed' => '✅ Hoàn tất',
            'pending' => '⏳ Chờ',
            'processing' => '⚙️ Đang xử lý',
            'failed' => '❌ Thất bại',
            'cancelled' => '🚫 Hủy',
            default => $item->status
        };

        $completedInfo = $item->completed_at ? 'Hoàn tất: ' . $item->completed_at : 'Chưa hoàn tất';

        return <<<HTML
            <div style="padding: 10px; border: 1px solid #ddd; border-radius: 4px;">
                <div><strong>Recharge #{$item->id}</strong> - User #{$item->user_id}</div>
                <table style="width: 100%; margin-top: 8px; border-collapse: collapse;">
                    <tr style="background: #f5f5f5;">
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Số tiền</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Phương thức</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>Trạng thái</strong></td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd;"><strong>{$item->amount}</strong></td>
                        <td style="padding: 8px; border: 1px solid #ddd;">{$item->payment_method}</td>
                        <td style="padding: 8px; border: 1px solid #ddd;">$statusColor</td>
                    </tr>
                </table>
                <div style="margin-top: 8px; font-size: 12px;">
                    {$completedInfo}
                </div>
            </div>
        HTML;
    }
}
