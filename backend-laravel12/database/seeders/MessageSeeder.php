<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'name'    => 'Riki',
                'email'   => 'riki@gmail.com',
                'message' => 'Lorem ipsum dolor sit amet consectetur adipisicing elit. Aliquid quas adipisci fuga eveniet repellendus veritatis et odit ad amet non?',
            ],
        ];

        foreach ($data as $item) {
            \App\Models\Message::updateOrCreate(['name' => $item['name']], $item);
        }
    }
}
