<?php

namespace App\Policies;

use App\Models\User;
use App\Models\EmployeeTask;
use Illuminate\Auth\Access\HandlesAuthorization;
use Kenepa\Banner\Models\Banner;

class BannerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee::task');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Banner $employeeTask): bool
    {
        return $user->can('view_employee::task');
    }

   

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_employee::task');
    }
}
