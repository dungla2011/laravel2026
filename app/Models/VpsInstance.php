<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Laravel\Database\TraitModelExtra;

class VpsInstance extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra, UnixTimeId;
    protected $guarded = [];

    /**
     * Many-to-many relationship: VPS has many users
     * Users who have access to this VPS (owner, admins, managers, etc)
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'vps_and_users', 'instance_id', 'user_id')
            ->withPivot('role', 'created_at', 'updated_at', 'deleted_at')
            ->withTimestamps();
    }

    /**
     * Get primary owner of VPS (user_id from original table)
     * Keep for backward compatibility
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Find VPS by MAC address and return init_ip
     *
     * @param string $mac MAC address (can be in any case, with/without colons)
     * @return string|null init_ip or null if not found
     */
    public static function getInitIpByMac($mac)
    {
        // Normalize MAC address to lowercase
        $mac = strtolower($mac);

        // Search for VPS with matching MAC address
        $vps = self::whereRaw("LOWER(init_mac_address) LIKE ?", ["%{$mac}%"])
            ->whereNotNull('init_ip')
            ->first();

        return $vps ? $vps->init_ip : null;
    }

}
