<?php

namespace App\Models\Agency;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    public $table = ('agency');
    protected $primaryKey = 'agencyCode';
    protected $fillable = [
        'name',
        'email',
        'noHp',
        'address',
        'logo',
        'domain',
        'deleteAt',
    ];
    const UPDATED_AT = 'updateAt';

}
