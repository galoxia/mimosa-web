@props(['config'])
<x-mail::layout :config="$config">
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
<img class="logo" src="{{ \App\Services\ImageUtils::getBase64(public_path('images/logo.png')) }}" alt="Logo"/>
{{--<table style="border-spacing: 0; width: 100%; font-size: 10px">--}}
{{--<tbody>--}}
{{--<tr>--}}
{{--<td style="text-align: center">C/Corrales de Monroy, 6</td>--}}
{{--<td style="text-align: center">Tel. 923 26 43 29</td>--}}
{{--<td style="text-align: right">CIF: B37262086</td>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
@if(isset($footer))
{!! $footer !!}
@else
{{--<p>--}}
{{--    © {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}--}}
{{--</p>--}}
<table style="border-spacing: 0; text-align: center; width: 100%; font-size: 14px">
<tbody>
<tr>
<td>C/Corrales de Monroy, 6</td>
<td>Tel. 923 26 43 29</td>
{{--<td>37001 Salamanca</td>--}}
</tr>
<tr>
<td>
<a href="https://www.fotomimosa.es">www.fotomimosa.es</a>
</td>
{{--<td></td>--}}
<td>
<a href="mailto:info@fotomimosa.es">info@fotomimosa.es</a>
</td>
</tr>
</tbody>
</table>
@endif
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
