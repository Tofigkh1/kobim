<?php

namespace App\Observers;

use App\Models\Application;
use App\Models\EmployeeTask;

class EmployeeTaskObserver
{
    /**
     * Handle the EmployeeTask "created" event.
     *
     * @param  \App\Models\EmployeeTask  $employeeTask
     * @return void
     */
    public function created(EmployeeTask $employeeTask)
    {
        $this->updateApplication($employeeTask);

    }

    /**
     * Handle the EmployeeTask "updated" event.
     *
     * @param  \App\Models\EmployeeTask  $employeeTask
     * @return void
     */
    public function updated(EmployeeTask $employeeTask)
    {
        $this->updateApplication($employeeTask);
    }

    /**
     * Handle the EmployeeTask "deleted" event.
     *
     * @param  \App\Models\EmployeeTask  $employeeTask
     * @return void
     */
    public function deleted(EmployeeTask $employeeTask)
    {
        $this->nullifyApplication($employeeTask);
    }

    protected function updateApplication(EmployeeTask $employeeTask)
    {
        $application = Application::find($employeeTask->application_id);

        if ($application) {
            $application->accepted_user_id = $employeeTask->user_id;
            $application->status = 1;
            $application->save();
        }
    }

    protected function nullifyApplication(EmployeeTask $employeeTask)
    {
        $application = Application::find($employeeTask->application_id);

        if ($application) {
            $application->accepted_user_id = null;
            $application->save();
        }
    }
}
