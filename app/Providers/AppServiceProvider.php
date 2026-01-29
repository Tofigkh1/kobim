<?php

namespace App\Providers;

use App\Http\Livewire\FetchEmployeeInfo;
use App\Models\EmployeeTask;
use App\Models\User;
use App\Observers\EmployeeTaskObserver;
use App\Observers\UserObserver;
use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use App\Services\ApiClient;
use App\Interfaces\UserDetailsServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserDetailsServiceInterface::class, ApiClient::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        EmployeeTask::observe(EmployeeTaskObserver::class);
        User::observe(UserObserver::class);
    }


  
}
