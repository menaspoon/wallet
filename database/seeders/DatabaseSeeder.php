<?php

namespace Database\Seeders;

use App\Models\Clint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Clint::factory()->create([
            'name' => 'Test Client 1',
        ]);

        Clint::factory()->create([
            'name' => 'Test Client 2',
        ]);
    }
}