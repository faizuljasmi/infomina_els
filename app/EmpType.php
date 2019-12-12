<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\LeaveEntitlement;

class EmpType extends Model
{
    //
    protected $fillable = [
        'name',
        // 'ent_annual', 
        //  'ent_calamity' ,
        //  'ent_carryfwd', 
        //  'ent_compassionate' ,
        //  'ent_emergency', 
        //  'ent_hospitalization' ,
        //  'ent_marriage' ,
        //  'ent_maternity', 
        //  'ent_paternity', 
        //  'ent_sick' ,
        //  'ent_training', 
        //  'ent_unpaid' ,
    ];

        
    public function user(){
        $this->hasMany(User::class);
    }

    public function entitlements(){
        $this->hasMany(LeaveEntitlement::class);
    }
}
