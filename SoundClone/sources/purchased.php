<?php 
if (IS_LOGGED == false) {
	header("Location: $site_url");
	exit();
}

$html = "<div class='no-songs-found text-center'>" . lang("No purchased tracks found") . "</div>";

$getPurchased = $db->where('user_id', $user->id)->orderBy('time', 'DESC')->get(T_PURCHAES, 20);

$can_download = false;
$notPurchased = false;

if (!empty($getPurchased)) {
	$html = '';
	foreach ($getPurchased as $key => $song) {
		$songData = songData($song->track_id);
		$music->songData = $songData;

        $isPurchased = isTrackPurchased($songData->id);

//        if (IS_LOGGED) {
//            if($songData->owner == true || isAdmin()) {
//                $can_download = true;
//            }
//            if ($songData->price > 0) {
//                if ($isPurchased) {
//                    $can_download = true;
//                } else {
//                    $notPurchased = true;
//                }
//            }
//            if ($music->config->go_pro == 'on') {
//                if ($user->is_pro == 1 && $isPurchased) {
//                    $can_download = true;
//                }
//            } else if ($notPurchased == false) {
//                $can_download = true;
//            }
//        }
        if (IS_LOGGED) {
            if ($songData->price > 0) {
                if ($music->config->who_can_download == 'pro' && $user->is_pro == 1 && $isPurchased) {
                    $can_download = true;
                }
                if ($music->config->who_can_download == 'pro' && $user->is_pro == 0 && $isPurchased) {
                    $can_download = true;
                }
                if ($music->config->who_can_download == 'free' && $user->is_pro == 0 && $isPurchased) {
                    $can_download = true;
                }
                if ($music->config->who_can_download == 'free' && $user->is_pro == 0 && !$isPurchased) {
                    $can_download = false;
                }
                if ($songData->IsOwner == true || isAdmin()) {
                    $can_download = true;
                }
            }
        }

        $music->can_download = $can_download;

		$html .= loadPage('purchased/list', [
			't_thumbnail' => $songData->thumbnail,
			't_id' => $songData->id,
			't_title' => $songData->title,
			't_artist' => $songData->publisher->name,
			't_url' => $songData->url,
			't_artist_url' => $songData->publisher->url,
			't_audio_id' => $songData->audio_id,
			't_time' => $song->time,
			't_price' => $song->price,
			't_duration' => $songData->duration,
			't_purchased_on' => date('m/d/Y', strtotime($song->timestamp)),
			't_key' => ($key + 1)
		]);
	}
}
$music->site_title = lang("Purchased Songs");
$music->site_description = $music->config->description;
$music->site_pagename = "purchased";
$music->site_content = loadPage("purchased/content", ['html' => $html]);
