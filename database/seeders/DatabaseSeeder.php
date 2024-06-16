<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{

    public function run()
    {
        DB::statement("SET foreign_key_checks=0");

        $this->call(CursosSeeder::class);
        $this->call(DisciplinasSeeder::class);
        $this->call(DepartamentosSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(GradesSeeder::class);

        DB::statement("SET foreign_key_checks=1");
    }
}
