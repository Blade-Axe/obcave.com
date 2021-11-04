<?php 
if ($option == 'get') {
	$user_sessions  = $db->arrayBuilder()->where('user_id',$user->id)->orderBy('time', 'DESC')->get(T_APP_SESSIONS);
	$info = array();
    foreach ($user_sessions as $session) {
        $session['browser'] = 'Unknown';
        $session['time'] = time_Elapsed_String($session['time']);
        $session['platform'] = ucfirst($session['platform']);
        $session['ip_address'] = '';
        if ($session['platform'] == 'web' || $session['platform'] == 'windows') {
            $session['platform'] = 'Unknown';
        }
        if ($session['platform'] == 'Phone') {
            $session['browser'] = 'Mobile';
        }
        if ($session['platform'] == 'Windows') {
            $session['browser'] = 'Desktop Application';
        }
        if (!empty($session['platform_details'])) {
            $uns = unserialize($session['platform_details']);
            $session['browser'] = $uns['name'];
            $session['platform'] = ucfirst($uns['platform']);
            $session['ip_address'] = $uns['ip_address'];
        }
        $info[] = $session;
    }
    $data = array(
        'status' => 200,
        'data' => $info
    );
}
if ($option == 'delete') {
	if (empty($_POST['id'])) {
        $data = [
                'status' => 400,
                'error' => 'id can not be empty'
            ];
    }
    else{
    	$id = secure($_POST['id']);
    	$check_session = $db->where('id', $id)->getOne(T_APP_SESSIONS);
	    if (!empty($check_session)) {
	        if (($check_session->user_id == $user->id)) {
	            $delete_session = $db->where('id', $id)->delete(T_APP_SESSIONS);
	            $delete_session = $db->where('session_id', $check_session->session_id)->delete(T_SESSIONS);
	            if ($delete_session) {
	            	$data = array(
			            'status' => 200,
			            'data' => 'session deleted'
			        );
	            }
	        }
	    }
    }
}