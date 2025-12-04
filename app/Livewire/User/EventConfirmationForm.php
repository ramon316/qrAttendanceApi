<?php

namespace App\Livewire\User;

use App\Models\Event;
use App\Models\EventConfirmation;
use Livewire\Component;

class EventConfirmationForm extends Component
{
    public $eventId;
    public $zone = '';
    public $adults = 0;
    public $teenagers = 0;
    public $children = 0;
    public $confirmationId = null;
    public $isEditing = false;

    public $zones = [
        'chihuahua' => 'Chihuahua',
        'juarez' => 'Juárez',
    ];

    protected function rules()
    {
        return [
            'zone' => 'required|in:chihuahua,juarez',
            'adults' => 'required|integer|min:0',
            'teenagers' => 'required|integer|min:0',
            'children' => 'required|integer|min:0',
        ];
    }

    protected $messages = [
        'zone.required' => 'La zona es requerida.',
        'zone.in' => 'La zona seleccionada no es válida.',
        'adults.required' => 'El número de adultos es requerido.',
        'adults.integer' => 'El número de adultos debe ser un número entero.',
        'adults.min' => 'El número de adultos no puede ser negativo.',
        'teenagers.required' => 'El número de adolescentes es requerido.',
        'teenagers.integer' => 'El número de adolescentes debe ser un número entero.',
        'teenagers.min' => 'El número de adolescentes no puede ser negativo.',
        'children.required' => 'El número de niños es requerido.',
        'children.integer' => 'El número de niños debe ser un número entero.',
        'children.min' => 'El número de niños no puede ser negativo.',
    ];

    public function mount($eventId)
    {
        $this->eventId = $eventId;

        // Check if user already has a confirmation
        $confirmation = EventConfirmation::where('user_id', auth()->id())->first();

        if ($confirmation) {
            $this->isEditing = true;
            $this->confirmationId = $confirmation->id;
            $this->zone = $confirmation->zone;
            $this->adults = $confirmation->adults;
            $this->teenagers = $confirmation->teenagers;
            $this->children = $confirmation->children;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();

        // Validate that at least one attendee is specified
        $total = (int)$this->adults + (int)$this->teenagers + (int)$this->children;
        if ($total === 0) {
            $this->addError('total', 'Debes especificar al menos un asistente.');
            return;
        }

        $data = [
            'user_id' => auth()->id(),
            'event_id' => $this->eventId,
            'zone' => $this->zone,
            'adults' => $this->adults,
            'teenagers' => $this->teenagers,
            'children' => $this->children,
        ];

        if ($this->isEditing) {
            // Update existing confirmation
            $confirmation = EventConfirmation::find($this->confirmationId);
            $confirmation->update($data);

            session()->flash('message', 'Tu confirmación ha sido actualizada exitosamente.');
        } else {
            // Create new confirmation
            EventConfirmation::create($data);

            $this->isEditing = true;
            session()->flash('message', 'Tu confirmación ha sido registrada exitosamente.');
        }

        $this->dispatch('confirmation-saved');
    }

    public function getTotalAttendeesProperty()
    {
        return (int)$this->adults + (int)$this->teenagers + (int)$this->children;
    }

    public function render()
    {
        return view('livewire.user.event-confirmation-form');
    }
}
