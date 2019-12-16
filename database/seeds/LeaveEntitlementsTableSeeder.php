<?php

use Illuminate\Database\Seeder;

class LeaveEntitlementsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
        DB::table('leave_entitlements')->insert(array(
            //Annual
            array(
              'leave_type_id' => '1',
              'emp_type_id' => '1',
              'no_of_days' => $faker->numberBetween(1,20),
            ),
            array(
                'leave_type_id' => '1',
                'emp_type_id' => '2',
                'no_of_days' => $faker->numberBetween(1,20),
              ),
              array(
                'leave_type_id' => '1',
                'emp_type_id' => '3',
                'no_of_days' => $faker->numberBetween(1,20),
              ),
              array(
                'leave_type_id' => '1',
                'emp_type_id' => '4',
                'no_of_days' => $faker->numberBetween(1,20),
              ),
              array(
                'leave_type_id' => '1',
                'emp_type_id' => '5',
                'no_of_days' => $faker->numberBetween(1,20),
              ),

              //Calamity
              array(
                'leave_type_id' => '2',
                'emp_type_id' => '1',
                'no_of_days' => $faker->numberBetween(1,20),
              ),
              array(
                  'leave_type_id' => '2',
                  'emp_type_id' => '2',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '2',
                  'emp_type_id' => '3',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '2',
                  'emp_type_id' => '4',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '2',
                  'emp_type_id' => '5',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),

                //Medical
                array(
                'leave_type_id' => '3',
                'emp_type_id' => '1',
                'no_of_days' => $faker->numberBetween(1,20),
              ),
              array(
                  'leave_type_id' => '3',
                  'emp_type_id' => '2',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '3',
                  'emp_type_id' => '3',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '3',
                  'emp_type_id' => '4',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                array(
                  'leave_type_id' => '3',
                  'emp_type_id' => '5',
                  'no_of_days' => $faker->numberBetween(1,20),
                ),
                
                     //Hospital
                     array(
                        'leave_type_id' => '4',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '4',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '4',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '4',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '4',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                    //Compassionate
                     array(
                        'leave_type_id' => '5',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '5',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '5',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '5',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '5',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                        //Emergency
                     array(
                        'leave_type_id' => '6',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '6',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '6',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '6',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '6',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                              //Marriage
                     array(
                        'leave_type_id' => '7',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '7',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '7',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '7',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '7',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                         //Maternity
                     array(
                        'leave_type_id' => '8',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '8',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '8',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '8',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '8',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                        //Paternity
                     array(
                        'leave_type_id' => '9',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '9',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '9',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '9',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '9',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),


                         //Training
                     array(
                        'leave_type_id' => '10',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '10',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '10',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '10',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '10',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),

                        //Unpaid
                     array(
                        'leave_type_id' => '11',
                        'emp_type_id' => '1',
                        'no_of_days' => $faker->numberBetween(1,20),
                      ),
                      array(
                          'leave_type_id' => '11',
                          'emp_type_id' => '2',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '11',
                          'emp_type_id' => '3',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '11',
                          'emp_type_id' => '4',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),
                        array(
                          'leave_type_id' => '11',
                          'emp_type_id' => '5',
                          'no_of_days' => $faker->numberBetween(1,20),
                        ),





              
            
            
          ));
    }
}
