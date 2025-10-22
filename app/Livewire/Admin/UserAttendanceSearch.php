<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class UserAttendanceSearch extends Component
{

    public $employee_id = '';
    public $user = null;
    public $showResults = false;

    public function searchEmployee()
    {
        $this->validate([
            'employee_id' => 'required|string',
        ], [
            'employee_id.required' => 'Por favor ingresa un número de empleado',
        ]);

        $this->user = User::where('employee_id', $this->employee_id)->first();
        $this->showResults = true;

        if ($this->user) {
            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Empleado encontrado',
                'description' => 'Se encontró el empleado: ' . $this->user->name
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'title' => 'No encontrado',
                'description' => 'No se encontró ningún empleado con la matrícula: ' . $this->employee_id
            ]);
        }
    }

    public function clearSearch()
    {
        $this->reset(['employee_id', 'user', 'showResults']);
    }

    public function render()
    {
        return view('livewire.admin.user-attendance-search');
    }
}
