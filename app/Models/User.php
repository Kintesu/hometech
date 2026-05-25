<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users'; // Chỉ định đúng bảng [cite: 415]
    public $timestamps = false; // Tắt timestamps nếu bảng của bạn không có created_at

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'role',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
    ];
}
