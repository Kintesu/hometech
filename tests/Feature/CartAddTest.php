<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CartAddTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('products');

        Schema::create('products', function ($table) {
            $table->id();
            $table->string('name');
            $table->decimal('price', 15, 2);
            $table->integer('stock_quantity')->default(0);
            $table->string('image')->nullable();
        });
    }

    public function test_adding_product_to_cart_redirects_back_to_previous_page(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'May ep cham Philips',
            'price' => 3900000,
            'stock_quantity' => 10,
        ]);

        $response = $this
            ->from('/san-pham/' . $productId)
            ->post('/gio-hang/them/' . $productId, [
                'quantity' => 2,
            ]);

        $response->assertRedirect('/san-pham/' . $productId);
        $response->assertSessionHas('cart.' . $productId);
        $this->assertSame(2, session('cart')[$productId]['quantity']);
    }

    public function test_customer_can_increase_and_decrease_cart_quantity(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'Noi chien khong dau',
            'price' => 2200000,
            'stock_quantity' => 10,
        ]);

        $this->withSession([
            'cart' => [
                $productId => [
                    'name' => 'Noi chien khong dau',
                    'quantity' => 2,
                    'price' => 2200000,
                    'image' => null,
                ],
            ],
        ])->from('/gio-hang')->post('/gio-hang/cap-nhat/' . $productId, [
            'action' => 'increase',
        ])->assertRedirect('/gio-hang');

        $this->assertSame(3, session('cart')[$productId]['quantity']);

        $this->from('/gio-hang')->post('/gio-hang/cap-nhat/' . $productId, [
            'action' => 'decrease',
        ])->assertRedirect('/gio-hang');

        $this->assertSame(2, session('cart')[$productId]['quantity']);
    }

    public function test_customer_can_update_cart_quantity_by_input(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'May loc nuoc',
            'price' => 7200000,
            'stock_quantity' => 10,
        ]);

        $this->withSession([
            'cart' => [
                $productId => [
                    'name' => 'May loc nuoc',
                    'quantity' => 2,
                    'price' => 7200000,
                    'image' => null,
                ],
            ],
        ])->from('/gio-hang')->post('/gio-hang/cap-nhat/' . $productId, [
            'quantity' => 5,
        ])->assertRedirect('/gio-hang');

        $this->assertSame(5, session('cart')[$productId]['quantity']);
    }

    public function test_decreasing_cart_quantity_below_one_removes_item(): void
    {
        $productId = DB::table('products')->insertGetId([
            'name' => 'May ep cham',
            'price' => 3900000,
            'stock_quantity' => 10,
        ]);

        $this->withSession([
            'cart' => [
                $productId => [
                    'name' => 'May ep cham',
                    'quantity' => 1,
                    'price' => 3900000,
                    'image' => null,
                ],
            ],
        ])->from('/gio-hang')->post('/gio-hang/cap-nhat/' . $productId, [
            'action' => 'decrease',
        ])->assertRedirect('/gio-hang');

        $this->assertArrayNotHasKey($productId, session('cart'));
    }
}
