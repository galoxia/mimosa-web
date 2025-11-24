<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\CRUDControllerDispatcher;
use App\Http\Controllers\Admin\EntityCRUDController;
use App\Http\Controllers\Admin\UserCRUDController;
use App\Http\Controllers\Api\DegreeController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;

Route::get( '/', function () {
    session()->reflash();

    if ( auth()->user() ) {
        return redirect()->route( 'account.dashboard' );
    } else {
        return redirect()->route( 'login' );
    }
} )->name( 'home' );

Route::get( '/testing', function () {
    return view( 'test' )->with( [
        'foo' => 1
    ] );
} )->name( 'testing' );

Route::impersonate();

// Admin routes
Route::middleware( AdminMiddleware::class )->group( function () {
    Route::get( '/admin/dashboard', [ AdminDashboardController::class, 'index' ] )->name( 'admin.dashboard' );
    Route::get( '/admin/crud/{action}', [ CRUDControllerDispatcher::class, 'get' ] )
        ->where( 'action', 'index|update|create' )->name( 'admin.crud.get' );
    Route::post( '/admin/crud/{action}', [ CRUDControllerDispatcher::class, 'post' ] )
        ->where( 'action', 'filter|massive-update|create|update' )->name( 'admin.crud.post' );
    Route::delete( '/admin/crud/delete', [ CRUDControllerDispatcher::class, 'delete' ] )->name( 'admin.crud.delete' );
    Route::post( '/admin/crud/datatable', [ EntityCRUDController::class, 'datatable' ] )->name( 'admin.crud.datatable' );
    // Rutas de controladores específicos
    Route::post( '/admin/crud/users/{user}/send', [ UserCRUDController::class, 'send' ] )->name( 'admin.crud.users.send' );
//    Route::post( '/admin/crud/users/{user}/sendSMS', [ UserCRUDController::class, 'sendSMS' ] )->name( 'admin.crud.users.sendSMS' );

    // API para el entorno de administración
    Route::post( '/api/messages/preview', [ MessageController::class, 'preview' ] )->name( 'api.messages.preview' );
    Route::get( '/api/degrees/{degree}', [ DegreeController::class, 'show' ] )->name( 'api.degrees.show' );
//    Route::get( '/api/degrees/{degree}/next-student-number', [ DegreeController::class, 'nextStudentNumber' ] )->name( 'api.degrees.next-student-number' );
//    Route::get( '/api/degrees/{degree}/next-teacher-number', [ DegreeController::class, 'nextTeacherNumber' ] )->name( 'api.degrees.next-teacher-number' );
//    Route::get( '/api/prices/{product}/{degree?}', [ PriceController::class, 'get' ] )->name( 'api.prices.get' );
    Route::get( '/api/products/{product}/{degree?}', [ ProductController::class, 'show' ] )->name( 'api.products.show' );
} );

Route::middleware( 'auth' )->group( function () {
    Route::post( '/logout', [ LoginController::class, 'logout' ] )->name( 'logout' );

    // Client account routes
    Route::get( '/dashboard', [ AccountController::class, 'index' ] )->name( 'account.dashboard' );
    Route::get( '/dashboard/help', [ AccountController::class, 'showHelp' ] )->name( 'account.help' );

    Route::get( '/dashboard/my-data', [ AccountController::class, 'editProfile' ] )->name( 'account.profile.edit' );
    Route::put( '/dashboard/my-data', [ AccountController::class, 'updateProfile' ] )->name( 'account.profile.update' );
    Route::put( '/dashboard/my-data/password', [ AccountController::class, 'updatePassword' ] )->name( 'account.profile.update-password' );

    // API de cliente
//    Route::middleware( 'throttle:6,1' )->group( function () {
//    } );
} );

Route::middleware( 'guest' )->group( function () {
    // Authentication Routes
    Route::get( '/login', [ LoginController::class, 'showLoginForm' ] )->name( 'login' );
    Route::post( '/login', [ LoginController::class, 'login' ] );

    // Registration Routes
    Route::get( '/register', [ RegisterController::class, 'showRegistrationForm' ] )->name( 'register' );
    Route::post( '/register', [ RegisterController::class, 'register' ] );

    // Password Reset Routes
    Route::get( '/forgot-password', [ ForgotPasswordController::class, 'showLinkRequestForm' ] )->name( 'password.request' );
    Route::post( '/forgot-password', [ ForgotPasswordController::class, 'sendResetLinkEmail' ] )
        ->middleware( 'throttle:6,1' )
        ->name( 'password.email' );
    Route::get( '/reset-password/{token}', [ ForgotPasswordController::class, 'showResetForm' ] )->name( 'password.reset' );
    Route::post( '/reset-password', [ ForgotPasswordController::class, 'reset' ] )->name( 'password.update' );
} );

