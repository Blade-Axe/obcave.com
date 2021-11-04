<?php
require_once('assets/import/stripe-php-7.52.0/init.php');
global $music;
$data = array();

$stripe = array(
    'secret_key' => $music->config->stripe_secret,
    'publishable_key' => $music->config->stripe_id
);
\Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);

$product        = Secure($_POST[ 'description' ]);
$realprice      = Secure($_POST[ 'price' ]);
$price          = Secure($_POST[ 'price' ]) * 100;
$amount         = 0;
$currency       = strtolower($music->config->stripe_currency);
$payType        = Secure($_POST[ 'payType' ]);
$membershipType = 0;

try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => $currency,
                'product_data' => [
                    'name' => $product,
                ],
                'unit_amount' => $price,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => $music->config->site_url . "/stripe-wallet/true",
        'cancel_url' => $music->config->site_url . "/stripe-wallet/false",
    ]);

    $_SESSION['stripe_session_payment_intent'] = $session->payment_intent;
    $data = array(
        'status' => 200,
        'id' => $session->id
    );
    header('Content-type: application/json; charset=UTF-8');
    echo json_encode($data);
    exit();
} catch (Exception $e) {
    $data = array(
        'status' => 400,
        'message' => $e->getMessage()
    );
}
