<x-mail::message>
# Thank You

Your order has been created. Items will be shipped shortly.

<x-mail::table>
    | Product                  | Quantity         | Total  |
    | :----------------------- |:----------------:| ------:|
    @foreach($items as $item)
    | {{ $item['name'] }}<br/><sub><sup> {{ $item['description'] }}</sup></sub> | {{ $item['quantity'] }}      | {{$item['line_total']}}      |

    @endforeach
</x-mail::table>

<x-mail::table>
    | Totals      |                 |
    | :---------- |----------------:|
    | Subtotal    | {{ $subtotal }} |
    | Tax         | {{ $tax }}      |
    | Total       | {{ $total }}    |
</x-mail::table>

<x-mail::panel>
    ## Delivery Details
    {{ $address['line_1'] }}
    {{ $address['line_2'] }}
    {{ $address['town_city'] }}
    {{ $address['county'] }}
    {{ $address['postcode'] }}
</x-mail::panel>

Thanks,<br/>
{{ config('app.name') }}
</x-mail::message>
