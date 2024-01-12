<div>

    @push('laravel-cart-header-scripts')
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <script src="https://js.stripe.com/v3/"></script>
    @endpush
{{--        {{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->payment->client_secret }}--}}
{{--        {{ \Antidote\LaravelCart\Facades\Cart::getActiveOrder()->total }}--}}
    <form
        x-data=''
        x-init='
            stripe = await Stripe("{{ $stripe_api_key }}");
            elements = stripe.elements();
            cardElement = elements.create("card");
            cardElement.mount("#card-element");
        '
        x-on:submit='
            $event.preventDefault();

            fetch("{{ $checkout_confirm_url }}", {
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
                        "{{ $client_secret }}",
                        {
                            payment_method : {
                                card: cardElement
                            }
                        }
                    )
                    .then(function(response) {

                        if(response.error) {
                            const messageContainer = document.querySelector("#error-message");
                            messageContainer.textContent = response.error.message;
                        } else {
                            fetch("{{ $post_checkout_url }}", {
                                method: "get",
                                headers: {
                                    "Content-Type": "application/json",
                                    "X-CSRF-TOKEN": document.querySelector("meta[name=\"csrf-token\"]").getAttribute("content")
                                }
                            })
                            .then(function(response) {
                                location.href = "{{ $order_complete_url }}"
                            })
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
