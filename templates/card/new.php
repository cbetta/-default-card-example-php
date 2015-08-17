<form method="post" action="/card/create">
  <div id="payment-form"></div>
  <input type="submit" value="Add card">
</form>

<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script>
// We generated a client token for you so you can test out this code
// immediately. In a production-ready integration, you will need to
// generate a client token on your server (see section below).
var clientToken = "{{ clientToken }}";

braintree.setup(clientToken, "dropin", {
  container: "payment-form"
});
</script>
