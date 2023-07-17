<?php

use Illuminate\Database\Seeder;

class WorkingHoursTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('working_hours')->insert(array(
            array(
              'label' => '7.30am - 4.30pm',
            ),
            array(
              'label' => '8.00am - 5.00pm',
            ),
            array(
              'label' => '8.30am - 5.30pm',
            ),
            array(
              'label' => '9.00am - 6.00pm',
            ),
            array(
              'label' => 'Open/Shift',
            ),
        ));

        // php artisan db:seed --class=WorkingHoursTableSeeder
    }
}
