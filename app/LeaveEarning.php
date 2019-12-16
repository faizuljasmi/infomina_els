<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveEarning extends Model
{
    protected $fillable = [
        'no_of-days',
    ];

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function leave_type(){
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
     }
}
