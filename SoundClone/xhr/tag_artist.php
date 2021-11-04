<?php
if ($option == 'tag') {
	if (!empty($_POST['key']) && $music->config->tag_artist_system == 1) {
		$name  = Secure($_POST['key']);
		$logged_user_id = $music->user->id;
	    $sql = "SELECT id FROM `".T_USERS."` WHERE `active` = '1' AND `artist` != 0 AND ((`username` LIKE '%" . $name . "%') OR (`name` LIKE '%" . $name . "%')) ";
	    $sql .= "AND `id` NOT IN (SELECT `user_id` FROM `" . T_BLOCKS . "` WHERE `blocked_id` = '{$logged_user_id}') ";
	    $sql .= "AND `id` NOT IN (SELECT `blocked_id` FROM `" . T_BLOCKS . "` WHERE `user_id` = '{$logged_user_id}') ";
	    $sql .= "AND (`id` IN (SELECT `following_id` FROM `" . T_FOLLOWERS . "` WHERE `follower_id` = {$logged_user_id} AND `following_id` <> {$logged_user_id} AND `active` = '1') ";
	    $sql .= "OR `id` IN (SELECT `follower_id` FROM `" . T_FOLLOWERS . "` WHERE `follower_id` <> {$logged_user_id} AND `following_id` = {$logged_user_id} AND `active` = '1')) LIMIT 5;";
	    $data = array();
	    $query_get = mysqli_query($sqlConnect,$sql);
	    if (mysqli_num_rows($query_get) > 0) {
	    	$html  = "";
	        while ($tag_user = mysqli_fetch_assoc($query_get)) {
	        	$music->tag_user = userData($tag_user['id']);
	        	$html .= LoadPage('upload-song/artist_list');
	        }
	        $data['html'] = $html;
	    }
	}
}
if ($option == 'approve') {
	$data['status'] = 400;
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$data['status'] = 200;
		$db->where('id',Secure($_POST['id']))->where('artist_id',$music->user->id)->update(T_ARTISTS_TAGS,array('approved' => 1));
	}
}
if ($option == 'decline') {
	$data['status'] = 400;
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$data['status'] = 200;
		$db->where('id',Secure($_POST['id']))->where('artist_id',$music->user->id)->delete(T_ARTISTS_TAGS);
	}
}
	
        