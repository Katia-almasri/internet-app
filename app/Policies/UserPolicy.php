<?php

namespace App\Policies;

use App\User;
use Auth;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

   
    public function viewAny(User $user)
    {
        //
    }

   
    public function view(User $user, Group $group)
    {
        // $user = Auth::guard('user-api')->user();

        // if ($user->hasRole('user')) {
        //     if($group->owner_id == $user->id)
        //         return true;
        // }
        // return false;
    }

    
    public function viewFilesBelongToGroup(User $user, Group $group)
    {
        return true;
    }

    public function viewGroups(User $user)
    {
        return true;
    }

    public function addFile(User $user, Group $group){
         if($user->groups()->first()->pivot->user_id==$user->id)
            return true;
    }

    
    public function update(User $user, User $model)
    {
        //
    }

    
    public function delete(User $user, User $model)
    {
        //
    }

   
    public function restore(User $user, User $model)
    {
        //
    }

    
    public function forceDelete(User $user, User $model)
    {
        //
    }
}
