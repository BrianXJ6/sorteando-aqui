<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Admin master
        Admin::factory()->createOne([
            'name' => 'Brian Barros',
            'email' => 'admin.master@sorteandoaqui.com.br',
            'password' => '1234567890',
        ]);

        // Admins fake
        Admin::factory()->count(4)->create();
    }
}
