<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    //Declare Fillable

    //Declare Relation with other models
    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function leaveType(){
        return $this->hasOne(LeaveType::class,'leave_type_id');
    }

    //Declare Relation with other models
    public function approver_one(){
        return $this->hasOne(User::class,'id','approver_id_1');
    }

    public function approver_two(){
        return $this->hasOne(User::class, 'id','approver_id_2');
    }

    public function approver_three(){
        return $this->hasOne(User::class,'id','approver_id_3');
    }

    public function relief_personnel(){
        return $this->hasOne(User::class,'id','relief_personnel_id');
    }

}
