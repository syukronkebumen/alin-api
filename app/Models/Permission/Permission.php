<?php

namespace App\Models\Permission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    public $table = ('permission');
    protected $primaryKey = 'permissionCode';
    protected $fillable = [
        'permission',
        'description',
        'moduleCode',
        'deleteAt',
    ];
    const UPDATED_AT = 'updateAt';

    public function getPermission($userCode)
    {
        $getPermission = Permission::select(
            'permission.permissionCode',
            'permission',
            'description'
        )->leftjoin('role_permission', 'role_permission.permissionCode', '=', 'permission.permissionCode')
            ->leftjoin('role', 'role.roleCode', '=', 'role_permission.roleCode')
            ->leftjoin('role_user', 'role.roleCode', '=', 'role_user.roleCode')
            ->where('role_user.userCode', '=', $userCode)
            ->whereNull('role.deleteAt')
            ->whereNull('role_permission.deleteAt')
            ->whereNull('role_user.deleteAt')
            ->get();

        return $getPermission;
    }

    public function getSpecialPermission($userCode)
    {
        $getSpecialPermission = Permission::select(
            'permission.permissionCode',
            'permission',
            'description'
        )->leftjoin('role_permission', 'role_permission.permissionCode', '=', 'permission.permissionCode')
            ->leftjoin('user_permission', 'user_permission.permissionCode', '=', 'permission.permissionCode')
            ->where('user_permission.userCode', '=', $userCode)
            ->whereNull('user_permission.deleteAt')
            ->get();

        return $getSpecialPermission;
    }
}
