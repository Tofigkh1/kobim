<?php

namespace App\Livewire;

use Livewire\Component;

class GetClientData extends Component
{

    public $voenError;

    protected $listeners = ['updateVoenError' => '$refresh']; 

    public function mount()
    {
        $this->voenError = session()->get('voenError');
    }

    public function checkSession()
    {
        $this->voenError = session()->get('voenError'); 
    }

    public function render()
    {
        return view('livewire.get-client-data', [
            'voenError' => $this->voenError,
        ]);
    }

    public function updated()
    {
        $this->checkSession(); 
    }

    public function hydrate()
    {
        $this->checkSession(); 
    }
}
