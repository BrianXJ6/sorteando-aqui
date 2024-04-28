<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Admin master user
        User::factory()->create([
            'name' => 'Brian Ferreira',
            'email' => 'brianferreira15@hotmail.com',
            'password' => '123456',
        ]);

        // Creating user sequences by toggling values for `email_verified_at`
        User::factory()->count(10)->sequence(
            ['email_verified_at' => now()],
            ['email_verified_at' => null],
        )->create();
    }
}
