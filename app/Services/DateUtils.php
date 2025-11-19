<?php

namespace App\Services;

use Carbon\Carbon;

final class DateUtils
{
    private const HOLIDAYS = [
        [ 1, 1 ], // 1 de enero
        [ 6, 1 ], // 6 de enero, etc...
        [ 1, 5 ],
        [ 12, 6 ],
        [ 15, 8 ],
        [ 8, 9 ],
        [ 12, 10 ],
        [ 1, 11 ],
        [ 6, 12 ],
        [ 8, 12 ],
        [ 25, 12 ],
    ];

    public static function getHolyWeekDates( int $year ): array
    {
        // Según el algoritmo de Computus (calendario Gregoriano)
        $a = $year % 19;
        $b = intdiv( $year, 100 );
        $c = $year % 100;
        $d = intdiv( $b, 4 );
        $e = $b % 4;
        $f = intdiv( $b + 8, 25 );
        $g = intdiv( $b - $f + 1, 3 );
        $h = ( 19 * $a + $b - $d - $g + 15 ) % 30;
        $i = intdiv( $c, 4 );
        $k = $c % 4;
        $l = ( 32 + 2 * $e + 2 * $i - $h - $k ) % 7;
        $m = intdiv( $a + 11 * $h + 22 * $l, 451 );
        $month = intdiv( $h + $l - 7 * $m + 114, 31 );
        $day = ( ( $h + $l - 7 * $m + 114 ) % 31 ) + 1;

        $easterSunday = Carbon::create( $year, $month, $day );
        $holyThursday = $easterSunday->copy()->subDays( 3 );
        $goodFriday = $easterSunday->copy()->subDays( 2 );

        return compact( 'easterSunday', 'holyThursday', 'goodFriday' );
    }

    public static function isHoliday( Carbon $date ): bool
    {
        $holyWeekDates = self::getHolyWeekDates( $date->year );

        $holidays = array_merge( self::HOLIDAYS, [
            [ $holyWeekDates['holyThursday']->day, $holyWeekDates['holyThursday']->month ],
            [ $holyWeekDates['goodFriday']->day, $holyWeekDates['goodFriday']->month ],
        ] );

        $theDayBefore = $date->copy()->subDay();
        // Son vacaciones si $date es un día festivo o si $date es lunes y el día anterior (domingo) fue festivo nacional.
        return
            in_array( [ $date->day, $date->month ], $holidays ) ||
            ( $date->isMonday() && in_array( [ $theDayBefore->day, $theDayBefore->month ], $holidays ) );
    }

    public static function getAcademicYear( Carbon $date = null ): int
    {
        $date = $date ?? now();
        return $date->month >= 9 ? $date->year : $date->year - 1;
    }
}
