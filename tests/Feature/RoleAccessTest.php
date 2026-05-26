<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('username')->unique();
                $table->string('password');
                $table->string('full_name')->nullable();
                $table->string('role')->nullable()->default('Customer');
                $table->timestamps();
            });
        }
    }

    public function test_staff_sales_cannot_access_admin_area(): void
    {
        $user = new User([
            'username' => 'sales',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);
        $user->id = 10;

        $response = $this->actingAs($user)->get('/quantri');

        $response->assertRedirect('/quantri/login');
    }

    public function test_staff_sales_can_access_standalone_pos_page(): void
    {
        $user = new User([
            'username' => 'sales',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);
        $user->id = 10;

        $response = $this->actingAs($user)->get('/pos');

        $response->assertStatus(200);
    }

    public function test_staff_sales_can_login_from_admin_login_page(): void
    {
        User::create([
            'username' => 'sales',
            'password' => bcrypt('secret'),
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);

        $response = $this->post('/quantri/login', [
            'username' => 'sales',
            'password' => 'secret',
        ]);

        $response->assertRedirect('/pos');
    }

    public function test_authenticated_staff_sales_opening_admin_login_redirects_to_pos(): void
    {
        $user = new User([
            'username' => 'sales',
            'full_name' => 'Sales Staff',
            'role' => 'StaffSales',
        ]);
        $user->id = 10;

        $response = $this->actingAs($user)->get('/quantri/login');

        $response->assertRedirect('/pos');
    }

    public function test_pos_access_denial_uses_shared_staff_login_page(): void
    {
        $user = new User([
            'username' => 'warehouse',
            'full_name' => 'Warehouse Staff',
            'role' => 'StaffWarehouse',
        ]);
        $user->id = 11;

        $response = $this->actingAs($user)->get('/pos');

        $response->assertRedirect('/quantri/login');
    }
}
