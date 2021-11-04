<?php
if (!IS_LOGGED) {
    $data    = array(
        'status'  => '400',
        'errors' => array(
            'error_id' => '1',
            'error_text' => 'Not logged in'
        )
    );
} else {
    if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0 && !empty($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error    = null;
        $balance  = $music->user->balance;
        $user_id  = $music->user->id;
        $currency = $music->config->currency;
        // Check is unprocessed requests exits
        $db->where('user_id',$user_id);
        $db->where('status',0);
        $requests = $db->getValue(T_WITHDRAWAL_REQUESTS, 'count(*)');
        if (empty($requests)) {
            if ($balance >= $_POST['amount']) {
                if ($_POST['amount'] >= 50) {
                    $insert_data    = array(
                        'user_id'   => $user_id,
                        'amount'    => secure($_POST['amount']),
                        'email'     => secure($_POST['email']),
                        'requested' => time(),
                        'currency' => $currency,
                    );
                    $insert  = $db->insert(T_WITHDRAWAL_REQUESTS,$insert_data);
                    if (!empty($insert)) {
                        $data     = array(
                            'status'   => '200',
                            'success_type' => 'monetization',
                            'message'    => 'Your withdrawal request has been successfully sent!'
                        );
                    }
                }
                else{
                    $data    = array(
                        'status'  => '400',
                        'errors' => array(
                            'error_id' => '4',
                            'error_text' => 'The minimum withdrawal request is 50: '.$currency
                        )
                    );
                }
            }
            else{
                $data    = array(
                    'status'  => '400',
                    'errors' => array(
                        'error_id' => '5',
                        'error_text' => 'The amount bigger than your balance'
                    )
                );
            }
        }
        else{
            $data    = array(
                'status'  => '400',
                'errors' => array(
                    'error_id' => '3',
                    'error_text' => 'You can not submit withdrawal request until the previous requests has been approved / rejected'
                )
            );
        }
    }
    else{
        $data       = array(
            'status'     => '400',
            'errors'         => array(
                'error_id'   => '4',
                'error_text' => 'Bad Request, Invalid or missing parameter'
            )
        );
    }

}