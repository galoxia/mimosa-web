@props(['url'])
<tr>
{{--<td class="header">--}}
<td>
{{--<a href="{{ $url }}" style="display: inline-block;">--}}
{{--{!! $slot !!}--}}
{{--</a>--}}
<table class="header" align="center" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
{!! $slot !!}
</td>
</tr>
</table>
</td>
</tr>
