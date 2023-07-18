<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userPermission extends Model
{
    use HasFactory;
    public $table = "user_permission";
    public $primaryKey = "upCode";
    protected $fillable = [
        'upCode',
        'userCode',
        'permissionCode',

    ];
}
