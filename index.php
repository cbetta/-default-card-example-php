<?php
session_cache_limiter(false);
session_start();
require 'vendor/autoload.php';

Braintree_Configuration::environment('sandbox');
Braintree_Configuration::merchantId('ffdqc9fyffn7yn2j');
Braintree_Configuration::publicKey('qj65nndbnn6qyjkp');
Braintree_Configuration::privateKey('a3de3bb7dddf68ed3c33f4eb6d9579ca');

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
  $result = Braintree_PaymentMethod::update($card->token, ['options' => ['makeDefault' => true, 'verifyCard' => false]]);
  // $result = Braintree_Customer::update($customer->id, ['creditCard' => ['options' => ['makeDefault' => true, 'updateExistingToken' => $card->token, 'verifyCard' => false]]]);

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
