<div>

    @push('laravel-cart-header-scripts')
        <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush
{{--        {{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->payment->client_secret }}--}}
{{--        {{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->total }}--}}
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

            fetch("{{ config('laravel-cart.urls.checkout_confirm') }}", {
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

                if(response.check) {
                    stripe.confirmCardPayment(
                        "{{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->payment->client_secret }}",
                        {
                            payment_method : {
                                card: cardElement
                            }
                        }
                    )
                    .then(function(response) {

                        if(response.error) {

                        } else {
                            location.href = "{{ \Illuminate\Support\Facades\Config::get('laravel-cart.urls.order_complete') }}?order_id={{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->id }}"
                        }
                    })
                } else {
                    window.alert("cart amount has changed");
                    location.reload(true);
                }
            });
        '
    >
        <x-laravel-cart-stripe::card-form/>
    </form>
</div>
