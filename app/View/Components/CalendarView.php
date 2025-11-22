<?php

namespace App\View\Components;

use App\Models\Calendar;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CalendarView extends Component
{
    /**
     * @var Calendar[]
     */
    public array $calendars;

    public function __construct( array $calendars )
    {
        $this->calendars = $calendars;
    }

    public function render(): View|Closure|string
    {
        return view( 'components.calendar-view' );
    }
}
