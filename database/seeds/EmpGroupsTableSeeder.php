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
              'name' => 'Group1',
            ),
            array(
                'name' => 'Group2',
              ),
              array(
                'name' => 'Group3',
              ),
              array(
                'name' => 'Group4',
              ),
              array(
                'name' => 'Group5',
              ),
            
          ));
    }
}
