<?php 
if (IS_LOGGED == false) {
	exit();
}
if (empty($_REQUEST['id'])) {
	exit('No id was sent.');
}

if (!empty($_REQUEST['id'])) {
	if (is_numeric($_REQUEST['id'])) {
		$_REQUEST['id'] = secure($_REQUEST['id']);
		$album = $db->where('id', $_REQUEST['id'])->getOne(T_REVIEWS);

        $songData = songData($album->track_id);


		if (!empty($album)) {
		 	if (isAdmin() || $user->id == $songData->user_id) {
           		$dalbum = $db->where('id', $album->id)->delete(T_REVIEWS);
	           	if ($dalbum) {
	           		$data['status'] = 200;
	           	}
           	}
		}
	}
}

?>