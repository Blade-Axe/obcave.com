<?php  
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

$data['status'] = 400;
if ($option == 'get_request') {
	$html = '';
	$requests = $db->where('artist_id',$music->user->id)->where('approved',0)->get(T_ARTISTS_TAGS);
	$db->where('artist_id',$music->user->id)->update(T_ARTISTS_TAGS,array('seen' => time()));
	if (!empty($requests)) {
		foreach ($requests as $key => $value) {
			$user = userData($value->user_id);
			$song = songData($value->track_id);
			if (!empty($user) && !empty($song)) {
				$html .= loadPage('header/request-list', [
					'USER_DATA' => $user,
					'n_time' => time_Elapsed_String($value->time),
	                'ns_time' => date('c',$value->time),
					'n_text' => lang('Tagged You'),
					'n_url' => $song->url,
					'n_a_url' => 'track/'.$song->audio_id,
					'ID' => $value->id,
				]);
			}
		}
		if (!empty($html)) {
			$data = [
				'status' => 200,
				'html' => $html
			];
		}
	}
}
if ($option == 'get') {
	$countNotSeen = getNotifications('count', false);
	if ($countNotSeen > 0) {
		$getNotifications = getNotifications('fetch', false);
	} else {
		$getNotifications = getNotifications();
	}
	if (!empty($getNotifications)) {
		$html = '';
		foreach ($getNotifications as $key => $notification) {
			$notifierData = userData($notification->notifier_id);
            $notificationtext = ($notification->type == 'admin_notification') ? $notification->text : getNotificationTextFromType($notification->type);

			$html .= loadPage('header/notification-list', [
				'USER_DATA' => $notifierData,
				'n_time' => time_Elapsed_String($notification->time),
                'ns_time' => date('c',$notification->time),
                'n_type' => $notification->type,
				'uri' => $notification->url,
				'n_text' => str_replace('%d',$notification->text, $notificationtext),
				'n_url' => ($notification->type == 'follow_user') ? $notifierData->url : getLink($notification->url),
				'n_a_url' => ($notification->type == 'follow_user') ? $notifierData->username : $notification->url,
			]); 
		}
		if (!empty($html)) {
			$db->where('recipient_id', $user->id)->update(T_NOTIFICATION, ['seen' => time()]);
			$data = [
				'status' => 200,
				'html' => $html
			];
		}
	}
}

if ($option == 'count_unseen') {
	$data = [
		'status' => 200,
		'count' => getNotifications('count', false),
        'msgs' => $db->where('to_id', $user->id)->where('seen', 0)->getValue(T_MESSAGES, "COUNT(*)"),
        'request' => $db->where('artist_id',$music->user->id)->where('seen',0)->getValue(T_ARTISTS_TAGS,'COUNT(*)')
	];
}
?>