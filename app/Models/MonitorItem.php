<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MonitorItem extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    static function countEnableByUser($uid)
    {
        return MonitorItem::where(['user_id' => $uid, 'enable' => 1])->count();
    }
    public function getValidateRuleInsert()
    {
        $meta = new MonitorItem_Meta();
        $m1 = $meta->_check_interval_seconds(null, null, null);

        $mkey = array_keys($m1);
        //Remove phần tử đầu tiên
        array_shift($mkey);
        $mkey = implode(",", $mkey);

        //validate url_check phải là domain hoặc url hợp lệ:
        // Chấp nhận các định dạng:
        // 1. Domain (example.com)
        // 2. Domain:port (example.com:8080)
        // 3. URL đầy đủ (http://example.com hoặc https://example.com:8080)
        // 4. IPv4 address (10.0.1.129, 192.168.1.1)
        // 5. IPv4:port (10.0.1.129:8080)
        // ❌ KHÔNG cho phép: localhost, 127.0.0.1, 127.x.x.x

        return [
            'name' => 'required|string|max:255',
            'url_check' => [
                'sometimes',
                'max:512',
                // Regex: domain, IPv4 (KHÔNG phải 127.x.x.x), với/không có port/protocol
                'regex:/^(?:(?:https?:\/\/)?(?:(?:(?:[a-zA-Z0-9](?:[a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,})|(?:(?:(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])))(?::\d{1,5})?(?:\/[^\s]*)?)$/',
                // Validate: KHÔNG phải localhost
                'not_regex:/^(?:https?:\/\/)?localhost(?::\d{1,5})?(?:\/.*)?$/i',
                // Validate: KHÔNG phải 127.x.x.x
                'not_regex:/^(?:https?:\/\/)?127\.(?:[0-9]{1,3}\.){2}[0-9]{1,3}(?::\d{1,5})?(?:\/.*)?$/',
            ],
            'check_interval_seconds' => "sometimes|integer|in:$mkey",
            // Thêm các quy tắc xác thực khác nếu cần
        ];
    }

    public function getValidateRuleUpdate()
    {
        return $this->getValidateRuleInsert();
    }

    /**
     * Relationship với MonitorConfig qua pivot table monitor_and_configs
     */
    public function alertConfigs()
    {
        return $this->belongsToMany(MonitorConfig::class, 'monitor_and_configs', 'monitor_item_id', 'config_id');
    }

    /**
     * Thêm alert config vào monitor item
     *
     * @param int $itemId ID của monitor item
     * @param int $alertId ID của alert config
     * @return bool
     */
    public function attachAlertToItem($itemId, $alertId)
    {
        try {

            //Xem monitor_item_id có phải của user không, nếu ko thì báo lỗi:
            $uid = getCurrentUserId();

            if(!isSupperAdmin_()){
                $userMonitorItems = MonitorItem::where("user_id", $uid)->pluck('id')->toArray();
                if (!in_array($itemId, $userMonitorItems)) {
                    loi2("Không có quyền truy cập item id");
                }
                $userMonitorItems = MonitorConfig::where("user_id", $uid)->pluck('id')->toArray();
                if (!in_array($alertId, $userMonitorItems)) {
                    loi2("Không có quyền truy cập config id");
                }
            }

//            die("$itemId, $alertId ");
            // Kiểm tra xem relationship đã tồn tại chưa
            $exists = DB::table('monitor_and_configs')
                ->where('monitor_item_id', $itemId)
                ->where('config_id', $alertId)
                ->exists();

            if (!$exists) {
//                die("not exist");
                // Thêm bản ghi mới vào pivot table
                DB::table('monitor_and_configs')->insert([
                    'monitor_item_id' => $itemId,
                    'config_id' => $alertId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                return true;
            }
//            die("exist123");

            // Đã tồn tại, không cần thêm
            return true;

        } catch (\Exception $e) {
//            return rtJsonApiError("Error attaching alert to item: " . $e->getMessage());
//            Log::error('Error attaching alert to item: ' . $e->getMessage());
            loi2($e->getMessage());
        }
    }

    /**
     * Xóa alert config khỏi monitor item
     *
     * @param int $itemId ID của monitor item
     * @param int $alertId ID của alert config
     * @return bool
     */
    public function detachAlertFromItem($itemId, $alertId)
    {
        try {

                        //Xem monitor_item_id có phải của user không, nếu ko thì báo lỗi:
            $uid = getCurrentUserId();
            if(!isSupperAdmin_()) {
                $userMonitorItems = MonitorItem::where("user_id", $uid)->pluck('id')->toArray();

                if (!in_array($itemId, $userMonitorItems)) {
                    loi2("Không có quyền truy cập item id");
                }
                $userMonitorItems = MonitorConfig::where("user_id", $uid)->pluck('id')->toArray();
                if (!in_array($alertId, $userMonitorItems)) {
                    loi2("Không có quyền truy cập config id");
                }
            }
            // Xóa bản ghi khỏi pivot table
            $deleted = DB::table('monitor_and_configs')
                ->where('monitor_item_id', $itemId)
                ->where('config_id', $alertId)
                ->delete();

            return $deleted > 0 || true; // Trả về true ngay cả khi không có gì để xóa

        } catch (\Exception $e) {
            loi2('Error detaching alert from item: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sync alert configs cho monitor item theo chuẩn Laravel
     *
     * @param array $configIds Mảng config IDs cần sync
     * @return array Kết quả sync (attached, detached, updated)
     */
    public function syncAlertConfigs($configIds = [])
    {
        try {
            $uid = getCurrentUserId();

            // Kiểm tra quyền truy cập monitor item
            if (!isSupperAdmin_()) {
                if ($this->user_id !== $uid) {
                    loi2("Không có quyền truy cập monitor item ID: {$this->id}");
                }
            }

            // Lọc chỉ lấy config IDs thuộc về user hiện tại
            $validConfigIds = [];
            if (!empty($configIds)) {
                if (isSupperAdmin_()) {
                    // Admin có thể sync tất cả configs
                    $validConfigIds = MonitorConfig::whereIn('id', $configIds)->pluck('id')->toArray();
                } else {
                    // User chỉ có thể sync configs của mình
                    $validConfigIds = MonitorConfig::where('user_id', $uid)
                        ->whereIn('id', $configIds)
                        ->pluck('id')
                        ->toArray();
                }
            }

            // Sử dụng Laravel sync() - tự động attach/detach
            $result = $this->alertConfigs()->sync($validConfigIds);

            return [
                'success' => true,
                'attached' => $result['attached'] ?? [],
                'detached' => $result['detached'] ?? [],
                'updated' => $result['updated'] ?? [],
                'total_configs' => count($validConfigIds),
                'message' => 'Alert configs synced successfully'
            ];

        } catch (\Exception $e) {
            loi2('Error syncing alert configs: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Static method để sync alert configs cho một monitor item cụ thể
     *
     * @param int $itemId Monitor item ID
     * @param array $configIds Mảng config IDs
     * @return array
     */
    public static function syncAlertsForItem($itemId, $configIds = [])
    {
        $monitorItem = self::findOrFail($itemId);
        return $monitorItem->syncAlertConfigs($configIds);
    }

    /**
     * Lấy danh sách alert configs hiện tại của monitor item
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCurrentAlertConfigs()
    {
        return $this->alertConfigs()->get();
    }

    /**
     * Lấy danh sách config IDs hiện tại của monitor item
     *
     * @return array
     */
    public function getCurrentAlertConfigIds()
    {
        return $this->alertConfigs()->pluck('config_id')->toArray();
    }

    /**
     * Kiểm tra monitor item có alert config cụ thể không
     *
     * @param int $configId
     * @return bool
     */
    public function hasAlertConfig($configId)
    {
        return $this->alertConfigs()->where('config_id', $configId)->exists();
    }

    /**
     * Static method để attach alert cho một item cụ thể
     *
     * @param int $itemId
     * @param int $alertId
     * @return bool
     */
    public static function attachAlert($itemId, $alertId)
    {
        $instance = new static();
        return $instance->attachAlertToItem($itemId, $alertId);
    }

    /**
     * Static method để detach alert cho một item cụ thể
     *
     * @param int $itemId
     * @param int $alertId
     * @return bool
     */
    public static function detachAlert($itemId, $alertId)
    {
        $instance = new static();
        return $instance->detachAlertFromItem($itemId, $alertId);
    }


    /**
     * Disable Laravel timestamps if not using them
     * Set to false if your table doesn't have created_at/updated_at
     */
    // public $timestamps = false;

    // =================================================================
    // QUERY SCOPES
    // =================================================================

    /**
     * Scope: Chỉ lấy monitors đã enable
     */
    public function scopeEnabled($query, $user_id = null)
    {
//        die("UID1 = $user_id");
        if($user_id)
            $query = $query->where('user_id', $user_id);
        return $query->where('enable', true);
    }

    /**
     * Scope: Lọc theo status
     */
    public function scopeByStatus($query, $status, $user_id = null)
    {
        if($user_id)
            $query = $query->where('user_id', $user_id);
        if ($status === 'online') {
            return $query->where('last_check_status', 1);
        } elseif ($status === 'offline') {
            return $query->where('last_check_status', -1);
        }
        return $query;
    }

    /**
     * Scope: Tìm kiếm theo tên hoặc URL
     */
    public function scopeSearch($query, $search, $user_id = null)
    {
        if($user_id)
            $query = $query->where('user_id', $user_id);
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('url_check', 'like', "%{$search}%");
        });
    }

    /**
     * Scope: Lọc theo user_id
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: Active threads (checked trong X phút)
     */
    public function scopeActiveThreads($query, $minutes = 5, $user_id = null)
    {
        return $query->where('last_check_time', '>', now()->subMinutes($minutes));
    }

    /**
     * Scope: Warning monitors (chưa check hoặc check lâu)
     */
    public function scopeWarning($query, $minutes = 10, $user_id = null)
    {
        return $query->where(function($q) use ($minutes) {
            $q->whereNull('last_check_status')
                ->orWhere('last_check_time', '<', now()->subMinutes($minutes));
        });
    }

    // =================================================================
    // ACCESSORS & MUTATORS
    // =================================================================

    /**
     * Accessor: Format status thành string
     */
    public function getStatusAttribute()
    {
        if ($this->last_check_status === 1) return 'online';
        if ($this->last_check_status === -1) return 'offline';
        return 'unknown';
    }

    /**
     * Accessor: Mock response time (có thể lưu trong bảng riêng)
     */
    public function getResponseTimeAttribute()
    {
        // Trong thực tế, có thể lưu response time trong bảng riêng
        // hoặc cache trong Redis
        return round(mt_rand(50, 500) + (mt_rand(0, 100) / 100), 2);
    }

    /**
     * Accessor: Format uptime percentage
     */
    public function getUptimePercentageAttribute()
    {
        $total = $this->count_online + $this->count_offline;
        if ($total === 0) return 100;

        return round(($this->count_online / $total) * 100, 2);
    }

    /**
     * Accessor: Last check time human readable
     */
    public function getLastCheckTimeHumanAttribute()
    {
        return $this->last_check_time ? $this->last_check_time->diffForHumans() : 'Never';
    }

    // =================================================================
    // STATIC METHODS - STATISTICS & UTILITIES
    // =================================================================

    /**
     * Static: Thống kê tổng quan cho dashboard
     */
    public static function getStatistics($user_id)
    {
        $stats = [];

        // Tổng monitors enabled
        $stats['total_monitors'] = self::enabled($user_id)->count();

        // Đang online
        $stats['online_count'] = self::enabled($user_id)->byStatus('online')->count();

        // Đang offline
        $stats['offline_count'] = self::enabled($user_id)->byStatus('offline')->count();

        // Warning (chưa check hoặc check lâu)
        $stats['warning_count'] = self::enabled($user_id)->warning()->count();

        // Active threads (checked trong 5 phút)
        $stats['active_threads'] = self::enabled($user_id)->activeThreads(5)->count();

        // Top users by monitor count
        $stats['top_users'] = self::enabled()
            ->selectRaw('user_id, COUNT(*) as count, AVG(count_online + count_offline) as avg_checks')
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function($item) {
                return [
                    'user_id' => $item->user_id,
                    'monitor_count' => $item->count,
                    'avg_checks' => round($item->avg_checks, 0)
                ];
            })
            ->toArray();

        // Phân bố check types
        $stats['type'] = self::enabled()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get()
            ->map(function($item) {
                return [
                    'type' => $item->type,
                    'count' => $item->count
                ];
            })
            ->toArray();

        // Uptime statistics
        $stats['uptime_stats'] = [
            'excellent' => self::enabled()->whereRaw('(count_online / (count_online + count_offline)) >= 0.99')->count(),
            'good' => self::enabled()->whereRaw('(count_online / (count_online + count_offline)) >= 0.95 AND (count_online / (count_online + count_offline)) < 0.99')->count(),
            'poor' => self::enabled()->whereRaw('(count_online / (count_online + count_offline)) < 0.95')->count(),
        ];

        $stats['last_updated'] = now()->format('Y-m-d H:i:s');

        return $stats;
    }

    /**
     * Static: Đếm active threads với thời gian tùy chỉnh
     */
    public static function countActiveThreads($minutes = 5, $user_id = null)
    {
        return self::enabled($user_id)->activeThreads($minutes)->count();
    }

    /**
     * Static: Get monitors cần attention (offline hoặc warning)
     */
    public static function getNeedAttention($limit = 10, $user_id = null)
    {
        return self::enabled()
            ->where(function($q) {
                $q->byStatus('offline')
                    ->orWhere(function($q2) {
                        $q2->warning(10);
                    });
            })
            ->orderByDesc('last_check_time')
            ->limit($limit)
            ->get();
    }

    /**
     * Static: Bulk toggle monitors
     */
    public static function bulkToggle($ids, $enable = null)
    {
        $query = self::whereIn('id', $ids);

        if ($enable === null) {
            // Toggle each one individually
            $monitors = $query->get();
            foreach ($monitors as $monitor) {
                $monitor->toggle();
            }
            return $monitors;
        } else {
            // Set all to same status
            return $query->update(['enable' => $enable]);
        }
    }

    // =================================================================
    // INSTANCE METHODS
    // =================================================================

    /**
     * Method: Toggle enable/disable
     */
    public function toggle()
    {
        $this->enable = !$this->enable;
        $this->save();
        return $this;
    }

    /**
     * Method: Update check status
     */
    public function updateCheckStatus($status, $responseTime = null)
    {
        $this->last_check_status = $status;
        $this->last_check_time = now();

        // Increment counters
        if ($status == 1) {
            $this->count_online++;
        } else {
            $this->count_offline++;
        }

        // Store response time if provided (cần thêm column hoặc bảng riêng)
        // $this->last_response_time = $responseTime;

        $this->save();
        return $this;
    }

    /**
     * Method: Reset counters
     */
    public function resetCounters()
    {
        $this->count_online = 0;
        $this->count_offline = 0;
        $this->save();
        return $this;
    }

    /**
     * Method: Check if monitor is healthy
     */
    public function isHealthy($maxMinutesSinceLastCheck = 10)
    {
        return $this->enable &&
            $this->last_check_status == 1 &&
            $this->last_check_time &&
            $this->last_check_time->diffInMinutes(now()) <= $maxMinutesSinceLastCheck;
    }

    /**
     * Method: Get URL for checking (với port nếu có)
     */
    public function getCheckUrl()
    {
        $url = $this->url_check;

        if ($this->port && !str_contains($url, ':' . $this->port)) {
            $url = str_replace(['http://', 'https://'], '', $url);
            $url = 'http://' . $url . ':' . $this->port;
        }

        return $url;
    }

    // =================================================================
    // RELATIONSHIPS (nếu cần)
    // =================================================================

    /**
     * Relationship: User (nếu có bảng users)
     */
    // public function user()
    // {
    //     return $this->belongsTo(User::class);
    // }

    /**
     * Relationship: Check logs (nếu có bảng monitor_logs)
     */
    // public function logs()
    // {
    //     return $this->hasMany(MonitorLog::class);
    // }


    public static function createQuotaUser($uid)
    {
        $gpUser = MonitorSetting::where('user_id', $uid)->first();
        if (! $gpUser) {
            $mm = ['user_id' => $uid, 'max_quota_node' => DEF_LRV_DEFAULT_QUOTA_NODE_MONITOR];
            MonitorSetting::create($mm);
        }
        $gpUser = MonitorSetting::where('user_id', $uid)->first();
        if(!$gpUser->max_quota_node) {
            $gpUser->max_quota_node = DEF_LRV_DEFAULT_QUOTA_NODE_MONITOR;
            $gpUser->save();
        }
        return $gpUser;
    }

    static function getCountBuyedNode($uid)
    {
        $nBuyed = 0;
        if($billAndPro = \App\Models\OrderItem::where('user_id', $uid)->get()){
            foreach ($billAndPro as $item){
                if($item->param1)
                    $nBuyed += $item->param1;
            }
        }
        return $nBuyed;
    }

    public static function getCurrentQuota($uid){
        return self::checkQuota($uid, "", 1);
    }


}


