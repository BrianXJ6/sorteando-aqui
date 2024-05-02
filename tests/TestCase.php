<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * Create a simple user for testing
     *
     * @return User
     */
    public function user(): User
    {
        return User::factory()->create();
    }
}
