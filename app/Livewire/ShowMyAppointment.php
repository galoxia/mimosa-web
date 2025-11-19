<?php

namespace App\Livewire;

use App\Models\Appointment;
use Livewire\Attributes\On;
use Livewire\Component;

class ShowMyAppointment extends Component
{
    public ?int $appointment_id = null;

    public function render()
    {
        return view( 'livewire.show-my-appointment', [ 'appointment' => Appointment::find( $this->appointment_id ) ] );
    }

    private function setAppointment(): void
    {
        $this->appointment_id = auth()->user()->appointments()->first()?->id;
    }

    public function mount(): void
    {
        $this->setAppointment();
    }

    #[On( 'book' )]
    #[On( 'unbook' )]
    public function onBookUnbook(): void
    {
        $this->setAppointment();
    }
}
