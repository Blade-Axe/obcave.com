<?php
if (!IS_LOGGED || $music->config->user_ads != 'on') {
    header("Location: $site_url/404");
    exit;
}

$payment_currency = $music->config->currency;
//$currency         = !empty($music->config->currency_symbol_array[$music->config->currency]) ? $music->config->currency_symbol_array[$music->config->currency] : '$';
$currency = $music->config->currency_symbol;
$db->where('user_id',$user->id)->where('day_limit',0,'>')->where('day',date("Y-m-d"),'!=')->update(T_USR_ADS,array('day' => date("Y-m-d"), 'day_spend' => 0));

$user_ads        = $db->where('user_id',$user->id)->orderBy('id','DESC')->get(T_USR_ADS);
$ads_list        = "";

foreach ($user_ads as $ad) {
    $ads_list   .= loadPage('ads/list',array(
        'ID' => $ad->id,
        'TYPE' => ($ad->category == 'image') ? 'image' : 'video_library',
        'NAME' => $ad->name,
        'PR_METHOD' => ($ad->type == 1) ? 'Clicks' : 'Views',
        'RESULTS' => getAdAction($ad->id, ($ad->type == 1) ? 'click' : 'view'),//$ad->results,
        'SPENT' => number_format(getAdSpent($ad->id),2),
        'ACTIVE' => (($ad->status == 1) ? 'checked' : ''),
        'CURRENCY'   => $currency,
    ));
}

$countries = '';
foreach ($countries_name as $key => $value) {
    $selected = ($key == $music->user->country_id) ? 'selected' : '';
    $countries .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
}

$music->site_title = lang("Advertising") . ' | ' . $music->config->title;
$music->site_description = $music->config->description;
$music->site_pagename = "user_ads";
$music->site_content     = loadPage('ads/content',array(
    'CURRENCY'   => $currency,
    'ADS_LIST'   => $ads_list,
    'COUNTRIES' => $countries
));