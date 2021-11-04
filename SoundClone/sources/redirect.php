<?php
    $ad_id = secure($path['options'][1]);
    if (!IS_LOGGED || $music->config->user_ads != 'on' || empty($ad_id) || !is_numeric($ad_id)) {
        header('Location: ' . $site_url);
        exit;
    }

    if  ($_GET['type'] == 'pagead' && !empty($_SESSION['pagead'])) {
        if ($ad_id != $_SESSION['pagead']) {
            error_log("Error -Could not make transaction: The Page ad is Invalid", 0);
            global $site_url;
            redirect($site_url . '/');
        }
    }

    $ad     = $db->where('id',$ad_id)->getOne(T_USR_ADS);
    if (!empty($ad)) {
        $ad_owner     = $db->where('id',$ad->user_id)->getOne(T_USERS);
        $con_price    = $music->config->ad_c_price;
        $ad_trans     = false;
        $ad_url       = urldecode($ad->url);
        $is_owner     = false;
        $ad_tans_data = array(
            'results' => ($ad->results += 1)
        );

        if (IS_LOGGED) {
            $is_owner = ($ad->user_id == $user->id) ? true : false;
        }
        if (!array_key_exists($ad_id, $music->user_ad_cons['uaid_']) && !$is_owner) {
            $ad_tans_data['spent']               = ($ad->spent += $con_price);
            $ad_trans                            = true;
            $music->user_ad_cons['uaid_'][$ad->id]  = $ad->id;
            setcookie('_uads', htmlentities(serialize($music->user_ad_cons)), time() + (10 * 365 * 24 * 60 * 60),'/');
            $db->insert(T_ADS_TRANS,array('amount' => $con_price ,'type' => 'spent', 'ad_id' => $ad->id, 'time' => time()));
        }
        if ($ad->type == 1) {
            $type_ = 'click';
        }
        else{
            $type_ = 'view';
        }
        $update       = $db->where('id',$ad_id)->update(T_USR_ADS,$ad_tans_data);
        if ($update && $ad_trans && !$is_owner) {
            $db->insert(T_ADS_TRANS,array('type' => $type_, 'ad_id' => $ad->id, 'time' => time()));
            $user_wallet = $ad_owner->wallet - $con_price;
            if ($user_wallet < $con_price) {
                $db->where('id', $ad->id)->delete(T_USR_ADS);
            }
            $db->where('id',$ad_owner->id)->update(T_USERS,array('wallet' => ($ad_owner->wallet -= $con_price)));
            if ($ad->day_limit > 0) {
                if ($ad->day == date("Y-m-d")) {
                    $db->where('id',$ad->id)->update(T_USR_ADS,array('day_spend' => ($ad->day_spend + $con_price)));
                }
                else{
                    $db->where('id',$ad->id)->update(T_USR_ADS,array('day_spend' => $con_price ,
                        'day'       => date("Y-m-d")));
                }
            }
        }
        header("Location: $ad_url");
        exit();
    }
