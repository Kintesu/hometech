<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('product_serials')) {
            Schema::create('product_serials', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->string('serial_number', 100)->unique();
                $table->string('distributed_by', 100)->default('HomeTech');
                $table->string('status', 30)->default('Available')->index();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('warranties')) {
            Schema::create('warranties', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('product_serial_id')->index();
                $table->unsignedBigInteger('order_id')->nullable()->index();
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('customer_phone', 20)->nullable()->index();
                $table->date('purchase_date')->nullable();
                $table->date('activation_date')->nullable();
                $table->unsignedSmallInteger('warranty_months')->default(12);
                $table->string('status', 30)->default('Active')->index();
                $table->string('service_status', 30)->nullable()->index();
                $table->timestamp('service_received_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('warranties');
        Schema::dropIfExists('product_serials');
    }
};
