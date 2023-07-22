<?php

namespace App\Models\Agency;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;

    public $table = "agency";
    protected $fillable = [
        'agencyCode',
        'name',
        'email',
        'noHp',
        'address',
        'logo',
        'domain',
        'createAt',
        'updateAt',
        'deleteAt'
    ];

    public function getAgency($agencyCode)
    {
        $getAgency = Agency::select(
            'agencyCode',
            'name',
            'email',
            'noHp',
            'address',
            'logo',
            'domain'
        )->where('agencyCode', $agencyCode)
            ->whereNull('deleteAt')
            ->first();

        return $getAgency;
    }
}
