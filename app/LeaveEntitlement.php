<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\EmpType;
use App\User;

class LeaveEntitlement extends Model
{
    protected $fillable = [
        'no_of_days', 
    ];

    public function leave_type(){
        $this->belongsTo(LeaveType::class, 'leave_type_id');
    }

    public function emp_type(){
        $this->belongsTo(EmpType::class, 'emp_type_id');
    }

    // public function getEmpTypeAttribute(){
    //     return isset($this->emp_type) ? $this->emp_type->name : '';
    // }
}
