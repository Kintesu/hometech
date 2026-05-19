<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = false; // Tắt nếu bảng categories của bạn không có created_at

    protected $fillable = ['name'];
}