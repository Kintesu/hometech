<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WarrantyLookupTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('warranties');
        Schema::dropIfExists('product_serials');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('users');

        Schema::create('categories', function ($table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->string('full_name', 100);
            $table->string('role')->nullable()->default('Customer');
            $table->string('phone', 15)->nullable();
            $table->text('address')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('products', function ($table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id')->nullable();
            $table->decimal('price', 15, 2)->default(0);
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
        });

        Schema::create('orders', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->decimal('total_price', 15, 2)->default(0);
            $table->string('status')->nullable();
        });

        Schema::create('product_serials', function ($table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('serial_number', 100)->unique();
            $table->string('distributed_by', 100)->default('HomeTech');
            $table->string('status', 30)->default('Sold');
            $table->timestamps();
        });

        Schema::create('warranties', function ($table) {
            $table->id();
            $table->unsignedBigInteger('product_serial_id');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('activation_date')->nullable();
            $table->unsignedSmallInteger('warranty_months')->default(12);
            $table->string('status', 30)->default('Active');
            $table->string('service_status', 30)->nullable();
            $table->timestamp('service_received_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_customer_can_lookup_warranty_by_serial(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'Máy giặt Toshiba Inverter 8.5 kg',
        ]);

        $serialId = DB::table('product_serials')->insertGetId([
            'product_id' => $productId,
            'serial_number' => 'HT-SN-001',
            'status' => 'Sold',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('warranties')->insert([
            'product_serial_id' => $serialId,
            'customer_phone' => '0901234567',
            'purchase_date' => now()->subMonths(1)->toDateString(),
            'activation_date' => now()->subMonths(1)->toDateString(),
            'warranty_months' => 12,
            'status' => 'Active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post('/tra-cuu-bao-hanh', [
            'lookup_method' => 'serial',
            'keyword' => 'HT-SN-001',
        ]);

        $response->assertOk();
        $response->assertSee('Máy giặt Toshiba Inverter 8.5 kg');
        $response->assertSee('HT-SN-001');
        $response->assertSee('Còn hạn');
    }

    public function test_customer_sees_processing_status_when_product_is_under_warranty_service(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'Tủ lạnh Sharp Inverter 401 lít',
        ]);

        $serialId = DB::table('product_serials')->insertGetId([
            'product_id' => $productId,
            'serial_number' => 'HT-SN-REPAIR',
            'status' => 'Sold',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('warranties')->insert([
            'product_serial_id' => $serialId,
            'customer_phone' => '0911111111',
            'purchase_date' => now()->subMonths(2)->toDateString(),
            'activation_date' => now()->subMonths(2)->toDateString(),
            'warranty_months' => 24,
            'status' => 'Repairing',
            'service_status' => 'Repairing',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->post('/tra-cuu-bao-hanh', [
            'lookup_method' => 'phone',
            'keyword' => '0911111111',
        ]);

        $response->assertOk();
        $response->assertSee('Tủ lạnh Sharp Inverter 401 lít');
        $response->assertSee('Đang xử lý bảo hành tại trung tâm');
    }

    public function test_customer_sees_not_found_message_for_unknown_lookup_data(): void
    {
        $response = $this->post('/tra-cuu-bao-hanh', [
            'lookup_method' => 'serial',
            'keyword' => 'UNKNOWN-SERIAL',
        ]);

        $response->assertOk();
        $response->assertSee('Không tìm thấy thông tin bảo hành cho dữ liệu bạn vừa nhập. Vui lòng kiểm tra lại!');
    }
}
