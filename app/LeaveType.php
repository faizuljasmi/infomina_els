<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
    ];

        
    public function entitlements(){
        return $this->hasMany(LeaveEntitlement::class);
    }

    public function earnings(){
       return $this->hasMany(LeaveEarning::class);
    }
}
