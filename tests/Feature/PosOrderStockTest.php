<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PosOrderStockTest extends TestCase
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
        });

        Schema::create('order_details', function ($table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 15, 2);
        });
    }

    public function test_creating_pending_pos_order_does_not_reduce_product_stock(): void
    {
        $staff = User::create([
            'username' => 'sales',
            'password' => 'secret',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);

        $productId = DB::table('products')->insertGetId([
            'name' => 'May loc nuoc',
            'price' => 1000000,
            'stock_quantity' => 10,
            'requires_installation' => false,
        ]);

        $response = $this->actingAs($staff)->post('/pos/orders', [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 3,
                ],
            ],
            'status' => 'Pending',
            'delivery_address' => '123 Le Loi, Quan 1',
            'received_amount' => 3000000,
        ]);

        $response->assertRedirect();
        $this->assertSame('Pending', DB::table('orders')->value('status'));
        $this->assertSame(10, (int) Product::find($productId)->stock_quantity);
    }

    public function test_pending_pos_order_requires_delivery_address(): void
    {
        $staff = User::create([
            'username' => 'sales_pending_address',
            'password' => 'secret',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);

        $productId = DB::table('products')->insertGetId([
            'name' => 'Tu lanh',
            'price' => 7000000,
            'stock_quantity' => 2,
            'requires_installation' => false,
        ]);

        $response = $this->actingAs($staff)->post('/pos/orders', [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 1,
                ],
            ],
            'status' => 'Pending',
            'received_amount' => 7000000,
        ]);

        $response->assertSessionHasErrors('delivery_address');
        $this->assertSame(0, DB::table('orders')->count());
    }

    public function test_pos_order_can_be_created_as_completed(): void
    {
        $staff = User::create([
            'username' => 'sales_completed',
            'password' => 'secret',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);

        $productId = DB::table('products')->insertGetId([
            'name' => 'May giat',
            'price' => 5000000,
            'stock_quantity' => 5,
            'requires_installation' => false,
        ]);

        $response = $this->actingAs($staff)->post('/pos/orders', [
            'items' => [
                [
                    'product_id' => $productId,
                    'quantity' => 1,
                ],
            ],
            'status' => 'Completed',
            'received_amount' => 5000000,
        ]);

        $response->assertRedirect();
        $this->assertSame('Completed', DB::table('orders')->value('status'));
        $this->assertSame(4, (int) Product::find($productId)->stock_quantity);
    }
}
