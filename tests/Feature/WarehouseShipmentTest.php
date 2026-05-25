<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class WarehouseShipmentTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_details');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('products');
        Schema::dropIfExists('users');

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
            $table->decimal('price', 15, 2);
            $table->integer('stock_quantity')->default(0);
            $table->boolean('requires_installation')->default(false);
            $table->string('image')->nullable();
        });

        Schema::create('orders', function ($table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->decimal('total_price', 15, 2);
            $table->string('status')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('received_amount', 15, 2)->nullable();
            $table->decimal('change_amount', 15, 2)->nullable();
            $table->text('delivery_address')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('assigned_staff_tech_id')->nullable();
            $table->timestamp('installation_assigned_at')->nullable();
            $table->timestamp('installation_completed_at')->nullable();
        });

        Schema::create('order_status_histories', function ($table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('changed_by')->nullable();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('reason')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('order_details', function ($table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
        });
    }

    public function test_confirming_pending_order_ships_order_and_reduces_product_stock(): void
    {
        $staff = $this->warehouseStaffUser();
        $productId = $this->product(stockQuantity: 10);
        $orderId = $this->order(status: 'Pending');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $response = $this->actingAs($staff)->post('/kho/xuat-kho/' . $orderId);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('Shipping', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(7, (int) Product::find($productId)->stock_quantity);
    }

    public function test_confirming_installation_order_requires_assigned_staff_tech(): void
    {
        $staff = $this->warehouseStaffUser();
        $tech = $this->staffTechUser();
        $productId = $this->product(stockQuantity: 10, requiresInstallation: true);
        $orderId = $this->order(status: 'Pending');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $missingTechResponse = $this->actingAs($staff)->post('/kho/xuat-kho/' . $orderId);

        $missingTechResponse->assertRedirect();
        $missingTechResponse->assertSessionHas('error');
        $this->assertSame('Pending', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(10, (int) Product::find($productId)->stock_quantity);

        $response = $this->actingAs($staff)->post('/kho/xuat-kho/' . $orderId, [
            'assigned_staff_tech_id' => $tech->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('Shipping', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame($tech->id, (int) DB::table('orders')->where('id', $orderId)->value('assigned_staff_tech_id'));
        $this->assertNotNull(DB::table('orders')->where('id', $orderId)->value('installation_assigned_at'));
        $this->assertSame(7, (int) Product::find($productId)->stock_quantity);
    }

    public function test_confirming_pending_order_with_insufficient_stock_keeps_order_and_stock_unchanged(): void
    {
        $staff = $this->warehouseStaffUser();
        $productId = $this->product(stockQuantity: 2);
        $orderId = $this->order(status: 'Pending');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $response = $this->actingAs($staff)->post('/kho/xuat-kho/' . $orderId);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame('Pending', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(2, (int) Product::find($productId)->stock_quantity);
    }

    public function test_confirming_non_pending_order_is_rejected(): void
    {
        $staff = $this->warehouseStaffUser();
        $productId = $this->product(stockQuantity: 10);
        $orderId = $this->order(status: 'Shipping');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $response = $this->actingAs($staff)->post('/kho/xuat-kho/' . $orderId);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame('Shipping', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(10, (int) Product::find($productId)->stock_quantity);
    }

    public function test_pending_order_cannot_be_manually_changed_to_shipping_without_shipment_confirmation(): void
    {
        $admin = $this->adminUser();
        $productId = $this->product(stockQuantity: 10);
        $orderId = $this->order(status: 'Pending');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $response = $this->actingAs($admin)->post('/quantri/don-hang/cap-nhat/' . $orderId, [
            'status' => 'Shipping',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertSame('Pending', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(10, (int) Product::find($productId)->stock_quantity);
    }

    public function test_rejecting_shipment_cancels_pending_order_without_reducing_stock(): void
    {
        $staff = $this->warehouseStaffUser();
        $productId = $this->product(stockQuantity: 10);
        $orderId = $this->order(status: 'Pending');
        $this->orderDetail($orderId, $productId, quantity: 3);

        $response = $this->actingAs($staff)->post('/kho/tu-choi-xuat-kho/' . $orderId, [
            'reason' => 'Hang thuc te bi loi',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertSame('Canceled', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertSame(10, (int) Product::find($productId)->stock_quantity);
    }

    public function test_staff_warehouse_login_redirects_to_dedicated_shipment_page(): void
    {
        User::create([
            'username' => 'warehouse',
            'password' => bcrypt('secret'),
            'full_name' => 'Warehouse Staff',
            'role' => 'StaffWarehouse',
        ]);

        $response = $this->post('/quantri/login', [
            'username' => 'warehouse',
            'password' => 'secret',
        ]);

        $response->assertRedirect('/kho/xuat-kho');
    }

    public function test_only_staff_warehouse_can_access_dedicated_shipment_page(): void
    {
        $warehouseStaff = $this->warehouseStaffUser('warehouse_access');
        $admin = $this->adminUser();

        $this->actingAs($warehouseStaff)
            ->get('/kho/xuat-kho')
            ->assertStatus(200);

        $this->actingAs($admin)
            ->get('/kho/xuat-kho')
            ->assertRedirect('/quantri/login');
    }

    private function adminUser(): User
    {
        return User::create([
            'username' => 'admin',
            'password' => 'secret',
            'full_name' => 'Admin User',
            'role' => 'Admin',
        ]);
    }

    private function warehouseStaffUser(string $username = 'warehouse'): User
    {
        return User::create([
            'username' => $username,
            'password' => 'secret',
            'full_name' => 'Warehouse Staff',
            'role' => 'StaffWarehouse',
        ]);
    }

    private function staffTechUser(string $username = 'tech'): User
    {
        return User::create([
            'username' => $username,
            'password' => 'secret',
            'full_name' => 'Installation Staff',
            'role' => 'StaffTech',
        ]);
    }

    private function product(int $stockQuantity, bool $requiresInstallation = false): int
    {
        return DB::table('products')->insertGetId([
            'name' => 'May loc nuoc',
            'price' => 1000000,
            'stock_quantity' => $stockQuantity,
            'requires_installation' => $requiresInstallation,
        ]);
    }

    private function order(string $status): int
    {
        return DB::table('orders')->insertGetId([
            'order_date' => now(),
            'total_price' => 3000000,
            'status' => $status,
            'payment_method' => 'Tien mat',
        ]);
    }

    private function orderDetail(int $orderId, int $productId, int $quantity): void
    {
        DB::table('order_details')->insert([
            'order_id' => $orderId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'unit_price' => 1000000,
        ]);
    }
}
