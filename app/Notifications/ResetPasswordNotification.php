<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Lang;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The callback that should be used to create the reset password URL.
     *
     * @var \Closure|null
     */
    public static $createUrlCallback;

    /**
     * Create a notification instance.
     *
     * @param string $token
     * @return void
     */
    public function __construct(
        public string $token
    )
    {

    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via( $notifiable )
    {
        return [ 'mail' ];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail( $notifiable )
    {
        $url = $this->resetUrl( $notifiable );

        $message = Message::ofType( Message::RESET_PASSWORD )->firstOrFail();
        debug( $notifiable );
        return ( new MailMessage )
            ->subject( Lang::get( $message->subject ) )
            ->markdown( 'emails.messages.default', [
                'content' => Blade::render( $message->getParsedBody( $notifiable, [
                    'enlace' => $url,
                ] ) ),
                'config' => [
                    'show_background' => $message->show_background,
                ]
            ] );
    }

    /**
     * Get the reset password URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function resetUrl( $notifiable )
    {
        if ( static::$createUrlCallback ) {
            return call_user_func( static::$createUrlCallback, $notifiable, $this->token );
        }

        return url( route( 'password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false ) );
    }

    /**
     * Set a callback that should be used when creating the reset password button URL.
     *
     * @param \Closure $callback
     * @return void
     */
    public static function createUrlUsing( $callback )
    {
        static::$createUrlCallback = $callback;
    }
}
