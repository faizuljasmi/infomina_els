<?php

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
        return $user->user_type == 'Admin' || $user->id == $leaveApplication->user_id;
    }

    public function approve(User $user,LeaveApplication $leaveApplication)
    {
        return $user->user_type == 'Admin' || $user->user_type == 'Authority';
    }

}
