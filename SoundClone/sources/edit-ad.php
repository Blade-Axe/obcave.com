<?php
$ad_id = secure($path['options'][1]);

if (!IS_LOGGED || $music->config->user_ads != 'on' || empty($ad_id) || !is_numeric($ad_id)) {
    header('Location: ' . $site_url);
    exit;
}

$ad_data          = $db->where('id',$ad_id)->where('user_id',$user->id)->getOne(T_USR_ADS);

if (empty($ad_data)) {
    header('Location: ' . $site_url);
    exit;
}
$music->ad           = $ad_data;
$payment_currency = $music->config->currency;
$currency         = "";
if ($payment_currency == "USD") {
    $currency     = "$";
}
else if($payment_currency == "EUR"){
    $currency     = "€";
}
$music->audience    = @explode(',', $ad_data->audience);
$music->audience    = (is_array($music->audience) === true) ? $music->audience : array();
$music->title       = 'Edit Advertising | ' . $music->config->title;
$music->page        = "user_ads";

$music->site_title = 'Edit Advertising | ' . $music->config->title;
$music->description = $music->config->description;
$music->site_description = $music->config->description;
$music->site_pagename = "edit-ads";
$music->keyword     = @$music->config->keyword;
$music->site_content     = LoadPage('edit-ads/content',array(
    'CURRENCY'   => $currency,
    'ID'         => $ad_data->id,
    'NAME'       => $ad_data->name,
    'URL'        => urldecode($ad_data->url),
    'TITLE'      => $ad_data->headline,
    'DESC'       => $ad_data->description,
    'DAY_LIMIT'  => $ad_data->day_limit
));