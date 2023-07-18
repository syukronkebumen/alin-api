<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    public $table = ('subscription');
    protected $primaryKey = 'subscriptionCode';
    protected $fillable = [
        'agencyCode',
        'appCode',
        'price',
        'setting',
        'startDate',
        'endDate',
        'status',
        'deleteAt',
    ];
    const UPDATED_AT = 'updateAt';

}
