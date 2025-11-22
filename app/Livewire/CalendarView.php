<?php

namespace App\Livewire;

use App\Models\Calendar;
use Livewire\Attributes\On;
use Livewire\Component;

class CalendarView extends Component
{
    /**
     * @var Calendar[]
     */
    public array $calendars;

    public function mount( array $calendars ): void
    {
        $this->calendars = $calendars;
    }

    public function render()
    {
        return view( 'livewire.calendar-view' );
    }

    #[On( 'book' )]
    #[On( 'unbook' )]
    public function onBookUnbook(): void
    {
    }
}
