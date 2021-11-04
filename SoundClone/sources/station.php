<?php
if (empty($path['options'][1]) || $music->config->radio_station_import == 'off' ) {
    header("Location: $site_url/404");
    exit();
}
$station_id = secure($path['options'][1]);

if (IS_LOGGED) {
    $db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
}

$stationData = $db->where('id', $station_id)->get(T_STATIONS, null, array('*'));

if (empty($stationData)) {
    header("Location: $site_url/404");
    exit();
}
$stationData = $stationData[0];
$stationData->owner  = (IS_LOGGED == true) ? ($user->id == $stationData->user_id) : false;


$stationData->userData = userData($stationData->user_id);


$music->site_title = html_entity_decode( $stationData->station . ' - ' . $music->config->title);
$music->site_description =  $music->config->description;
$music->site_pagename = "station";
$music->site_content = loadPage("station/content", [
    'USER_DATA' => $stationData->userData,
    't_thumbnail' => $stationData->logo,
    't_song' => $stationData->url,
    't_title' => $stationData->station,
    't_time' => time_Elapsed_String($stationData->created_at),
    'genre' => $stationData->genre,
    'country' => $stationData->country,
    'COUNT_USER_SONGS' => $db->where('user_id', $songData->publisher->id)->getValue(T_SONGS, 'count(*)'),
    'COUNT_USER_FOLLOWERS' => number_format_mm($db->where('following_id', $songData->publisher->id)->getValue(T_FOLLOWERS, 'COUNT(*)')),
]);
