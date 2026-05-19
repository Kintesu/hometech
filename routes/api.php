<?php

use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

// Khai báo nhóm API cho Sản phẩm
Route::prefix('products')->group(function () {
    // API lấy danh sách tất cả sản phẩm: GET /api/products
    Route::get('/', [ProductController::class, 'index']);
    
    // API lấy chi tiết 1 sản phẩm: GET /api/products/{id}
    Route::get('/{id}', [ProductController::class, 'show']);
});