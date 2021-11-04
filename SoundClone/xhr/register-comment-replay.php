<?php  
if (IS_LOGGED == false) {
    $data = array(
        'status' => 300,
        'error' => 'You ain\'t logged in!'
    );
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

$track_id = 0;
if (!empty($_POST['id'])) {
	$track_id = secure($_POST['id']);
}		
if (empty($track_id)) {
	exit("Invalid Track ID");
}

$comment_id = 0;
if (!empty($_POST['cid'])) {
    $comment_id = secure($_POST['cid']);
}
if (empty($comment_id)) {
    exit("Invalid Comment ID");
}

if (empty(trim($_POST['value']))) {
	exit("Invalid value");
}	

$id = secure($_POST['id']);
$db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
$getSong = $db->where('audio_id', $track_id)->getOne(T_SONGS);
if (empty($getSong)) {
	exit("Invalid Track ID");
}


$db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
$getComment = $db->where('id', $comment_id)->getOne(T_COMMENTS);
if (empty($getComment)) {
    exit("Invalid comment ID");
}

$data['status'] = 400;
$text = '';

if (!empty($_POST['value'])) {
    if ($music->config->maxCharacters != 10000) {
        if ((mb_strlen($_POST['value']) - 10) > $music->config->maxCharacters) {
            exit("Text Lenght");
        }
    }
	$link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
    $i          = 0;
    preg_match_all($link_regex, secure($_POST['value']), $matches);
    foreach ($matches[0] as $match) {
        $match_url            = strip_tags($match);
        $syntax               = '[a]' . urlencode($match_url) . '[/a]';
        $_POST['value'] = str_replace($match, $syntax, $_POST['value']);
    }
    $mentions = array();
    $mention_regex = '/@([A-Za-z0-9_]+)/i';
    preg_match_all($mention_regex, secure($_POST['value']), $matches);
    foreach ($matches[1] as $match) {
        $match         = secure($match);
        $get_user_id = $db->where('username', secure($match))->getValue(T_USERS, 'id');
        if (!empty($get_user_id)) {
            $match_user    = userData($get_user_id);
            $match_search  = '@' . $match;
            $match_replace = '@[' . $match_user->id . ']';
            if (isset($match_user->id)) {
                $_POST['value'] = str_replace($match_search, $match_replace, $_POST['value']);
                $mentions[]   = $match_user->id;
            }
        }
    }
	$text = secure($_POST['value']);
}


$final_array = [
	'user_id' => $user->id,
	'comment_id' => $comment_id,
	'value' => $text,
	'time' => time()
];


$insert = $db->insert(T_COMMENT_REPLIES, $final_array);

if ($insert) {
	$comment = getCommentReplay($insert);
    $main_comment  = getComment($comment->comment_id);
	if (!empty($comment)) {
		$create_activity = createActivity([
		    'user_id' => $user->id,
			'type' => 'replay_commented_track',
			'track_id' => $main_comment->track_id,
		]);
		$songID = songData($main_comment->track_id);
		$commentUser = userData($comment->user_id);
		$music->comment = $comment;

        if (isset($mentions) && is_array($mentions)) {
            foreach ($mentions as $mention) {
                $create_notification = createNotification([
                    'notifier_id' => $music->user->id,
                    'recipient_id' => $mention,
                    'track_id' => $main_comment->track_id,
                    'comment_id' => $comment->id,
                    'type' => 'comment_mention',
                    'url' => 'track/'.$songID->audio_id
                ]);
            }
        }

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

		$html = loadPage('track/comment-replay-list', [
			'comment_id' => $comment->id,
			'comment_seconds' => 0,
			'comment_percentage' => 0,
			'USER_DATA' => $commentUser,
			'comment_text' => $comment_text,
			'comment_posted_time' => $comment->posted,
            'tcomment_posted_time' => date('c',$final_array['time']),
			'comment_seconds_formatted' => $main_comment->secondsFormated,
			'comment_song_id' => $songID->audio_id,
            'comment_song_track_id' => $main_comment->track_id,
            'owner' => $comment->owner
		]);
		if ($html) {
            $final_array['track_user_id'] = $getSong->user_id;
            $final_array['comment_user_id'] = $comment->user_id;
            RecordUserActivities('replay_comment', $final_array);
			$data = [
				'status' => 200,
				'html' => $html,
				'comment_wave' => ''
			];
		}
	}
}
?>