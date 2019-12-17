<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpGroup extends Model
{
    protected $fillable = [
        'name',

    ];

    //One employee group has many users
    public function users(){
       return $this->hasMany(User::class);
    }
}
