<?php
if ($music->config->fame_system != 1) {
    header("Location: $site_url/404");
    exit();
}
$result = $db->rawQuery('SELECT v.id,v.track_id , COUNT(*) AS count FROM '.T_VIEWS.' v  , '.T_SONGS.' s WHERE s.id = v.track_id GROUP BY s.user_id HAVING count >= '.$music->config->views_count.' ORDER BY count DESC LIMIT 10');

$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>' . lang('No artists found.') . '</div>';
$music->fame = false;
if (!empty($result)) {
	$music->fame = true;
	$html = '';
	foreach ($result as $key => $value) {
		$song = songData($value->track_id);
		if (!empty($song)) {
			$data = [
	            'ARTIST_DATA' => userData($song->user_id),
	            'VIEWS' => $value->count,
	            'VIEWS_FORMAT' => number_format($value->count),
	            'ID' => $value->id
	        ];
	        $html .= loadPage("fame/list", $data);
		}
	}
}

$music->fame_data = $result;
$music->site_title = html_entity_decode( lang('Hall of fame') . ' - ' . $music->config->title);
$music->site_description =  $music->config->description;
$music->site_pagename = "fame";
$music->site_content = loadPage("fame/content", [
    'DATA' => $html
]);
