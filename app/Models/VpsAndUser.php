<?php
/*
<?php

Giờ relationships đã sẵn sàng dùng ($user->vpsInstances(), $vps->users(), etc) 🎉


$vps->users() // Tất cả users có access VPS này
$vps->owner() // Primary owner (backward compat, từ user_id)

// Lấy tất cả VPS của user
$vpsOfUser = $user->vpsInstances()->get();

// Lấy tất cả users của 1 VPS
$usersOfVps = $vps->users()->get();

// Lấy với role
$admins = $vps->users()->wherePivot('role', 'admin')->get();

// Attach user vào VPS với role
$vps->users()->attach($userId, ['role' => 'manager']);

// Update role
$vps->users()->updateExistingPivot($userId, ['role' => 'admin']);

// Check role level
if ($vpsAndUser->hasRoleLevel('admin')) {
    // user là admin trở lên
}
*/
namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * VPS and User Pivot Model
 * Manages the many-to-many relationship between Users and VPS Instances
 *
 * This pivot table allows:
 * - 1 VPS to have multiple users (owner, admins, managers, viewers)
 * - 1 User to have access to multiple VPS instances
 *
 * Roles available: 'owner', 'admin', 'manager', 'viewer'
 */


class VpsAndUser extends ModelGlxBase//Pivot
{
    use SoftDeletes;

    protected $table = 'vps_and_users';
    protected $guarded = [];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    /**
     * Role constants for easy reference
     */
    public const ROLE_OWNER = 'owner';
    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_VIEWER = 'viewer';

    /**
     * Get all available roles
     */
    public static function getRoles()
    {
        return [
            self::ROLE_OWNER => 'Owner (Full control)',
            self::ROLE_ADMIN => 'Admin (Manage users & settings)',
            self::ROLE_MANAGER => 'Manager (Day-to-day operations)',
            self::ROLE_VIEWER => 'Viewer (Read-only access)',
        ];
    }

    /**
     * Check if has specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if has at least specified role level
     * Owner > Admin > Manager > Viewer
     */
    public function hasRoleLevel($minRole)
    {
        $roleHierarchy = [
            self::ROLE_OWNER => 4,
            self::ROLE_ADMIN => 3,
            self::ROLE_MANAGER => 2,
            self::ROLE_VIEWER => 1,
        ];

        return ($roleHierarchy[$this->role] ?? 0) >= ($roleHierarchy[$minRole] ?? 0);
    }

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to VpsInstance
     */
    public function instance()
    {
        return $this->belongsTo(VpsInstance::class, 'instance_id');
    }
}
