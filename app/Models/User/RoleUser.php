<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleUser extends Model
{
    use HasFactory;
    public $table = "role_user";
    public $primaryKey = "ruCode";
    protected $fillable = [
        'ruCode', 'roleCode', 'userCode',
    ];
}
