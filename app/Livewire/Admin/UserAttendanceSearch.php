<?php

namespace App\Livewire\Admin;

use App\Models\Employee;
use App\Models\PendingAttendance;
use App\Models\User;
use Livewire\Component;

class UserAttendanceSearch extends Component
{

    public $employee_id = '';
    public $user = null;
    public $employee = null;
    public $showResults = false;
    public $totalAttendances = 0;
    public $confirmedAttendances = 0;
    public $pendingAttendances = 0;

    public function searchEmployee()
    {
        $this->validate([
            'employee_id' => 'required|string',
        ], [
            'employee_id.required' => 'Por favor ingresa un número de empleado',
        ]);

        // Buscar usuario registrado
        $this->user = User::where('employee_id', $this->employee_id)->first();

        if ($this->user) {
            // Usuario registrado encontrado
            // Contar asistencias confirmadas
            $this->confirmedAttendances = $this->user->attendances()->count();

            // Contar asistencias pendientes no migradas
            $this->pendingAttendances = PendingAttendance::where('employee_matricula', $this->employee_id)
                ->notMigrated()
                ->count();

            // Total combinado
            $this->totalAttendances = $this->confirmedAttendances + $this->pendingAttendances;

            $this->showResults = true;

            $this->dispatch('notify', [
                'type' => 'success',
                'title' => 'Usuario encontrado',
                'description' => 'Se encontró el usuario: ' . $this->user->name
            ]);
        } else {
            // No hay usuario registrado, buscar en empleados y asistencias pendientes
            $this->employee = Employee::where('matricula', $this->employee_id)->first();

            // Contar solo asistencias pendientes
            $this->confirmedAttendances = 0;
            $this->pendingAttendances = PendingAttendance::where('employee_matricula', $this->employee_id)
                ->notMigrated()
                ->count();
            $this->totalAttendances = $this->pendingAttendances;

            $this->showResults = true;

            if ($this->employee && $this->pendingAttendances > 0) {
                $this->dispatch('notify', [
                    'type' => 'info',
                    'title' => 'Empleado sin registro',
                    'description' => 'Se encontró información de ' . $this->employee->nombre_completo . ' con ' . $this->pendingAttendances . ' asistencia(s) pendiente(s)'
                ]);
            } elseif ($this->employee) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'title' => 'Sin asistencias',
                    'description' => 'El empleado ' . $this->employee->nombre_completo . ' no tiene asistencias registradas'
                ]);
            } else {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'title' => 'No encontrado',
                    'description' => 'No se encontró ningún registro con la matrícula: ' . $this->employee_id
                ]);
            }
        }
    }

    public function clearSearch()
    {
        $this->reset(['employee_id', 'user', 'employee', 'showResults', 'totalAttendances', 'confirmedAttendances', 'pendingAttendances']);
    }

    public function render()
    {
        return view('livewire.admin.user-attendance-search');
    }
}
