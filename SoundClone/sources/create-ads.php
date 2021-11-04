<?php
if (!IS_LOGGED || $music->config->user_ads != 'on') {
    header("Location: $site_url/404");
    exit;
}

$payment_currency = $music->config->currency;
//$currency         = !empty($music->config->currency_symbol_array[$music->config->currency]) ? $music->config->currency_symbol_array[$music->config->currency] : '$';
$currency = $music->config->currency_symbol;

$music->site_title = lang("Create Advertising") . ' | ' . $music->config->title;
$music->site_description = $music->config->description;
$music->site_pagename = "user_ads";
$music->site_content     = loadPage('create-ads/content',array(
    'CURRENCY'   => $currency
));