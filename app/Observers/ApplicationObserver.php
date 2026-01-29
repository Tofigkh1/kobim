<?php

namespace App\Observers;

use App\Models\Application;
use Carbon\Carbon;

class ApplicationObserver
{
    /**
     * Handle the Application "created" event.
     */
    public function created(Application $application): void
    {
        $year = Carbon::parse($application->created_at)->year;
        $application->applicationNumber = $application->id . '-' . $application->applicationType . '/' . $year;
        $application->save();
    }

    public function creating(Application $application)
    {
        if (auth()->check()) {
            $application->applicationType = 'F';
        } else {
            $application->applicationType = 'O';
        }
    }

    /**
     * Handle the Application "updated" event.
     */
    public function updated(Application $application): void
    {
        //
    }

    /**
     * Handle the Application "deleted" event.
     */
    public function deleted(Application $application): void
    {
        //
    }

    /**
     * Handle the Application "restored" event.
     */
    public function restored(Application $application): void
    {
        //
    }

    /**
     * Handle the Application "force deleted" event.
     */
    public function forceDeleted(Application $application): void
    {
        //
    }
}
