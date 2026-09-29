<?php

namespace App\Livewire\Admin;

use App\Models\MothersDayRegistration;
use Livewire\Component;
use Livewire\Attributes\Rule;

class MothersDayQuickAdd extends Component
{
    #[Rule('required|unique:mothers_day_registrations,matricula', message: [
        'required' => 'La matrícula es obligatoria.',
        'unique' => 'Esta matrícula ya ha sido registrada.',
    ])]
    public $matricula = '';

    #[Rule('required|min:3', message: [
        'required' => 'El nombre es obligatorio.',
        'min' => 'El nombre debe tener al menos 3 caracteres.',
    ])]
    public $name = '';

    public function save()
    {
        $this->validate();

        MothersDayRegistration::create([
            'matricula' => $this->matricula,
            'name' => $this->name,
            'zone' => 'chihuahua',
            'email' => 'manual@example.com',
            'phone' => '0000000000',
        ]);

        $this->reset(['matricula', 'name']);

        $this->dispatch('registration-added');
        session()->flash('success', 'Registro agregado correctamente.');
    }

    public function render()
    {
        return view('livewire.admin.mothers-day-quick-add');
    }
}
