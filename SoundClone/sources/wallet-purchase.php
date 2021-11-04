<?php 
include_once('assets/includes/paypal.php');
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;

if( !isset($_GET['price']) ){
    header("Location: $site_url/404");
    exit();
}
if (IS_LOGGED == false ) {
	header("Location: $site_url/404");
	exit();
}
if (empty($path['options'][1]) || empty($_GET['paymentId']) || empty($_GET['token']) || empty($_GET['PayerID'])) {
	header("Location: $site_url/payment-error");
	exit();
}

if ($path['options'][1] !== 'true') {
    header("Location: $site_url/payment-error");
    exit();
}
$price = (int)secure($_GET['price']);
$PayerID = secure($_GET['PayerID']);
$token = secure($_GET['token']);
$paymentId = secure($_GET['paymentId']);

$payment   = Payment::get($paymentId, $paypal);
$execute   = new PaymentExecution();
$execute->setPayerId($PayerID);

try{
    $result = $payment->execute($execute, $paypal);
    if ($result) {
    	$updateUser = $db->where('id', $user->id)->update(T_USERS, ['wallet' => $db->inc($price)]);
    	if ($updateUser) {
            CreatePayment(array(
                'user_id'   => $user->id,
                'amount'    => $price,
                'type'      => 'WALLET',
                'pro_plan'  => 0,
                'info'      => 'Replenish My Balance',
                'via'       => 'PayPal'
            ));
    		header("Location: $site_url/ads");
	        exit();
    	} else {
    		header("Location: $site_url/payment-error?reason=cant-create-payment");
			exit();
    	}
    }
}

catch (Exception $e) {
    header("Location: $site_url/payment-error?reason=invalid-payment");
	exit();
}
