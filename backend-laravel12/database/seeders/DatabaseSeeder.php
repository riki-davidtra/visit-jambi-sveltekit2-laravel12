<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@gmail.com',
            'password' => 'password',
        ]);

        $this->call([
            CategorySeeder::class,
            DestinationSeeder::class,
            MessageSeeder::class,
        ]);
    }
}
