<?php
if (empty($path['options'][1])) {
    header("Location: $site_url/404");
    exit();
}
$audio_id = secure($path['options'][1]);

$getIDAudio = $db->where('audio_id', $audio_id)->getValue(T_SONGS, 'id');

if (empty($getIDAudio)) {
    header("Location: $site_url/404");
    exit();
}

$songData = songData($getIDAudio);
//if ($songData->IsOwner == true || IsAdmin()) {
//
//}else{
//    header("Location: $site_url");
//    exit();
//}

$songData->owner  = false;

if (IS_LOGGED == true) {
    $songData->owner  = ($user->id == $songData->publisher->id) ? true : false;
}

$total_rate = 0;
$total_review = 0;
$reviews_html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M15,6H3V8H15V6M15,10H3V12H15V10M3,16H11V14H3V16M17,6V14.18C16.69,14.07 16.35,14 16,14A3,3 0 0,0 13,17A3,3 0 0,0 16,20A3,3 0 0,0 19,17V8H22V6H17Z" /></svg>' . lang("No reviews on this track yet.") . '</div>';
$reviews = $db->objectbuilder()->where('track_id',$songData->id)->orderBy('id', 'DESC')->get(T_REVIEWS);
if (!empty($reviews)) {
    $reviews_html = '';
    foreach ($reviews as $key => $value) {
        $user = userData($value->user_id);
        $reviews_html .= loadPage('track/review', ['SONG_DATA' => $songData, 'USER_DATA' => $user, 'RATE' => $value->rate, 'TM' => $value->time, 'DESC' => $value->description, 'ID' => $value->id]);
        $total_review++;
        $total_rate += $value->rate;
    }
}
$music->rate_avg = $total_rate / $total_review;

$music->albumData = [];
if (!empty($songData->album_id)) {
    $music->albumData = $db->where('id', $songData->album_id)->getOne(T_ALBUMS);
}
$isPurchased = isTrackPurchased($songData->id);

$can_download = false;
$notPurchased = false;

if( $music->config->who_can_download == 'pro' ){
    if ($user->is_pro == 1 && $isPurchased) {
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
        $comment_html .= loadPage('track/comment-list', [
            'comment_id' => $comment->id,
            'comment_seconds' => $comment->songseconds,
            'comment_percentage' => $comment->songpercentage,
            'USER_DATA' => $commentUser,
            'comment_text' => $comment->value,
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


$autoPlay = false;
if (!empty($path['options'][2])) {
    if ($path['options'][2] == 'play') {
        $autoPlay = true;
    }
}

if (IS_LOGGED === true) {
    if ($user->is_pro == 1 && $music->config->go_pro == 'on') {
        $is_pro = true;
    }
}



$t_desc = $songData->description;
$music->isPurchased = $isPurchased;
$music->autoPlay = $autoPlay;



$music->site_title = html_entity_decode($songData->title);
$music->site_description = $songData->description;
$music->site_pagename = "track_reviews";
$music->site_content = loadPage("track/reviews", [
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
    'reviews_html' => $reviews_html,
    'total_review' => $total_review
]);