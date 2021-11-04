<?php
if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
	$song_id = secure($_POST['id']);
	$getSong = songData($song_id);
	if (!empty($getSong)) {
		$create_activity = createActivity([
		    'user_id' => $user->id,
			'type' => 'shared_track',
			'track_id' => $getSong->id,
		]);
		$data['status'] = 200;
		$data['data'] = 'song reposted';
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'song not found'
        ];
	}
}
else{
	$data = [
        'status' => 400,
        'error' => 'id can not be empty'
    ];
}