<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public $table = "role";
    public $primaryKey = "roleCode";
    protected $fillable = [
        'roleCode', 'role','agencyCode','deleteAt'
    ];
    public function getUpdatedAtColumn()
    {
        return 'updateAt';
    }
}
