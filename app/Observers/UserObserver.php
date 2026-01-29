<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    


    public function created(User $user): void
    {
        $user->email_verified_at = $user->created_at;
        $user->save();
    }
}
