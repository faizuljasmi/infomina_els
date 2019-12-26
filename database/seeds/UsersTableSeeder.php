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
          'password' => 'password',
          'user_type' => 'admin'
        ]);
        factory('App\User')->create([
          'email'=> 'employee@gmail.com',
          'name' => 'Test Employee',
          'password' => 'password',
          'user_type' => 'employee'
        ]);
        factory('App\User')->create([
          'email'=> 'admin@gmail.com',
          'name' => 'Test Admin',
          'password' => 'password',
          'user_type' => 'admin'
        ]);
        factory('App\User')->create([
          'email'=> 'vanimalar@infomina.com.my',
          'name' => 'Vani',
          'password' => 'password',
          'user_type' => 'Admin',
          'staff_id' => 'IF072'
        ]);
        factory('App\User')->times(10)->create();
        Schema::enableForeignKeyConstraints();
    }
}
