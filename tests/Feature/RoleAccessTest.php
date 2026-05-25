<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
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
}
