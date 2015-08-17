<?php
session_cache_limiter(false);
session_start();
require 'vendor/autoload.php';

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('krfw6vjpntwc8ngf');
Braintree_Configuration::publicKey('ktj2yhb84kcthxcx');
Braintree_Configuration::privateKey('3720bfff590f9e0d4994b0df48199f49');

# App config
$app = new \Slim\Slim(array(
  'templates.path' => './templates/',
  'view' => new \Slim\Views\Twig()
));
$app->view->setTemplatesDirectory('./templates');

# Make default method
$app->get('/card/make_default/:index', function($index) use ($app) {
  $customer = get_customer();
  $card = $customer->creditCards[$index];
  $result = Braintree_PaymentMethod::update($card->token, ['options' => ['makeDefault' => true]]);
  $app->redirect('/');
});

# Add card method
$app->post('/card/create', function() use ($app) {
  $customer = get_customer();
  Braintree_PaymentMethod::create([
    'customerId' => $customer->id,
    'paymentMethodNonce' => $app->request()->post('payment_method_nonce')
  ]);
  $app->redirect('/');
});

# New card form
$app->get('/card/new', function() use ($app) {
  $clientToken = Braintree_ClientToken::generate();
  $app->render('card/new.php',array('clientToken' => $clientToken));
});

$app->get('/', function() use ($app) {
  $customer = get_customer();
  $app->render('index.php',array('customer' => $customer));
});
$app->run();



function get_customer() {
  $id = $_SESSION["demoUser"];
  try {
    $customer = Braintree_Customer::find($id);
  } catch (Exception $e){
    $customer = Braintree_Customer::create()->customer;
    $_SESSION["demoUser"] = $customer->id;
  }
  return $customer;
}
