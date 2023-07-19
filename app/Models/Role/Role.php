<?php

namespace App\Models\Role;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public $table = ('role');
    protected $primaryKey = 'roleCode';
    protected $fillable = [
        'role',
        'agencyCode',
        'status',
        'deleteAt',
    ];
    const UPDATED_AT = 'updateAt';

}
