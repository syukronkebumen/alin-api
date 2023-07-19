<?php

namespace App\Models\RolePermission;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    use HasFactory;
    public $table = ('role_permission');
    protected $primaryKey = 'rpCode';
    protected $fillable = [
        'permissionCode',
        'roleCode',
        'deleteAt',
    ];
    const UPDATED_AT = 'updateAt';

}
