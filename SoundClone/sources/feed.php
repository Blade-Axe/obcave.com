<?php 
if (IS_LOGGED == false) {
	header("Location: $site_url");
	exit();
}
$music->feedcount = 0;
$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3,7H9V13H3V7M3,3H21V5H3V3M21,11V13H11V11H21M3,15H17V17H3V15M3,19H21V21H3V19Z" /></svg>' . lang("No activties found") . '</div>';
$getActivties = getActivties(20);
if (!empty($getActivties)) {
	$html = '';
	foreach ($getActivties as $key => $activity) {
		$music->feedcount++;
		$getActivity = getActivity($activity, false);
		$html .= loadPage("user/activity", $getActivity);
	}
}
$is_pro = false;
if (IS_LOGGED === true) {
    if ($user->is_pro == 1 && $music->config->go_pro == 'on') {
        $is_pro = true;
    }
}
$db->where("id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
$artist_sidebar = $db->where('artist', '1')->
where("id NOT IN (SELECT following_id FROM " . T_FOLLOWERS . " WHERE follower_id = '{$music->user->id}') AND id <> '{$music->user->id}'")->get(T_USERS, 8);

$time = strtotime(date('l').", ".date('M')." ".date('d').", ".date('Y'));
if (date('l') == 'Saturday') {
    $start = strtotime(date('M')." ".date('d').", ".date('Y')." 12:00am");
}
else{
    $start = strtotime('last saturday, 12:00am', $time);
}

if (date('l') == 'Friday') {
    $end = strtotime(date('M')." ".date('d').", ".date('Y')." 11:59pm");
}
else{
    $end = strtotime('next Friday, 11:59pm', $time);
}
$sql = 'SELECT 
  '.T_USERS.'.id
FROM
  '.T_VIEWS.'
  INNER JOIN '.T_SONGS.' ON ('.T_VIEWS.'.track_id = '.T_SONGS.'.id)
  INNER JOIN '.T_USERS.' ON ('.T_SONGS.'.user_id = '.T_USERS.'.id)
WHERE
  '.T_USERS.'.artist = 1 AND '.T_VIEWS.'.`time` >= '. $start .' AND '.T_VIEWS.'.`time` <= '. $end .'
GROUP BY
  '.T_USERS.'.id
LIMIT 8';
$artist_sidebar = $db->rawQuery($sql);
$artist_sidebar_html = '';
$artist_sidebar_html_list = '';

if (!empty($artist_sidebar)) {
	foreach ($artist_sidebar as $key => $value) {
		$artist_sidebar_html_list .= loadPage('feed/sidebar_artists_list', ['USER_DATA' => userData($value->id)]);
	}
	$artist_sidebar_html = loadPage('feed/sidebar_artists', ['html' => $artist_sidebar_html_list]);
}

$time_week = time() - 604800;

$query = "SELECT " . T_SONGS . ".*, COUNT(" . T_VIEWS . ".id) AS " . T_VIEWS . "
FROM " . T_SONGS . " LEFT JOIN " . T_VIEWS . " ON " . T_SONGS . ".id = " . T_VIEWS . ".track_id
WHERE " . T_SONGS . ".time > $time_week AND " . T_SONGS . ".availability = '0'";

if (IS_LOGGED) {
	$query .= " AND " . T_SONGS . ".user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)";
}

$query .= " GROUP BY " . T_SONGS . ".id
ORDER BY " . T_VIEWS . " DESC LIMIT 20";

$weekly_sidebar = $db->rawQuery($query);

$weekly_sidebar_html = '';
$weekly_sidebar_html_list = '';

if (!empty($weekly_sidebar)) {
	foreach ($weekly_sidebar as $key => $song) {
		$songData = songData($song, false, false);
		$weekly_sidebar_html_list .= loadPage('feed/sidebar_weekly_list', $songData->songArray);
	}
	$weekly_sidebar_html = loadPage('feed/sidebar_weekly', ['html' => $weekly_sidebar_html_list]);
}

$announcement_html = '';
/* Get active Announcements */
if (IS_LOGGED === true) {
    $announcement          = get_announcments();
    if(!empty($announcement)) {
        $announcement_html =  loadPage("announcements/content",array(
            'ANN_ID'       => $announcement->id,
            'ANN_TEXT'     => htmlspecialchars_decode(str_replace('<br>','',$announcement->text)),
        ));
    }
}
$music->announcement = $announcement_html;
/* Get active Announcements */
$last_ads = 0;
$ads_sys = ($music->config->user_ads == 'on') ? true : false;
if (!empty($_COOKIE['last_ads_seen']) && !$is_pro) {
    if ($_COOKIE['last_ads_seen'] > (time() - 600)) {
        $last_ads = 1;
    }
}
$ad_html = '';

if ($last_ads == 0 && !$is_pro && $ads_sys) {
    $rand = (rand(0, 1)) ? rand(0, 1) : (rand(0, 1) ?: rand(0, 1));

    //if ($rand == 0) {
        $ad_data = get_user_ads(2);

        if (!empty($ad_data)) {
            $user_data      = UserData($ad_data->user_id);
            $_SESSION['pagead'] = $ad_data->id;
            $ad_html   = loadPage('ads/view',array(
                'USERDATA' => $user_data,
                'ADDATA' => $ad_data,
                'type' => 'pagead'
            ));
            if ($ad_data->type == 2 && $ad_data->user_id !== $user->id) {
                register_ad_views($ad_data->id, $ad_data->user_id);
            }
        }

//    }else if($rand == 1){
//
//    }

}

$music->site_title = 'Feed';
$music->site_description = $music->config->description;
$music->site_pagename = "feed";
$music->site_content = loadPage("feed/content", ['html' => $html, 'artist_sidebar' => $artist_sidebar_html, 'weekly_sidebar' => $weekly_sidebar_html,'ANNOUNCEMENT'     => $announcement_html,'ads' => $ad_html]);