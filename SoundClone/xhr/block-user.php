<?php  
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

$user_id = 0;
if (!empty($_REQUEST['id'])) {
	if (is_numeric($_REQUEST['id'])) {
		if ($_REQUEST['id'] != $user->id && $_REQUEST['id'] > 0) {
			$user_id = secure($_REQUEST['id']);
        }
    }
}		
if (empty($user_id)) {
	exit("Invalid User ID");
}	

$userData = userData($user_id);

if (empty($userData)) {
	exit("Invalid User");
}	

if ($userData->admin == 1) {
	exit("User is admin, can't be blocked");
}


$data['status'] = 400;
$isBlocked = ($db->where('user_id', $music->user->id)->where('blocked_id', $user_id)->getValue(T_BLOCKS, 'count(*)') > 0);

if ($option == 'block') {
	if (!$isBlocked) {
		$create_block = $db->insert(T_BLOCKS, ['user_id' => $user->id, 'blocked_id' => $user_id, 'time' => time()]);
		if ($create_block) {
			$data['status'] = 200;
		}
	}
}

if ($option == 'unblock') {
	if ($isBlocked) {
		$delete_block = $db->where('user_id', $user->id)->where('blocked_id', $user_id)->delete(T_BLOCKS);
		if ($delete_block) {
			$data['status'] = 200;
		}
	}
}
?>