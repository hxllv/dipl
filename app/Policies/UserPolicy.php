<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user): Response
    {
        return ($user->role->middlewares->find('users.view')) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function invite(User $user): Response
    {
        return ($user->role->middlewares->find('users.invite')) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function edit(User $user): Response
    {
        return ($user->role->middlewares->find('users.edit')) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }

    public function delete(User $user): Response
    {
        return ($user->role->middlewares->find('users.delete')) ? 
            Response::allow() : 
            Response::denyWithStatus(403);
    }
}
