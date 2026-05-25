<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('orders') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('orders')
            ->where('status', 'Confirmed')
            ->update(['status' => 'Pending']);

        DB::table('orders')
            ->where('status', 'AwaitingInstallation')
            ->update(['status' => 'Shipping']);

        DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Shipping','Completed','Canceled') NULL DEFAULT 'Pending'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('orders') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE orders MODIFY status ENUM('Pending','Confirmed','Shipping','Completed','Canceled','AwaitingInstallation') NULL DEFAULT 'Pending'");
    }
};
