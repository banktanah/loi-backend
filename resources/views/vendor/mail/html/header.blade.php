<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="http://localhost:9393/static/media/logo-LOI1.4aaca4b9dd4cbbf4356a.png" class="logo" alt="LOI Logo">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
