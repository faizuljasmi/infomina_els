<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'user_type', 'emp_type_id', 'emp_group_id', 'join_date','gender',
        'job_title','emergency_contact_name','emergency_contact_no'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Add a mutator to ensure hashed passwords
     */
    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    //One User has one employee type
    public function emp_types(){
        return $this->belongsTo(EmpType::class, 'emp_type_id');
    }

    //One User has one employee group
    public function emp_group(){
        return $this->belongsTo(EmpGroup::class, 'emp_group_id');
    }

    //One User has many leave applications
    public function leave_applications(){
        return $this->hasMany(LeaveApplication::class);
    }

    //One user has one set of approval authority
    public function approval_authority(){
        return $this->hasOne(ApprovalAuthority::class);
    }

}
