<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Storage;

class Message extends Model implements AdminModelInterface
{
    use AdminModelTrait;

    protected $fillable = [
        'name',
        'type',
        'subject',
        'from',
        'cc',
        'body',
        'show_background',
        'priority',
    ];

    protected $casts = [
        'type' => 'integer',
        'show_background' => 'boolean',
        'priority' => 'integer',
    ];

    public function scopeOfType( $query, string $type ): void
    {
        $query->where( 'type', $type )->orderBy( 'priority', 'desc' );
    }

    public const MESSAGE = 0;
//    public const REGISTER = 1;
    public const RESET_PASSWORD = 2;
    public const TICKET = 3;
    public const TEACHER_TICKET = 4;
    public const UPCOMING_APPOINTMENT = 5;
    public const BIRTHDATE = 6;

    public const TYPES = [
        Message::MESSAGE => [
            'label' => 'Mensaje',
            'placeholders' => [
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
            ],
//            'placeholders' => [ ':name', ':surname1', ':surname2' ],
        ],
//        Message::REGISTER => 'Registro de usuario',
        Message::RESET_PASSWORD => [
            'label' => 'Restablecimiento de contraseña',
            'placeholders' => [
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
                ':enlace' => 'link'
            ],
//            'placeholders' => [ ':name', ':surname1', ':surname2', ':link' ],
        ],
        Message::TICKET => [
            'label' => 'Ticket',
            'placeholders' => [
                ':año' => 'year',
                ':código_taller' => 'workshop_code',
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
                ':nif' => 'identification_number',
                ':titulación' => 'degree',
                ':número' => 'student_number',
                ':producto' => 'product',
                ':precio' => 'price',
                ':conceptos' => 'concepts',
                ':pagado' => 'paid',
                ':resto' => 'pending',
                ':fecha' => 'today',
                ':consentimiento_individual' => 'single_marketing_consent',
                ':archivos_digitales' => 'wants_photo_files',
                ':fotos_grupales' => 'wants_group_photos',
            ],
//            'placeholders' => [ ':year', ':name', ':surname1', ':surname2', ':identification_number', ':degree', ':student_number', ':product', ':price', ':concepts', ':paid', ':pending', ':today', ':single_marketing_consent' ]
        ],
        Message::TEACHER_TICKET => [
            'label' => 'Ticket de profesor',
            'placeholders' => [
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
                ':número' => 'teacher_number',
                ':observaciones' => 'observations',
                ':titulaciones' => 'degrees',
                ':fecha' => 'today',
            ]
//            'placeholders' => [ ':name', ':surname1', ':surname2', ':observations', ':degrees' ],
        ],
        Message::UPCOMING_APPOINTMENT => [
            'label' => 'Recordatorio de 48 horas',
            'placeholders' => [
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
                ':hora' => 'time',
                ':día' => 'date',
                ':taller' => 'workshop',
            ],
//            'placeholders' => [ ':name', ':surname1', ':surname2', ':time', ':date', ':workshop' ],
        ],
        Message::BIRTHDATE => [
            'label' => 'Cumpleaños',
            'placeholders' => [
                ':nombre' => 'name', ':apellido1' => 'surname1', ':apellido2' => 'surname2',
                ':día' => 'date',
            ],
//            'placeholders' => [ ':name', ':surname1', ':surname2', ':date' ],
        ],
    ];

//    public function __construct( array $attributes = [] )
//    {
//        $this->attributes['cc'] = json_encode( [ 'test@uno.es', 'test@dos.es' ] );
//
//        parent::__construct( $attributes );
//    }

    public function __construct( array $attributes = [] )
    {
        parent::__construct( $attributes );
        // Establece los valores por defecto si no están ya establecidos.
        $this->attributes = array_merge( [
            'from' => config( 'mail.from.address' ),
            'type' => Message::MESSAGE,
            'show_background' => false,
            'priority' => 0,
        ], $this->attributes );
    }

    static function options(): array
    {
        return self::orderBy( 'name' )->pluck( 'name', 'id' )->toArray();
    }

    static function getIndexDefinitions(): array
    {
        return [
//            'id' => [
//                'label' => '#ID',
//            ],
            'name' => [
                'label' => 'Nombre'
            ],
            'type' => [
                'label' => 'Tipo',
                'getter' => fn( Message $message ) => self::TYPES[ $message->type ]['label'],
            ],
            'priority' => [
                'label' => 'Prioridad',
            ],
            'subject' => [
                'label' => 'Asunto'
            ],
            'from' => [
                'label' => 'De'
            ],
            'cc' => [
                'label' => 'Copia a',
                'getter' => fn( Message $message ) => implode( ', ', array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $message->cc ?? '' ) ) ) ),
            ],
            'body' => [
                'label' => 'Cuerpo',
                'getter' => fn( Message $message ) => sprintf( '%s%s', substr( $message->body, 0, 50 ), strlen( $message->body ) > 50 ? '...' : '' ),
            ],
            'attachments' => [
                'label' => '#Adjuntos',
                'getter' => fn( Message $message ) => count( $message->attachments ?? [] ),
            ],
        ];
    }

    public function getParsedBody( User $user, array $placeholders = [] ): string
    {
        // TODO: Aquí habrá que mirar si es profesor, coger el ->teacher?->surname1, etc.
        $placeholders = array_merge( [
            'nombre' => $user->public_name,
            'apellido1' => $user->student?->surname1,
            'apellido2' => $user->student?->surname2,
            'enlace' => config( 'app.url' )
        ], $placeholders );

        return preg_replace_callback( '/:([\p{L}_0-9]+)/u', function ( $matches ) use ( $placeholders ) {
            return $placeholders[ $matches[1] ] ?? $matches[0]; // Si no existe el reemplazo, deja el placeholder tal cual
        }, $this->body ?? '' );
    }

    static function getCreateFormDefinitions(): array
    {
        return [
            '_all' => [
                'sections' => 2,
                'enctype' => 'multipart/form-data',
            ],
            'name' => [
                'label' => 'Nombre'
            ],
            'type' => [
                'type' => 'select',
                'label' => 'Tipo',
                'options' => array_combine( array_keys( self::TYPES ), array_column( self::TYPES, 'label' ) ),
                'validation' => [ 'required', 'in:' . implode( ',', array_keys( self::TYPES ) ) ],
            ],
            'subject' => [
                'label' => 'Asunto'
            ],
            'from' => [
                'label' => 'De'
            ],
            'cc' => [
                'type' => 'textarea',
                'label' => 'CC',
                'footer' => [
                    'text' => 'Escribe cada destinatario en una línea'
                ],
                'required' => false,
            ],
            'body' => [
                'type' => 'textarea',
                'required' => false, // Para que el textarea no sea obligatorio en el front, ya que da problemas con el editor al no ser "focusable".
                'label' => 'Cuerpo',
                'label_class' => 'required', // Para que el label tenga el asterisco rojo de requerido.
                'class' => 'js-editor',
                'attributes' => [
                    'rows' => 10,
//                    'data-preview' => 'preview', // Id del elemento html donde se renderizará la vista previa del editor.
//                    'data-preview-url' => route( 'api.messages.preview' )
                ],
                'validation' => [ 'required' ], // Pero sí que lo exigimos al validar.
                'footer' => [
                    'text' => 'Etiquetas:' // Se rellenan desde el front al elegir el tipo de mensaje.
                ]
            ],
            'attachments' => [
                'type' => 'file',
                'required' => false,
                'attributes' => [
                    'multiple' => true,
                ],
                'label' => 'Adjuntos',
                'validation' => [ '*' => [ 'file', 'max:10240' ] ],
            ],
            'show_background' => self::getFieldTemplates( '¿Mostrar fondo?' )['bool'],
            'priority' => self::getFieldTemplates( 'Prioridad', max: 255 )['range'],
        ];
    }

    function getUpdateFormDefinitions(): array
    {
        $definitions = self::getCreateFormDefinitions();
        unset( $definitions['type'] );
        return $definitions;
    }

    function getAttachmentsAttribute(): array
    {
        $path = sprintf( '%s/%d/attachments', Str::plural( self::getClassSlug() ), $this->id );

        return array_map(
            fn( string $filename ) => basename( $filename ),
            Storage::files( $path )
        );
    }

    static function getSingularName(): string
    {
        return 'Mensaje';
    }

    static function getPluralName(): string
    {
        return 'Mensajes';
    }

    function __toString(): string
    {
        return $this->name;
    }
}
