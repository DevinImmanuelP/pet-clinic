<?php

namespace App\Policies;

use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function viewAny(): bool
    {
        return Auth::user()->role->name == 'admin';
    }

    public function update(): bool
    {
        return Auth::user()->role->name == 'admin';
    }

    public function create(): bool
    {
        return Auth::user()->role->name == 'admin';
    }

    public function delete(): bool
    {
        return Auth::user()->role->name == 'admin';
    }
}
