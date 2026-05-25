<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class InstallationStatusTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('order_status_histories');
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

        Schema::create('order_details', function ($table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
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
    }

    public function test_staff_tech_sees_only_todays_assigned_shipping_orders(): void
    {
        $tech = $this->user('tech', 'StaffTech');
        $otherTech = $this->user('othertech', 'StaffTech');
        $customer = $this->user('customer', 'Customer');

        $visibleOrderId = $this->order($customer->id, 'Shipping', $tech->id, now());
        $this->order($customer->id, 'Shipping', $otherTech->id, now());
        $this->order($customer->id, 'Shipping', $tech->id, now()->subDay());
        $this->order($customer->id, 'Completed', $tech->id, now());

        $response = $this->actingAs($tech)->get('/lap-dat');

        $response->assertStatus(200);
        $response->assertSee('DH-' . $visibleOrderId);
        $response->assertDontSee('DH-' . ($visibleOrderId + 1));
        $response->assertDontSee('DH-' . ($visibleOrderId + 2));
        $response->assertDontSee('DH-' . ($visibleOrderId + 3));
    }

    public function test_staff_tech_completes_assigned_shipping_order_and_history_is_recorded(): void
    {
        $tech = $this->user('tech', 'StaffTech');
        $customer = $this->user('customer', 'Customer');
        $orderId = $this->order($customer->id, 'Shipping', $tech->id, now());

        $response = $this->actingAs($tech)->post('/lap-dat/' . $orderId . '/trang-thai', [
            'status' => 'Completed',
        ]);

        $response->assertRedirect('/lap-dat');
        $response->assertSessionHas('success');
        $this->assertSame('Completed', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertNotNull(DB::table('orders')->where('id', $orderId)->value('installation_completed_at'));
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $orderId,
            'changed_by' => $tech->id,
            'from_status' => 'Shipping',
            'to_status' => 'Completed',
        ]);
    }

    public function test_staff_tech_marks_installation_failed_with_required_reason(): void
    {
        $tech = $this->user('tech', 'StaffTech');
        $customer = $this->user('customer', 'Customer');
        $orderId = $this->order($customer->id, 'Shipping', $tech->id, now());

        $missingReasonResponse = $this->actingAs($tech)->post('/lap-dat/' . $orderId . '/trang-thai', [
            'status' => 'InstallationFailed',
        ]);

        $missingReasonResponse->assertSessionHasErrors('reason');
        $this->assertSame('Shipping', DB::table('orders')->where('id', $orderId)->value('status'));

        $response = $this->actingAs($tech)->post('/lap-dat/' . $orderId . '/trang-thai', [
            'status' => 'InstallationFailed',
            'reason' => 'Khach vang nha',
        ]);

        $response->assertRedirect('/lap-dat');
        $response->assertSessionHas('success');
        $this->assertSame('InstallationFailed', DB::table('orders')->where('id', $orderId)->value('status'));
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $orderId,
            'changed_by' => $tech->id,
            'from_status' => 'Shipping',
            'to_status' => 'InstallationFailed',
            'reason' => 'Khach vang nha',
        ]);
    }

    public function test_staff_tech_cannot_update_unassigned_or_non_shipping_orders(): void
    {
        $tech = $this->user('tech', 'StaffTech');
        $otherTech = $this->user('othertech', 'StaffTech');
        $customer = $this->user('customer', 'Customer');
        $unassignedOrderId = $this->order($customer->id, 'Shipping', $otherTech->id, now());
        $completedOrderId = $this->order($customer->id, 'Completed', $tech->id, now());

        $this->actingAs($tech)
            ->post('/lap-dat/' . $unassignedOrderId . '/trang-thai', ['status' => 'Completed'])
            ->assertRedirect('/lap-dat')
            ->assertSessionHas('error');

        $this->actingAs($tech)
            ->post('/lap-dat/' . $completedOrderId . '/trang-thai', ['status' => 'Completed'])
            ->assertRedirect('/lap-dat')
            ->assertSessionHas('error');

        $this->assertSame('Shipping', DB::table('orders')->where('id', $unassignedOrderId)->value('status'));
        $this->assertSame('Completed', DB::table('orders')->where('id', $completedOrderId)->value('status'));
    }

    public function test_staff_tech_login_redirects_to_installation_page(): void
    {
        $tech = User::create([
            'username' => 'tech',
            'password' => bcrypt('secret'),
            'full_name' => 'Installation Staff',
            'role' => 'StaffTech',
        ]);

        $response = $this->post('/quantri/login', [
            'username' => $tech->username,
            'password' => 'secret',
        ]);

        $response->assertRedirect('/lap-dat');
    }

    private function user(string $username, string $role): User
    {
        return User::create([
            'username' => $username,
            'password' => bcrypt('secret'),
            'full_name' => ucfirst($username),
            'role' => $role,
            'phone' => '0900000000',
            'address' => 'Dia chi',
        ]);
    }

    private function order(int $customerId, string $status, int $techId, $assignedAt): int
    {
        return DB::table('orders')->insertGetId([
            'user_id' => $customerId,
            'order_date' => now(),
            'total_price' => 3000000,
            'status' => $status,
            'payment_method' => 'Tien mat',
            'delivery_address' => '123 Nguyen Trai',
            'assigned_staff_tech_id' => $techId,
            'installation_assigned_at' => $assignedAt,
        ]);
    }
}
