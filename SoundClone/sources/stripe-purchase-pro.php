<?php
require_once('assets/import/stripe-php-7.52.0/init.php');
if (IS_LOGGED == false) {
    header("Location: $site_url/404");
    exit();
}

if (empty($path['options'][1]) || !isset($_SESSION['stripe_session_payment_intent'])) {
    header("Location: $site_url/payment-error");
    exit();
}
if($path['options'][1] === 'false'){
    header("Location: $site_url/payment-error");
    exit();
}

$stripe = array(
    'secret_key' => $music->config->stripe_secret,
    'publishable_key' => $music->config->stripe_id
);
\Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);


$intent = \Stripe\PaymentIntent::retrieve($_SESSION['stripe_session_payment_intent']);
$charges = $intent->charges->data;

if($charges[0]->captured === 'false'){
    header("Location: $site_url/payment-error?reason=not-found");
    exit();
}

try{

    $updateUser = $db->where('id', $user->id)->update(T_USERS, ['is_pro' => 1, 'pro_time' => time()]);
    if ($updateUser) {
        CreatePayment(array(
            'user_id'   => $user->id,
            'amount'    => $music->config->pro_price,
            'type'      => 'PRO',
            'pro_plan'  => 1,
            'info'      => '',
            'via'       => 'Stripe'
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

} catch (Exception $e) {
    header("Location: $site_url/payment-error?reason=invalid-payment");
    exit();
}