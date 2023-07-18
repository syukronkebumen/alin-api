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

}
