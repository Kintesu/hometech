<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CustomerLogoutTest extends TestCase
{
    public function test_customer_can_logout_from_homepage_header(): void
    {
        $user = new User([
            'username' => 'customer',
            'full_name' => 'Customer User',
            'role' => 'Customer',
        ]);
        $user->id = 25;

        $response = $this->actingAs($user)->post('/dang-xuat');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
