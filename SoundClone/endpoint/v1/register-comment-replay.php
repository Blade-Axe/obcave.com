<?php
if (IS_LOGGED == false) {
    $data = array('status' => 400, 'error' => 'Not logged in');
    echo json_encode($data);
    exit();
}


if (empty($_POST['id'])) {
    $data = array('status' => 400, 'error' => 'Invalid Track ID');
    echo json_encode($data);
    exit();
}

if (empty($_POST['value'])) {
    $data = array('status' => 400, 'error' => 'Invalid value');
    echo json_encode($data);
    exit();
}	

$id = secure($_POST['id']);
$db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
$getSong = $db->where('audio_id', $id)->getOne(T_SONGS);
if (empty($getSong)) {
    $data = array('status' => 400, 'error' => 'Invalid Track ID');
    echo json_encode($data);
    exit();
}

$comment_id = 0;
if (!empty($_POST['cid'])) {
    $comment_id = secure($_POST['cid']);
}
if (empty($comment_id)) {
    exit("Invalid Comment ID");
}

$db->where("user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
$getComment = $db->where('id', $comment_id)->getOne(T_COMMENTS);
if (empty($getComment)) {
    exit("Invalid comment ID");
}

$data['status'] = 400;
$songpercentage = 0;
$songseconds = 0;
if (!empty($_POST['timePercentage'])) {
	if (is_numeric($_POST['timePercentage'])) {
		$songpercentage = secure($_POST['timePercentage']);
	}
} 

if (!empty($_POST['time'])) {
	if (is_numeric($_POST['time'])) {
		$songseconds = secure($_POST['time']);
	}
}

$text = '';

if (!empty($_POST['value'])) {
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
	if (!empty($comment)) {
		$create_activity = createActivity([
		    'user_id' => $user->id,
			'type' => 'replay_commented_track',
			'track_id' => $comment->track_id,
		]);
		$songID = songData($comment->track_id);
		$commentUser = userData($comment->user_id);
		$music->comment = $comment;
        if (isset($mentions) && is_array($mentions)) {
            foreach ($mentions as $mention) {
                $create_notification = createNotification([
                    'notifier_id' => $music->user->id,
                    'recipient_id' => $mention,
                    'track_id' => $comment->track_id,
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

		$comment_data = [
			'id' => $comment->id,
			'comment_seconds' => $comment->songseconds,
			'comment_percentage' => $comment->songpercentage,
			'USER_DATA' => $commentUser,
			'comment_text' => $comment_text,
			'comment_posted_time' => $comment->posted,
            'tcomment_posted_time' => date('c',strtotime($comment->posted)),
			'comment_seconds_formatted' => $comment->secondsFormated,
			'comment_song_id' => $songID->audio_id,
            'comment_song_track_id' => $comment->track_id,
		];
		if ($comment_data) {
			$data = [
				'status' => 200,
				'data' => $comment_data
            ];
		}
	}
}
?>