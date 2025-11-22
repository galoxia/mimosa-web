@props(['config'])
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}

@page {
margin: 0;
}

@media print {
body {
max-width: 9cm !important;
}
.logo {
max-width: 260px !important;
}
.content-cell {
padding: 4px 8px !important;
}
td:has(> .content) {
text-align: left !important;
}
{{--body.type-{{ \App\Models\Message::TICKET }} td:has(> .content),--}}
{{--body.type-{{ \App\Models\Message::TEACHER_TICKET }} td:has(> .content) {--}}
{{--text-align: left;--}}
{{--}--}}
{{--body.type-{{ \App\Models\Message::TICKET }},--}}
{{--body.type-{{ \App\Models\Message::TEACHER_TICKET }} {--}}
{{--max-width: 9cm !important;--}}
{{--}--}}
}

</style>
{!! $head ?? '' !!}
</head>
<body class="{{ $config['body_class'] ?? '' }}">
<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table
class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation"
@if($config['show_background'] ?? false)
style="background-image: url({{ \App\Services\ImageUtils::getBase64(public_path('images/fachada.png')) }});"
@endif
>
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
