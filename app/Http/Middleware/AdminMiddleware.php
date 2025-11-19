<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle( Request $request, Closure $next ): Response
    {
        if ( !auth()->user()?->is_admin ) {
            return redirect()->route( 'home' )->with( [
                'status' => __( 'Acceso denegado. El usuario no tiene los permisos necesarios.' ),
                'variant' => 'danger'
            ] );
        }

        return $next( $request );
    }
}
