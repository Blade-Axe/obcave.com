<?php 
if (empty($path['options'][1])) {
	header("Location: $site_url/404");
	exit();
}
$audio_id = secure($path['options'][1]);

if (IS_LOGGED) {
	$db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
}

$getIDAudio = $db->where('audio_id', $audio_id)->getValue(T_SONGS, 'id');

if (empty($getIDAudio)) {
	header("Location: $site_url/404");
	exit();
}

$songData = songData($getIDAudio);

$music->stream_exist = $stream_exist  = false;
if($songData->src === 'radio') {
    $music->stream_exist = CheckStreamUrl($songData->audio_location);
}

$songData->owner  = false;

if (IS_LOGGED == true) {
    $songData->owner  = ($user->id == $songData->publisher->id) ? true : false;
}

$music->albumData = [];
if (!empty($songData->album_id)) {
	$music->albumData = $db->where('id', $songData->album_id)->getOne(T_ALBUMS);
}
$isPurchased = isTrackPurchased($songData->id);

$can_download = false;
$notPurchased = false;

if( $music->config->who_can_download == 'pro' ){
    if(empty($songData->price)){
        $isPurchased = true;
    }
    if (IS_LOGGED && $user->is_pro == 1 && $isPurchased) {
        $can_download = true;
    }else{
        $can_download = false;
    }
}
else{
	if (IS_LOGGED) {
		$can_download = true;
	}
}

//if (IS_LOGGED) {
//	if($songData->owner == true || isAdmin()) {
//		$can_download = true;
// 	}
// 	if ($songData->price > 0) {
// 		if ($isPurchased) {
//	 		$can_download = true;
//	 	} else {
//	 		$notPurchased = true;
//	 	}
// 	}
// 	if ($music->config->go_pro == 'on') {
// 		if ($user->is_pro == 1 && $isPurchased) {
// 			$can_download = true;
// 		}
// 	} else if ($notPurchased == false) {
// 		$can_download = true;
// 	}
//}
//
//if( $music->config->who_can_download == 'pro' && $user->is_pro == 1 && $isPurchased ){
//    $can_download = true;
//}
//
//if( $music->config->who_can_download == 'free' && $user->is_pro == 0 && $isPurchased ){
//    $can_download = true;
//}
//
//if( $music->config->who_can_download == 'free' && $user->is_pro == 0 && !$isPurchased ){
//    $can_download = false;
//}
//
//if( $music->songData->allow_downloads == 0){
//    $can_download = false;
//}

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
        if ($songData->owner == true || isAdmin()) {
            $can_download = true;
        }
    }
}



$music->can_download = $can_download;
$music->songData = $songData;

$getSongComments = $db->where('track_id', $songData->id)->orderBy('id', 'DESC')->get(T_COMMENTS, 30);
$music->comment_count = 0;
$comment_html = '';
$comments_on_wave = '';
if (!empty($getSongComments)) {
	foreach ($getSongComments as $key => $comment) {
        $music->comment_count++;
		$comment = getComment($comment, false);
		$commentUser = userData($comment->user_id);
		$music->comment = $comment;

		$comment_text = $comment->value;
        $mention_regex = '/@\[([0-9]+)\]/i';
        if (preg_match_all($mention_regex, $comment_text, $matches)) {
            foreach ($matches[1] as $match) {
                $match = secure($match);
                $match_user = userData($match);
                $match_search = '@[' . $match . ']';
                if (isset($match_user->id)) {
                    $match_replace = '<a href="' . $music->config->site_url . '/' . $match_user->username . '" data-load="'.$match_user->username.'">@' . $match_user->username . '</a>';
                    $comment_text = str_replace($match_search, $match_replace, $comment_text);
                }
            }
        }
		$comment_html .= loadPage('track/comment-list', [
			'comment_id' => $comment->id,
			'comment_seconds' => $comment->songseconds,
			'comment_percentage' => $comment->songpercentage,
			'USER_DATA' => $commentUser,
			'comment_text' => $comment_text,
			'comment_posted_time' => $comment->org_posted,
            'tcomment_posted_time' => date('c',$comment->org_posted),
			'comment_seconds_formatted' => $comment->secondsFormated,
			'comment_song_id' => $songData->audio_id,
            'comment_song_track_id' => $comment->track_id,
		]);
		$comments_on_wave .= '<div class="comment-on-wave" style="left: ' . ($comment->songpercentage * 100). '%;"><img src="' . $commentUser->avatar . '"><div class="comment-on-wave-data"><div><span class="comment-on-wave-time">' . $comment->secondsFormated . '</span><p>' . $comment->value . '</p></div></div></div>';
	}
} else {
	$comment_html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z" /></svg>' . lang("No comments found") . '</div>';
}


$related_tracks = $db->where('category_id', $songData->category_id)->where('id', $songData->id, '<>')->orderBy('RAND()')->get(T_SONGS, 10);
if (empty($related_tracks)) {
	$related_tracks = $db->orderBy('RAND()')->where('id', $songData->id, '<>')->get(T_SONGS, 10);
}

$related_tracks_html = '';
if (!empty($related_tracks)) {
	foreach ($related_tracks as $key => $related_track) {
		$related_track = songData($related_track, true, false);
		if (!empty($related_track)) {
			$music->related_track = $related_track;
			$related_tracks_html .= loadPage('track/related', [
				'song_id' => $related_track->id,
				'song_audio_id' => $related_track->audio_id,
				'song_title' => $related_track->title,
				'USER_DATA' => $related_track->publisher,
				'song_thumbnail' => $related_track->thumbnail,
			]);
		}
			
	}
}

$recentPlays = $db->where('track_id', $songData->id)->where('user_id', '0', '<>')->orderBy('id', 'DESC')->get(T_VIEWS, 10);

$recentUserPlays_html = '';
if (!empty($recentPlays)) {
	foreach ($recentPlays as $key => $recentPlay) {
		$recentPlay = userData($recentPlay->user_id);
		$recentUserPlays_html .= loadPage('feed/sidebar_artists_list', [
			'USER_DATA' => $recentPlay,
		]);
	}
}


$autoPlay = false;
if (!empty($path['options'][2])) {
	if ($path['options'][2] == 'play') {
		$autoPlay = true;
	}
}
$is_pro = false;
if (IS_LOGGED === true) {
    if ($user->is_pro == 1 && $music->config->go_pro == 'on') {
        $is_pro = true;
    }
}

$ads_sys = ($music->config->user_ads == 'on') ? true : false;
if (!empty($_COOKIE['last_ads_seen']) && !$is_pro) {
    if ($_COOKIE['last_ads_seen'] > (time() - 600)) {
        $last_ads = 1;
    }
}
$ad_html = '';
$last_ads = 0;
$music->audio_ad_id = 0;
if ($last_ads == 0 && !$is_pro && $ads_sys) {
    //$rand = (rand(0, 1)) ? rand(0, 1) : (rand(0, 1) ?: rand(0, 1));

    //if ($rand == 0) {
        $ad_data = get_user_ads(1);

        if (!empty($ad_data)) {
            $user_data      = UserData($ad_data->user_id);
            $ad_html   = loadPage('ads/view',array(
                'USERDATA' => $user_data,
                'ADDATA' => $ad_data,
                'type' => 'track'
            ));
            if($ad_data->type == '2' && $ad_data->user_id !== $user->id){
                register_ad_views($ad_data->id, $ad_data->user_id);
            }
        }

    //}else if($rand == 1){

    //}
        if ($music->config->audio_ads == 'on') {
        	$audio_ad = get_user_ads(1,'audio');
	        if (!empty($audio_ad)) {
	        	$music->audio_ad_id = $audio_ad->id;
	        }
        }
	        

}

$total_rate = 0;
$total_review = 0;
$reviews = $db->objectbuilder()->where('track_id',$songData->id)->orderBy('id', 'DESC')->get(T_REVIEWS);
if (!empty($reviews)) {
    foreach ($reviews as $key => $value) {
        $total_review++;
        $total_rate += $value->rate;
    }
}
$music->rate_avg = 0;
if ($total_review > 0) {
	$music->rate_avg = $total_rate / $total_review;
}

$music->total_review = $total_review;

$t_desc = $songData->description;
$music->isPurchased = $isPurchased;
$music->autoPlay = $autoPlay;
$music->site_title = html_entity_decode($songData->title . ' - ' . $music->config->title);
$music->site_description =  $songData->description;
$music->site_pagename = "track";
$music->site_content = loadPage("track/content", [
	'USER_DATA' => $songData->publisher,
	't_thumbnail' => $songData->thumbnail,
	't_song' => $songData->secure_url,
	't_title' => $songData->title,
	't_description' => $t_desc,
	't_lyrics' => $songData->lyrics,
	't_time' => time_Elapsed_String($songData->time),
    'ts_time' => date('c',$songData->time),
	't_audio_id' => $songData->audio_id,
	't_id' => $songData->id,
	't_price' => $songData->price,
	'category_name' => $songData->category_name,
	't_shares' => number_format_mm($songData->shares),
	'COUNT_LIKES' => number_format_mm(countLikes($songData->id)),
    'COUNT_DISLIKES' => number_format_mm(countDisLikes($songData->id)),
	'COUNT_VIEWS' => number_format_mm($db->where('track_id', $songData->id)->getValue(T_VIEWS, 'count(*)')),
	'COUNT_USER_SONGS' => $db->where('user_id', $songData->publisher->id)->getValue(T_SONGS, 'count(*)'),
	'COUNT_USER_FOLLOWERS' => number_format_mm($db->where('following_id', $songData->publisher->id)->getValue(T_FOLLOWERS, 'COUNT(*)')),
	'comment_count' => number_format_mm($db->where('track_id', $songData->id)->getValue(T_COMMENTS, 'count(*)')),
	'fav_count' => number_format_mm($db->where('track_id', $songData->id)->getValue(T_FOV, 'count(*)')),
    'purchase_count' => number_format_mm($db->where('track_id', $songData->id)->getValue(T_PURCHAES, 'count(*)')),
	'comment_list' => $comment_html,
	'comments_on_wave' => $comments_on_wave,
	'related_tracks' => $related_tracks_html,
	'recentPlays' => $recentUserPlays_html,
    'ads' => $ad_html,
    'stream_exist' => $stream_exist,
    't_src' => $songData->src
]);
