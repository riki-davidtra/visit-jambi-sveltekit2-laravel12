<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'user_id'     => 1,
                'category_id' => 1,
                'name'        => 'Destination Name',
                'location'    => 'Location',
                'image'       => null,
                'description' => 'Description',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Destination::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
