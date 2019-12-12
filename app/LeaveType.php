<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'name',
    ];

        
    public function entitlements(){
        $this->hasMany(LeaveEntitlement::class);
    }

    public function earnings(){
        $this->hasMany(LeaveEarning::class);
    }
}
