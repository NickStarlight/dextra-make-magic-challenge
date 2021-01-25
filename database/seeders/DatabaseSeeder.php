<?php

namespace Database\Seeders;

use App\Models\Character;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Character::factory(20)->create();
    }
}
