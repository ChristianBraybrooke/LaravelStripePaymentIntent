<script src="https://js.stripe.com/v3/"></script>
<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function () {
    // Variables
    var $cardholderName = document.getElementById('cardHolderName')
    var $errorElement = document.getElementById('paymentError')
    var $submitBtn = document.getElementById('cardSubmitBtn')
    var $paymentForm = document.getElementById('paymentForm')

    var paymentAmount = $paymentForm.dataset.amount
    var paymentCurrencyCode = $paymentForm.dataset.currency
    var submitBtnText = $submitBtn.innerHTML
    var submitBtnWaitingText = 'Processing Payment'

    // Initialise stripe
    var stripe = Stripe('{{ config('services.stripe.key') }}')
    var elements = stripe.elements()
    var cardElement = elements.create('card')
    cardElement.mount('#card-element')

    // Listen to the form
    $paymentForm.addEventListener('submit', function (event) {
      // Stop the form from being submitted.
      event.preventDefault()

      // Change the submit button text and disable.
      $submitBtn.innerHTML = submitBtnWaitingText
      $submitBtn.disabled = true

      stripeCreatePaymentMethod()
    }, false)

    function stripeCreatePaymentMethod () {
      if (formIsValid()) {
        // Let stripe create the payment method.
        stripe.createPaymentMethod('card', cardElement, {
          billing_details: {
            name: $cardholderName.value
          }
        })
          .then(function(result) {
            if (result.error) {
              // There was an error creating the payment method.
            } else {
              // Payment method created OK - send the payment_method_id to the server.
              fetch('/stripe-payments', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  payment_method_id: result.paymentMethod.id,
                  amount: paymentAmount,
                  currency: paymentCurrencyCode
                })
              })
                .then(function(result) {
                  // We have a response back from the server - handle it.
                  result.json().then(function(json) {
                    handleServerResponse(json)
                  })
            })
          }
        })
      } else {
        handleError({ message: 'Please complete all required feilds.' })
      }
    }

    function formIsValid () {
      return $cardholderName.value ? true : false
    }

    function handleServerResponse (response) {
      if (response.error) {
        handleError({ message: response.error })
      } else if (response.requires_action) {
        // Action is required on the client side (2FA / 3D Secure).
        stripe.handleCardAction(
          response.payment_intent_client_secret
        )
          .then(function (result) {
            // Authentication has failed or the customer has declined.
            if (result.error) {
              handleError(result.error)
            } else {
              // The customer has approved authentication and we can try again - this time with a payment intent id.
              fetch('/stripe-payments', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                  payment_intent_id: result.paymentIntent.id,
                  amount: paymentAmount,
                  currency: paymentCurrencyCode
                })
            })
              .then(function (confirmResult) {
                return confirmResult.json()
              })
              // Run through this again, but this time we (should) end up at the bottom and submit the form.
              .then(handleServerResponse);
          }
        })
      } else {
        // Add the payment ID to the form
        createHiddenInput('payment_record_id', response.payment_record.id)

        // Submit the form
        $paymentForm.submit()
      }
    }

    function createHiddenInput(name, value)
    {
      var $input = document.createElement('input')
      $input.type = 'hidden'
      $input.name = name
      $input.value = value
      $paymentForm.appendChild($input)
      return $input
    }

    // Handle and display any errors
    function handleError(error) {
      // Re-enable the form and submit button.
      $submitBtn.innerHTML = submitBtnText
      $submitBtn.disabled = false

      // Show the error on the form.
      $errorElement.innerHTML = '<div class="alert alert-danger" role="alert">' + error.message + '</div>'
      $errorElement.style.display = 'block'
    }
  })
</script>