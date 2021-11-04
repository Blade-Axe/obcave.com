<?php 
include_once('assets/includes/paypal.php');
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;


if (IS_LOGGED == false || $music->config->go_pro != 'on') {
	header("Location: $site_url/404");
	exit();
}
if (empty($path['options'][1]) || empty($_GET['paymentId']) || empty($_GET['token']) || empty($_GET['PayerID'])) {
	header("Location: $site_url/payment-error");
	exit();
}

if ($user->is_pro == 1) {
	header("Location: $site_url/payment-error?reason=already-pro");
	exit();
}
$PayerID = secure($_GET['PayerID']);
$token = secure($_GET['token']);
$paymentId = secure($_GET['paymentId']);

$payment   = Payment::get($paymentId, $paypal);
$execute   = new PaymentExecution();
$execute->setPayerId($PayerID);

try{
    $result = $payment->execute($execute, $paypal);
    if ($result) {
    	$updateUser = $db->where('id', $user->id)->update(T_USERS, ['is_pro' => 1, 'pro_time' => time()]);
    	if ($updateUser) {
            CreatePayment(array(
                'user_id'   => $id,
                'amount'    => $music->config->pro_price,
                'type'      => 'PRO',
                'pro_plan'  => 1,
                'info'      => '',
                'via'       => 'PayPal'
            ));

            if ((!empty($_SESSION['ref']) || !empty($user->ref_user_id)) && $music->config->affiliate_type == 1 && $user->referrer == 0) {
                if ($music->config->amount_percent_ref > 0) {
                    if (!empty($_SESSION['ref'])) {
                        $ref_user_id = $db->where('username', secure($_SESSION['ref']))->getValue(T_USERS, 'id');
                    }
                    elseif (!empty($user->ref_user_id)) {
                        $ref_user_id = $db->where('id', $user->ref_user_id)->getValue(T_USERS, 'id');
                    }
                    if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                        $db->where('id', $user->user_id)->update(T_USERS,array(
                                                                            'referrer' => $ref_user_id,
                                                                            'src' => 'Referrer'
                                                                        ));
                        $ref_amount     = ($music->config->amount_percent_ref * $music->config->pro_price) / 100;
                        $db->where('id', $ref_user_id)->update(T_USERS,array('balance' => $db->inc($ref_amount)));
                        unset($_SESSION['ref']);
                    }
                } else if ($music->config->amount_ref > 0) {
                    if (!empty($_SESSION['ref'])) {
                        $ref_user_id = $db->where('username', secure($_SESSION['ref']))->getValue(T_USERS, 'id');
                    }
                    elseif (!empty($user->ref_user_id)) {
                        $ref_user_id = $db->where('id', $user->ref_user_id)->getValue(T_USERS, 'id');
                    }
                    if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                        $db->where('id', $user->user_id)->update(T_USERS,array(
                                                                            'referrer' => $ref_user_id,
                                                                            'src' => 'Referrer'
                                                                        ));
                        $db->where('id', $ref_user_id)->update(T_USERS,array('balance' => $db->inc($music->config->amount_ref)));
                        unset($_SESSION['ref']);
                    }
                }
            }


    		header("Location: $site_url/upgraded");
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
