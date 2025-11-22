<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Message::create( [
            'name' => 'Ticket1',
            'type' => Message::TICKET,
            'subject' => 'Tu recibo de pago en Foto MIMOSA',
            'from' => config( 'mail.from.address' ),
            'body' => file_get_contents( __DIR__ . '/default-ticket-message.blade.php' ),
            'show_background' => true
        ] );

        Message::create( [
            'name' => 'Ticket profesor1',
            'type' => Message::TEACHER_TICKET,
            'subject' => 'Tu ticket de profesor',
            'from' => config( 'mail.from.address' ),
            'body' => file_get_contents( __DIR__ . '/default-teacher-ticket-message.blade.php' ),
            'show_background' => true
        ] );

        Message::create( [
            'name' => 'Recordar contraseña',
            'type' => Message::RESET_PASSWORD,
            'subject' => 'Solicitud de nueva contraseña',
            'from' => config( 'mail.from.address' ),
            'body' => file_get_contents( __DIR__ . '/default-reset-password-message.blade.php' ),
            'show_background' => false
        ] );

    }
}
