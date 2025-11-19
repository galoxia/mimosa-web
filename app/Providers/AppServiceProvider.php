<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Password::defaults( fn() => Password::min( 8 )->mixedCase()->uncompromised() );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Para que Blade::render parsee los componentes de markdown "<x-mail::" hay que vincular el ns "mail" a la carpeta de vistas donde se encuentran los componentes.
        View::addNamespace( 'mail', resource_path( 'views/vendor/mail/html' ) );

        Blade::if( 'admin', fn() => auth()->user()?->is_admin );
        Blade::if( 'student', fn() => auth()->user()?->student !== null );
        // Enable foreign key constraints for SQLite
        if ( DB::getDriverName() === 'sqlite' ) {
            DB::statement( 'PRAGMA foreign_keys = ON;' );
        }

        Validator::extend( 'exists_with_foreign_keys', function ( $attribute, $value, $parameters, $validator ) {
            $table = $parameters[0];
            $column = $parameters[1];
            $foreignKeys = array_slice( $parameters, 2 );

            $query = DB::table( $table )->where( $column, $value );

            $data = $validator->getData();

            foreach ( $foreignKeys as $key ) {
                $query->where( $key, $data[ $key ] ?? null );
            }

            return $query->exists();
        } );

        Validator::extend( 'not_exists_with_foreign_keys', function ( $attribute, $value, $parameters, $validator ) {
            $table = array_shift( $parameters );
            $column = array_shift( $parameters );
            $ignoreId = array_pop( $parameters );
            if ( filter_var( $ignoreId, FILTER_VALIDATE_INT ) === false ) {
                $parameters = array_merge( $parameters, [ $ignoreId ] );
                $ignoreId = null;
            }
            $foreignKeys = $parameters;

            $query = DB::table( $table )->where( $column, $value );

            if ( $ignoreId ) {
                $query->where( 'id', '!=', $ignoreId );
            }

            $data = $validator->getData();

            foreach ( $foreignKeys as $key ) {
                $query->where( $key, $data[ $key ] ?? null );
            }

            return !$query->exists();
        } );

        Validator::replacer( 'not_exists_with_foreign_keys', function ( $message, $attribute, $rule, $parameters ) {
            $table = array_shift( $parameters );
            $column = array_shift( $parameters );
            $foreignKey = array_shift( $parameters );
            $translations = trans( 'validation.attributes' );

            return str_replace(
                [ ':table', ':column', ':foreign_key' ],
                [ $translations[ $table ] ?? $table, $translations[ $column ] ?? $column, $translations[ $foreignKey ] ?? $foreignKey ],
                $message
            );
        } );

    }
}
