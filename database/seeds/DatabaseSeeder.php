<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
include 'categoriesTableSeeder.php';
include 'usersTableSeeder.php';
include 'postsTableSeeder.php';

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->truncateTables([
            'users',
            'categories',
            'posts'
        ]);
        $this->call(usersTableSeeder::class);
        $this->call(categoriesTableSeeder::class);
        $this->call(postsTableSeeder::class);

    }

    protected function truncateTables(array $tables){
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach($tables as $table){
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
