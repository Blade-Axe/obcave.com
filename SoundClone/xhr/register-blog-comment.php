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
	exit("Invalid Article ID");
}	

if (empty(trim($_POST['value']))) {
	exit("Invalid value");
}	

$id = secure($_POST['id']);
$getSong = $db->where('id', $_POST['id'])->getOne(T_BLOG);
if (empty($getSong)) {
	exit("Invalid Article ID");
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
	$text = secure($_POST['value']);
}


$final_array = [
	'user_id' => $user->id,
	'article_id' => $getSong->id,
	'value' => $text,
	'time' => time()
];


$insert = $db->insert(T_BLOG_COMMENTS, $final_array);

if ($insert) {
	$comment = getBlogComment($insert);
	if (!empty($comment)) {
		$commentUser = userData($comment->user_id);
		$music->comment = $comment;
		$html = loadPage('blogs/comment-list', [
			'comment_id' => $comment->id,
			'USER_DATA' => $commentUser,
			'comment_text' => $comment->value,
			'comment_posted_time' => $comment->time,
            'tcomment_posted_time' => date('c',$final_array['time']),
		]);
		if ($html) {
			$data = [
				'status' => 200,
				'html' => $html
			];
		}
	}
}
?>