<?php
//http://localhost/deepsound.com/unusual-login?type=two-factor
if (empty($_SESSION['code_id'])) {
    header("Location: $site_url");
    exit();
}
if (!empty($_GET['type'])) {
	if ($_GET['type'] == 'two-factor') {
		$wo['lang']['confirm_your_account'] = $wo['lang']['two_factor'];
		$wo['lang']['sign_in_attempt'] = $wo['lang']['to_log_in_two_factor'];
		if ($music->config->two_factor_type == 'both') {
			$wo['lang']['we_have_sent_you_code'] = $wo['lang']['sent_two_factor_both'];
		} else if ($music->config->two_factor_type == 'email') {
			$wo['lang']['we_have_sent_you_code'] =  $wo['lang']['sent_two_factor_email'];
		} else if ($music->config->two_factor_type == 'phone') {
			$wo['lang']['we_have_sent_you_code'] = $wo['lang']['sent_two_factor_phone'];
		}
	} else {
        header("Location: $site_url");
        exit();
	}
}
$music->site_title = lang("Unusual login");
$music->site_description = '';
$music->site_pagename = "unusual";
$music->site_content = loadPage("home/unusual-login");
