<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        DB::table('users')->truncate();
        factory('App\User')->create([
          'email'=> 'faizul.jasmi95@gmail.com',
          'name' => 'Faizul Jasmi',
          'password' => bcrypt('secret'),
          'user_type' => 'admin'
        ]);
        factory('App\User')->times(30)->create();
        Schema::enableForeignKeyConstraints();
    }
}
