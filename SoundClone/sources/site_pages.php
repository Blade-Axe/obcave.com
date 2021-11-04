<?php
if (empty($path['options'][1])) {
	header("Location: $site_url/404");
	exit();
}

$music->page_data = GetCustomPage($path['options'][1]);
if (empty($music->page_data)) {
	header("Location: $site_url/404");
	exit();
}

$music->site_title = $music->page_data['page_title'] . ' | ' . $music->config->title;
$music->site_description = $music->config->description;
$music->site_pagename = "custom_page";
$music->site_content     = loadPage('custom_page/content');