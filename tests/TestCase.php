<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! Schema::hasTable('users')) {
            return;
        }

        $user = User::factory()->create([
            'user_type' => 'super_admin',
            'is_active' => true,
        ]);

        Sanctum::actingAs($user, ['*']);
    }
}
