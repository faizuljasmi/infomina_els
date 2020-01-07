<?php

/**
 * @author Faizul Jasmi
 * @email faizul.jasmi@infomina.com.my
 * @create date 2020-01-07 09:03:50
 * @modify date 2020-01-07 09:03:50
 * @desc [description]
 */

namespace App\Policies;

use App\User;
use App\LeaveApplication;
use Illuminate\Auth\Access\HandlesAuthorization;

class LeaveApplicationPolicy
{
    use HandlesAuthorization;

     /**
     * Determine whether the user can view the post.
     *
     * @param  \App\User  $user
     * @param  \App\LeaveApplication  $leaveApplication
     * @return mixed
     */
    public function view(User $user, LeaveApplication $leaveApplication)
    {
        return $user->user_type == 'Admin' || $user->id == $leaveApplication->user_id || $user->user_type == 'Authority';
    }

    public function approve(User $user,LeaveApplication $leaveApplication)
    {
        return $user->user_type == 'Admin' || $user->user_type == 'Authority';
    }

}
