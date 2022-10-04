<div>

    @push('laravel-cart-header-scripts')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

        {{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->getTotal() }}
    <form
        x-data=''
        x-init='
            stripe = Stripe("{{ config('laravel-cart.stripe.api_key') }}");
            elements = stripe.elements();
            cardElement = elements.create("card");
            cardElement.mount("#card-element");
        '
        x-on:submit='
            $event.preventDefault();

            fetch("/checkout/confirm", {
                method: "get",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content")
                }
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(response) {
                alert(response.check);

                if(response.check) {
                    stripe.confirmCardPayment(
                        "{{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->payment->body['client_secret'] }}",
                        {
                                payment_method : {
                                    card: cardElement
                                }
                        }
                    )
                    .then(function(response) {
                        alert(response)
                    })
                }
            });
        '
    >
        <label for="card-element">Card</label>
        <div id="card-element"></div>
        <x-button type='submit' class='p-8 bg-arbor-green-500 text-white'>Submit</x-button>
    </form>
</div>