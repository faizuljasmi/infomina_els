<?php

namespace App\Imports;

use App\User;
use App\LeaveEarning;
use App\TakenLeave;
use App\LeaveBalance;
use App\BroughtForwardLeave;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class ImportLeaveEarn implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function headingRow(): int
    {
        return 2;
    }
    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            $user = User::orderBy('id','ASC')->where('name', @$row['name'])->first();
            if($user == null){
                return null;
            }
            $user_id = $user->id;

            //Annual
            $le1 = new LeaveEarning;
            $le1->leave_type_id = '1';
            $le1->user_id = $user_id;
            $le1->no_of_days = @$row['e1'];
            $le1->save();
            $bf = new BroughtForwardLeave;
            $bf->leave_type_id = '1';
            $bf->user_id = $user_id;
            $bf->no_of_days = @$row['c1'];
            $bf->save();
            $tl1 = new TakenLeave;
            $tl1->leave_type_id = '1';
            $tl1->user_id = $user_id;
            $tl1->no_of_days = @$row['t1'];
            $tl1->save();
            $lb1 = new LeaveBalance;
            $lb1->leave_type_id = '1';
            $lb1->user_id = $user_id;
            $lb1->no_of_days = @$row['b1'];
            $lb1->save();

            //Calamity
            $le2 = new LeaveEarning;
            $le2->leave_type_id = '2';
            $le2->user_id = $user_id;
            $le2->no_of_days = @$row['e2'];
            $le2->save();
            $tl2 = new TakenLeave;
            $tl2->leave_type_id = '2';
            $tl2->user_id = $user_id;
            $tl2->no_of_days = @$row['t2'];
            $tl2->save();
            $lb2 = new LeaveBalance;
            $lb2->leave_type_id = '2';
            $lb2->user_id = $user_id;
            $lb2->no_of_days = @$row['b2'];
            $lb2->save();

            //Sick
            $le3 = new LeaveEarning;
            $le3->leave_type_id = '3';
            $le3->user_id = $user_id;
            $le3->no_of_days = @$row['e3'];
            $le3->save();
            $tl3 = new TakenLeave;
            $tl3->leave_type_id = '3';
            $tl3->user_id = $user_id;
            $tl3->no_of_days = @$row['t3'];
            $tl3->save();
            $lb3 = new LeaveBalance;
            $lb3->leave_type_id = '3';
            $lb3->user_id = $user_id;
            $lb3->no_of_days = @$row['b3'];
            $lb3->save();
       
            //Hospitalization
            $le4 = new LeaveEarning;
            $le4->leave_type_id = '4';
            $le4->user_id = $user_id;
            $le4->no_of_days = @$row['e4'];
            $le4->save();
            $tl4 = new TakenLeave;
            $tl4->leave_type_id = '4';
            $tl4->user_id = $user_id;
            $tl4->no_of_days = @$row['t4'];
            $tl4->save();
            $lb4 = new LeaveBalance;
            $lb4->leave_type_id = '4';
            $lb4->user_id = $user_id;
            $lb4->no_of_days = @$row['b4'];
            $lb4->save();

            //Compassionate
            $le5 = new LeaveEarning;
            $le5->leave_type_id = '5';
            $le5->user_id = $user_id;
            $le5->no_of_days = @$row['e5'];
            $le5->save();
            $tl5 = new TakenLeave;
            $tl5->leave_type_id = '5';
            $tl5->user_id = $user_id;
            $tl5->no_of_days = @$row['t5'];
            $tl5->save();
            $lb5 = new LeaveBalance;
            $lb5->leave_type_id = '5';
            $lb5->user_id = $user_id;
            $lb5->no_of_days = @$row['b5'];
            $lb5->save();

            //Emergency
            $le6 = new LeaveEarning;
            $le6->leave_type_id = '6';
            $le6->user_id = $user_id;
            $le6->no_of_days = @$row['e6'];
            $le6->save();
            $tl6 = new TakenLeave;
            $tl6->leave_type_id = '6';
            $tl6->user_id = $user_id;
            $tl6->no_of_days = @$row['t6'];
            $tl6->save();
            $lb6 = new LeaveBalance;
            $lb6->leave_type_id = '6';
            $lb6->user_id = $user_id;
            $lb6->no_of_days = @$row['b6'];
            $lb6->save();

            //Maternity
            $le7 = new LeaveEarning;
            $le7->leave_type_id = '8';
            $le7->user_id = $user_id;
            $le7->no_of_days = @$row['e7'];
            $le7->save();
            $tl7 = new TakenLeave;
            $tl7->leave_type_id = '8';
            $tl7->user_id = $user_id;
            $tl7->no_of_days = @$row['t7'];
            $tl7->save();
            $lb7 = new LeaveBalance;
            $lb7->leave_type_id = '8';
            $lb7->user_id = $user_id;
            $lb7->no_of_days = @$row['e7'];
            $lb7->save();

            //Paternity
            $le8 = new LeaveEarning;
            $le8->leave_type_id = '9';
            $le8->user_id = $user_id;
            $le8->no_of_days = @$row['e8'];
            $le8->save();
            $tl8 = new TakenLeave;
            $tl8->leave_type_id = '9';
            $tl8->user_id = $user_id;
            $tl8->no_of_days = @$row['t8'];
            $tl8->save();
            $lb8 = new LeaveBalance;
            $lb8->leave_type_id = '9';
            $lb8->user_id = $user_id;
            $lb8->no_of_days = @$row['b8'];
            $lb8->save();
        }
    }
}