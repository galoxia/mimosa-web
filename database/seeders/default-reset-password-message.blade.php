<p style="color: #800001; font-size: 18px">
    <strong>Hola, :nombre</strong>
</p>

Has recibido este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta.

Haz clic en el siguiente botón para restablecer tu contraseña:

<x-mail::button url=":enlace">
    Restablecer Contraseña
</x-mail::button>

Este enlace de restablecimiento de contraseña caducará en 60 minutos.

Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna acción.

Saludos,

El equipo de {{ config('app.name') }}

<p style="font-size: 12px; color: #6b7280">
    Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web: :enlace
</p>
