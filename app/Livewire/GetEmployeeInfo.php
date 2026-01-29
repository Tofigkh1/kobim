<?php

namespace App\Livewire;

use Livewire\Component;

class GetEmployeeInfo extends Component
{

    public $employee_type;
    public $voen;
    public $fin;
    public $name_surname;
    public $education;

    public function updatedVoen($value)
    {
        if ($this->employee_type == 1 && !empty($value)) {
            $this->fetchEmployeeInfo($value);
        }
    }

    public function updatedFin($value)
    {
        if ($this->employee_type == 0 && !empty($value)) {
            $this->fetchEmployeeInfo($value);
        }
    }

    protected function fetchEmployeeInfo($identifier)
    {
        // Simulate static data for name_surname and education
        // Replace with actual API call when available
        $this->name_surname = 'John Doe'; // Static value for example
        $this->education = 'Bachelor';    // Static value for example
    }


    public function render()
    {
        return view('livewire.get-employee-info');
    }

    
}
