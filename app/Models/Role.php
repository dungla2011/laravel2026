<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends ModelGlxBase
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function permissions()
    {
        //        $obj = new Permission();
        //        $obj->setPrimaryKey('key_code');
        $ret = $this->belongsToMany(Permission::class,
            'permission_role', 'role_id', 'permission_id', 'id', 'route_name_code')->withTimestamps();

        //        $obj->setPrimaryKey('id');
        return $ret;
    }

    static function getAllUrlAllowRoleId($roleId)
    {
        $role = self::find($roleId);
        $ret = [];
        foreach ($role->permissions as $permission) {
            $ret[] = $permission->url;
        }
        return $ret;
    }

    static function getAllRouteNameAllowRoleId($roleId)
    {
        $role = self::find($roleId);
        $ret = [];
        foreach ($role->permissions as $permission) {
            $ret[] = $permission->route_name_code;
        }
        return $ret;
    }

    static function checkRouteNameAllowRoleId($roleId, $url)
    {
        $role = self::find($roleId);
        foreach ($role->permissions as $permission) {
            if ($permission->route_name_code == $url) {
                return true;
            }
        }
        return false;
    }

    static function checkUrlAllowRoleId($roleId, $url)
    {
        $role = self::find($roleId);
        foreach ($role->permissions as $permission) {
            if ($permission->url == $url) {
                return true;
            }
        }
        return false;
    }
}
