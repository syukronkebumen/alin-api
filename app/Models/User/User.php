<?php

namespace App\Models\User;

use App\Models\Agency\Agency;
use App\Models\Subscription\Subscription;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Passport\Passport;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    public $table = "user";
    public $primaryKey = "userCode";
    protected $fillable = [
        'name',
        'email',
        'password',
        'isActive',
        'otp',
        'status',
        'agencyCode',
        'createAt'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function getUpdatedAtColumn()
    {
        return 'updateAt';
    }
    protected function serializeDate(\DateTimeInterface $date)
    {
        // Convert the date to UNIX Epoch time or UTC format
        return $date->format('U'); // For UNIX Epoch time
        // return $date->toIso8601String(); // For UTC format
    }

    public $timestamps = false;

    public function getUser($email)
    {
        $getUser = User::select(
            'userCode',
            'email',
            'password',
            'isActive',
            'status',
            'agencyCode'
        )->where('email', $email)
            ->first();

        return $getUser;
    }
}
