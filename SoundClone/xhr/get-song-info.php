<?php

$track_id = 0;
if (!empty($_GET['id'])) {
    $track_id = secure($_GET['id']);
}
if (empty($track_id)) {
    exit("Invalid Track ID");
}

$id = secure($_GET['id']);
$getSong = $db->where('audio_id', $_GET['id'])->getOne(T_SONGS);
if (empty($getSong)) {
    exit("Invalid Track ID");
}

$data['status'] = 400;

$getSong = songData($getSong->id);

if (empty($_POST['components'])) {
    $_POST['components'] = sha1(time());
}

$fingerPrint = sha1(json_encode($_POST['components']));

if (IS_LOGGED) {
    $db->where('track_id', $getSong->id)->where('user_id', $user->id)->delete(T_VIEWS);
}

$db->where('fingerprint', $fingerPrint)->where('track_id', $getSong->id);

if (IS_LOGGED) {
    $db->where('user_id', $user->id, '<>');
}
$checkIfViewExits = $db->getValue(T_VIEWS, 'count(*)');
if (empty($checkIfViewExits)) {
    $insertArray = [
        'fingerprint' => secure($fingerPrint),
        'track_id' => $getSong->id,
        'time' => time()
    ];
    if (IS_LOGGED) {
        $insertArray['user_id'] = $user->id;
    }
    if (!empty($getSong->album_id)) {
        $insertArray['album_id'] = $getSong->album_id;
    }
    $addFingerPrint = $db->insert(T_VIEWS, $insertArray);
} else {
    if (IS_LOGGED) {
        $updateArray = [
            'user_id' => $user->id,
            'time' => time()
        ];
        if (!empty($getSong->album_id)) {
            $updateArray['album_id'] = $getSong->album_id;
        }
        $db->where('fingerprint', $fingerPrint)->where('track_id', $getSong->id)->update(T_VIEWS, $updateArray);
    }
}




$time_seconds = formatSeconds($getSong->duration);
$waves = '';


$dark = $getSong->dark_wave;
$light = $getSong->light_wave;
$bar = '#363636';
$opacity = '';

if( $music->config->ffmpeg_system == 'off'){
    $dark = $getSong->light_wave;
    $light = $getSong->dark_wave;

    if($_COOKIE['mode'] == 'day'){
        $bar = 'rgb(191, 191, 191)';
        $opacity = 'opacity: 0.5;';
        if($getSong->ffmpeg == 0){
            $dark = $getSong->light_wave;
            $light = $getSong->dark_wave;
        }else{
            $dark = $getSong->dark_wave;
            $light = $getSong->light_wave;
        }

    }else{
        $opacity = '';
        if($getSong->ffmpeg == 0){
            $dark = $getSong->light_wave;
            $light = $getSong->dark_wave;
        }else{
            $dark = $getSong->dark_wave;
            $light = $getSong->light_wave;
        }

    }
}else{
    $dark = $getSong->dark_wave;
    $light = $getSong->light_wave;

    if($_COOKIE['mode'] == 'day'){
        $dark = str_replace('_dark.png','_day.png',$getSong->dark_wave);
        if(!file_exists( $dark ) ){
            $dark = $getSong->light_wave;
            $light = $getSong->dark_wave;
        }
        $bar = 'rgb(191, 191, 191)';
    }else{
        $dark = str_replace('_day.png','_dark.png',$getSong->dark_wave);

        if($getSong->ffmpeg == 0){
            $dark = $getSong->light_wave;
            $light = $getSong->dark_wave;
        }else{
            $dark = $getSong->dark_wave;
            $light = $getSong->light_wave;
        }

    }

}

$rl = 'left: 0;border-left: inherit!important;border-right: 1px solid '.$bar.' !important;';
if ( $music->language_type == 'rtl' ){
    $rl = 'right: 0;border-right: inherit!important;border-left: 1px solid '.$bar.' !important;';
}

if (!empty($getSong->dark_wave) && !empty($getSong->light_wave)) {
    $waves = '
	<div id="waveform" style="width: 100% !important;" data-id="' . $getSong->audio_id . '">
			<div class="images" style="width: 100%" id="dark-waves">
				<img src="' . getMedia($dark) . '" style="width: 100%;" id="dark-wave">
				<div class="comment-waves "></div>
				<div style="width: 0%; z-index: 111; position: absolute; overflow: hidden; top: 0; <?php echo $rl;?> " id="light-wave">
					<img src="' . getMedia($light) . '">
				</div>
			</div>
	</div>';
}

$getSongComments = $db->where('track_id', $getSong->id)->orderBy('id', 'DESC')->get(T_COMMENTS, 10);
$comment_html = '';
$comments_on_wave = '';
if (!empty($getSongComments)) {
    foreach ($getSongComments as $key => $comment) {
        $comment = $music->comment = getComment($comment, false);
        $commentUser = userData($comment->user_id);

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
            'comment_song_id' => $getSong->audio_id,
            'comment_song_track_id' => $comment->track_id,
        ]);
        $comments_on_wave .= '<div class="comment-on-wave small-waves-icons" style="left: ' . ($comment->songpercentage * 100). '%;"><img src="' . $commentUser->avatar . '"><div class="comment-on-wave-data"><div><span class="comment-on-wave-time">' . $comment->secondsFormated . '</span><p>' . $comment->value . '</p></div></div></div>';
    }
}

$purchase = 'false';

if ($getSong->price > 0) {
    if (!isTrackPurchased($getSong->id)) {
        $purchase = 'true';
        if (IS_LOGGED == true) {
            if ($user->id == $getSong->user_id) {
                $purchase = 'false';
            }
        }
    }
}
$data_load = '';
$is_ad = '';
if (!empty($_GET['audio_ad_id'])) {
    $ad = $db->where('id',secure($_GET['audio_ad_id']))->getOne(T_USR_ADS);
    if (!empty($ad)) {
        $getSong->src = '';
        $getSong->secure_url = getMedia($ad->audio_media);
        $getSong->url = $music->config->site_url . '/redirect/'.$ad->id.'?type=track';
        $getSong->title = $ad->name;
        $data_load = '/redirect/'.$ad->id.'?type=track';
        $is_ad = 'yes';
        if (IS_LOGGED == true) {
            if($ad->user_id !== $user->id){
                register_ad_views($ad->id, $ad->user_id);
            }
        }
        else{
            register_ad_views($ad->id, $ad->user_id);
        }
            
    }
}

$data = [
    'status' => 200,
    'songTitle' => $getSong->title,
    'artistName' => $getSong->publisher->name,
    'albumName' => 'Album',
    'songURL' => ($getSong->src == 'radio') ? $getSong->audio_location : $getSong->secure_url,
    'coverURL' => $getSong->thumbnail,
    'songID' => $getSong->id,
    'songAudioID' => $getSong->audio_id,
    'songPageURL' => $getSong->url,
    'duration' => $time_seconds,
    'songDuration' => $getSong->duration,
    'songWaves' => $waves,
    'comments' => $comment_html,
    'waves' => $comments_on_wave,
    'purchase' => $purchase,
    'price' => $getSong->price,
    'favorite_button' => getFavButton($getSong->id, 'fav-icon'),
    'is_favoriated' => isFavorated($getSong->id),
    'age' => false,
    'data_load' => $data_load,
    'showDemo' => (!empty($getSong->price) && $music->config->ffmpeg_system == 'on' && !empty($getSong->demo_track) && !isTrackPurchased($getSong->id)) ? 'true' : 'false',
    'is_ad' => $is_ad
];
$age = false;
if ($getSong->age_restriction == 1) {
    if (!IS_LOGGED) {
        $age = true;
    } else {
        if ($user->age < 18) {
            $age = true;
        }
    }
}

if ($age == true) {
    $data = ['status' => 200, 'age' => true];
}

?>