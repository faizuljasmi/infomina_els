<?php

use Illuminate\Database\Seeder;

class EmpGroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('emp_groups')->insert(array(
            array(
              'name' => 'IT1',
            ),
            array(
                'name' => 'IT2',
              ),
              array(
                'name' => 'SE1',
              ),
              array(
                'name' => 'SE2',
              ),
              array(
                'name' => 'HR',
              ),
            
          ));
    }
}
