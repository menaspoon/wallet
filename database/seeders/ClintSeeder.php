<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\clint;

class ClintSeeder extends Seeder {

    public function run(): void {
        Clint::factory(2)->create();
    }

}
