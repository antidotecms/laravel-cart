{{--@dump($event_data)--}}
<table class='text-xs'>
@foreach($event_data as $key => $value)
    <tr>
        <td class='p-1'>{{ $key }}</td>
        <td class='p-1'>
            @if(!is_null($value) && !is_array($value))
                {{ $value }}
            @endif
        </td>
    </tr>
@endforeach
</table>
