<?php

namespace App\View\Components;

use App\Models\Calendar;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CalendarView extends Component
{
    public Calendar $calendar;

    public function __construct( Calendar $calendar )
    {
        $this->calendar = $calendar;
    }

    public function render(): View|Closure|string
    {
        return view( 'components.calendar-view' );
    }
}
