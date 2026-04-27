<?php

namespace App\Livewire\Public;

use App\Models\MothersDayRegistration as RegistrationModel;
use Livewire\Component;
use Livewire\Attributes\Rule;

class MothersDayRegistration extends Component
{
    #[Rule('required|in:chihuahua,juarez', message: [
        'required' => 'La zona es obligatoria.',
        'in' => 'La zona seleccionada no es válida.',
    ])]
    public $zone = '';

    #[Rule('required|exists:employees,matricula|unique:mothers_day_registrations,matricula', message: [
        'required' => 'La matrícula es obligatoria.',
        'exists' => 'La matrícula no se encuentra registrada como empleada.',
        'unique' => 'Esta matrícula ya ha sido registrada para el evento.',
    ])]
    public $matricula = '';

    #[Rule('required|min:3|max:100', message: [
        'required' => 'El nombre completo es obligatorio.',
        'min' => 'El nombre debe tener al menos 3 caracteres.',
        'max' => 'El nombre es demasiado largo.',
    ])]
    public $name = '';

    #[Rule('required|email|max:100', message: [
        'required' => 'El correo electrónico es obligatorio.',
        'email' => 'El correo electrónico no es válido.',
        'max' => 'El correo es demasiado largo.',
    ])]
    public $email = '';

    #[Rule('required|digits:10', message: [
        'required' => 'El teléfono es obligatorio.',
        'digits' => 'El teléfono debe ser de 10 dígitos.',
    ])]
    public $phone = '';

    public $isRegistered = false;

    public $zones = [
        'chihuahua' => 'Chihuahua',
        'juarez' => 'Juárez',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function register()
    {
        $this->validate();

        RegistrationModel::create([
            'zone' => $this->zone,
            'matricula' => $this->matricula,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
        ]);

        $this->isRegistered = true;
        session()->flash('success', '¡Tu registro ha sido exitoso! Gracias por participar.');
        
        $this->reset(['zone', 'matricula', 'name', 'email', 'phone']);
    }

    public function render()
    {
        return view('livewire.public.mothers-day-registration')
            ->layout('layouts.guest');
    }
}
