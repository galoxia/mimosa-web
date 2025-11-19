{{--<x-mail::message>--}}
{{--<p style="color: #ff2323; font-size: 18px">Hola, {{ $name }}</p>--}}

{{--Has recibido este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta.--}}

{{--Haz clic en el siguiente botón para restablecer tu contraseña:--}}

{{--<x-mail::button :url="$url">--}}
{{--    Restablecer Contraseña--}}
{{--</x-mail::button>--}}

{{--Este enlace de restablecimiento de contraseña caducará en 60 minutos.--}}

{{--Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna acción.--}}

{{--Saludos,--}}

{{--El equipo de {{ config('app.name') }}--}}

{{--<p style="font-size: 12px; color: #6b7280">--}}
{{--    Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web: {{ $url }}--}}
{{--</p>--}}
{{--</x-mail::message>--}}

    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablecer Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            /*color: #333;*/
            margin: 0;
            padding: 0;
            text-align: center;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            /*background-color: darkred;*/
            /*color: white;*/
            padding: 20px;
            /*text-align: center;*/
        }

        .content {
            padding: 20px;
            /*background-color: #f9fafb;*/
        }

        .button {
            display: inline-block;
            background-color: darkred;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1 style="font-size: 28px">Foto Estudio <span style="color: darkred; font-weight: lighter">MIMOSA</span></h1>
        <img src="{{ $message->embed(storage_path('app/private/messages/1/attachments/olga.jpg')) }}" alt="Logo">
    </div>
    <div class="content">
        <p style="color: darkred; font-size: 18px">Hola, {{ $name }}</p>
        <p>Has recibido este correo porque hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
        <p>Haz clic en el siguiente botón para restablecer tu contraseña:</p>
        <p>
            <a href="{{ $url }}" class="button">Restablecer Contraseña</a>
        </p>
        <p>Este enlace de restablecimiento de contraseña caducará en 60 minutos.</p>
        <p>Si no solicitaste un restablecimiento de contraseña, no es necesario realizar ninguna acción.</p>
        <p>Saludos,<br>El equipo de {{ config('app.name') }}</p>
    </div>
    <div class="footer">
        <p>Si tienes problemas para hacer clic en el botón "Restablecer Contraseña", copia y pega la siguiente URL en tu navegador web: {{ $url }}</p>
    </div>
</div>
</body>
</html>
