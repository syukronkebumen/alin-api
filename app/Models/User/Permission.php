<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    public $table = "permission";
    public $primaryKey = "permissionCode";
    protected $fillable = [
        'permissionCode', 'permission', 'description'
    ];
}
