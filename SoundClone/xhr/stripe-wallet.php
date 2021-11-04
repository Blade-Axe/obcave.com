<?php
require_once('assets/import/stripe-php-3.20.0/vendor/autoload.php');
global $music;
$data = array();

$stripe = array(
    'secret_key' => $music->config->stripe_secret,
    'publishable_key' => $music->config->stripe_id
);
\Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);

$product        = Secure($_POST[ 'description' ]);
$realprice      = Secure($_POST[ 'price' ]);
$price          = Secure($_POST[ 'price' ]);
$amount         = 0;
$currency       = strtolower($music->config->stripe_currency);
$payType        = Secure($_POST[ 'payType' ]);
$membershipType = 0;
$token          = $_POST[ 'stripeToken' ];

if (empty($token)) {
    $data = array(
        'status' => 400,
        'message' => 'invalid token'
    );
}


try {
    $customer = \Stripe\Customer::create(array(
        'source' => $token
    ));
    $charge   = \Stripe\Charge::create(array(
        'customer' => $customer->id,
        'amount' => $price,
        'currency' => $currency
    ));
    if ($charge) {

        $updateUser = $db->where('id', $user->id)->update(T_USERS, ['wallet' => $db->inc($price)]);
        if ($updateUser) {
            CreatePayment(array(
                'user_id'   => $user->id,
                'amount'    => $price,
                'type'      => 'WALLET',
                'pro_plan'  => 0,
                'info'      => 'Replenish My Balance',
                'via'       => 'Stripe'
            ));
            $data = array(
                'status' => 200,
                'url' => "$site_url/ads"
            );
        } else {
            $data = array(
                'status' => 400,
                'message' => 'can not create payment'
            );
        }

    }
} catch (Exception $e) {
    $data = array(
        'status' => 400,
        'message' => $e->getMessage()
    );
}


header('Content-type: application/json; charset=UTF-8');
echo json_encode($data);
exit();