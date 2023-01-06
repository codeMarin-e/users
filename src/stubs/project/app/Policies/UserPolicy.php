<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user, $ability) {
        // @HOOK_USER_POLICY_BEFORE
        if($user->hasRole('Super Admin', 'admin') )
            return true;
    }

    public function view(User $user) {
        // @HOOK_USER_POLICY_VIEW
        return $user->hasPermissionTo('users.view', request()->whereIam());
    }

    public function create(User $user) {
        // @HOOK_USER_POLICY_CREATE
        return $user->hasPermissionTo('users.create', request()->whereIam());
    }

    public function update(User $user, User $chUser) {
        // @HOOK_USER_POLICY_UPDATE
        if( !$user->hasPermissionTo('users.update', request()->whereIam()) )
            return false;
        if( $chUser->hasRole('Super Admin', 'admin'))
            return false;
        return true;
    }

    public function delete(User $user, User $chUser) {
        // @HOOK_USER_POLICY_DELETE
        if( !$user->hasPermissionTo('users.delete', request()->whereIam()) )
            return false;
        if( $chUser->hasRole('Super Admin', 'admin'))
            return false;
        return true;
    }

    // @HOOK_USER_POLICY_END


}
