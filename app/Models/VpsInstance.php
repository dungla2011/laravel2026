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
