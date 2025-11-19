<?php

namespace App\Services;

final class Flash
{
    public static function error( string $status ): void
    {
        session()->flash( 'status', $status );
        session()->flash( 'variant', 'danger' );
    }

    public static function success( string $status ): void
    {
        session()->flash( 'status', $status );
        session()->flash( 'variant', 'info' );
    }

    public static function warning( string $status ): void
    {
        session()->flash( 'status', $status );
        session()->flash( 'variant', 'warning' );
    }
}
