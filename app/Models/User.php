<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use App\Services\DateUtils;
use Carbon\Carbon;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;
use Lab404\Impersonate\Models\Impersonate;


class User extends Authenticatable implements AdminModelInterface
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use AdminModelTrait;

    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime:Y-m-d',
            'password' => 'hashed',
        ];
    }

    function getIsAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    public function getPublicNameAttribute(): ?string
    {
        // TODO: Aquí habrá que mirar si es profesor, coger el ->teacher?->name
        return $this->student?->name ?? /* $this->teacher?->name ?? */ $this->name;
    }

    /**
     * Student profile for this User (1:1)
     */
    public function student(): HasOne
    {
        return $this->hasOne( Student::class );
    }

    /**
     * Send the password reset notification.
     *
     * @param string $token
     * @return void
     */
    public function sendPasswordResetNotification( $token )
    {
        $this->notify( new ResetPasswordNotification( $token ) );
    }

    public function appointments(): HasMany
    {
        return $this->hasMany( Appointment::class );
    }

    public function canImpersonate(): bool
    {
        return $this->is_admin;
    }

    function canBook(): bool
    {
        $manager = app( 'impersonate' );
        // Un administrador puede reservar siempre.
        if ( $this->is_admin || $manager->isImpersonating() ) {
            return true;
        }
        // Un usuario normal puede reservar si no tiene ya reserva o si no la tiene en los próximos 2 días (excluyendo domingos y festivos).
        $when = Carbon::tomorrow();
        while ( $when->isSunday() || DateUtils::isHoliday( $when ) ) {
            $when->addDay();
        }

        return $this->appointment?->schedule_date->greaterThan( $when ) ?? true;
    }

    public function getAppointmentAttribute(): ?Appointment
    {
        return $this->appointments()->newest()->first();
    }

    public function canBeImpersonated(): bool
    {
        return !$this->is_admin;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    static function getIndexDefinitions(): array
    {
        return [
//            'id' => [ 'label' => '#ID' ],
            'student' => [
                'type' => 'relation',
                'label' => 'Alumno',
            ],
            'email' => [ 'label' => 'Email' ],
            'is_admin' => [
                'type' => 'bool',
                'label' => '¿Admin?'
            ],
        ];
    }

    static function getSingularName(): string
    {
        return 'Usuario';
    }

    static function getPluralName(): string
    {
        return 'Usuarios';
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre',
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
                'attributes' => [
                    'autocomplete' => 'email',
                ],
                'validation' => [ 'unique:users,email' ],
            ],
            'role' => [
                'type' => 'select',
                'label' => 'Rol',
                'options' => [
                    'user' => 'Usuario',
                    'admin' => 'Administrador',
                ],
                'validation' => [ 'in:user,admin' ],
            ],
            'password' => [
                'type' => 'password',
                'label' => 'Nueva contraseña',
                'component' => 'password-input',
                'attributes' => [
                    'autocomplete' => 'new-password',
                ],
                'validation' => [ 'confirmed' ],
            ],
            'password_confirmation' => [
                'type' => 'password',
                'label' => 'Confirmar contraseña',
                'component' => 'password-input',
                'attributes' => [
                    'autocomplete' => 'new-password',
                ]
            ]
        ];
    }

    function getUpdateFormDefinitions(): array
    {
        return [
            'name' => [
                'label' => 'Nombre',
            ],
            'email' => [
                'type' => 'email',
                'label' => 'Email',
                'component' => 'shielded-input',
                'attributes' => [
                    'autocomplete' => 'email',
                ],
                'validation' => [ 'unique:users,email,' . $this->id ],
            ],
            'role' => [
                'type' => 'select',
                'label' => 'Rol',
                'options' => [
                    'user' => 'Usuario',
                    'admin' => 'Administrador',
                ],
                'validation' => [ 'in:user,admin' ],
            ],
            'password' => [
                'component' => 'password-input',
                'type' => 'password',
                'required' => false,
                'label' => 'Nueva contraseña',
                'attributes' => [
                    'autocomplete' => 'new-password',
                ],
                'validation' => [ 'confirmed' ],
                'getter' => fn( $value ) => null,
                'set_if_null' => false,
            ],
            'password_confirmation' => [
                'component' => 'password-input',
                'type' => 'password',
                'required' => false,
                'label' => 'Confirmar contraseña',
                'attributes' => [
                    'autocomplete' => 'new-password',
                ]
            ]
        ];
    }
}
