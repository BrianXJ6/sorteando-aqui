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
            'password' => '123456',
        ]);

        // Admins fake
        Admin::factory()->count(3)->create();
    }
}
