<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Permission extends Authenticatable
{
    use HasFactory;
    public $table = "permission";
    public $primaryKey = "permissionCode";
    protected $fillable = [
        'permissionCode', 'permission', 'description'
    ];
}
