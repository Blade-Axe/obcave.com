<?php
if ($option == 'update_lastseen') {
    if ($music->loggedin == false) {
        $data = array(
            'status' => 200
        );
    }
    if (LastSeen($music->user->id) === true) {
        $data = array(
            'status' => 200
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'session_status') {
    if ($music->loggedin == false) {
        $data = array(
            'status' => 200
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'confirm_user_unusal_login') {
    if (!empty($_POST['confirm_code']) && !empty($_SESSION['code_id'])) {
        $confirm_code = $_POST['confirm_code'];
        $user_id = $_SESSION['code_id'];
        if (empty($_POST['confirm_code'])) {
            $errors = lang('Please check your details.');
        } else if (empty($_SESSION['code_id'])) {
            $errors = lang('Error while activating your account.');
        }
        $confirm_code = $db->where('id', $user_id)->where('email_code', md5($confirm_code))->getValue(T_USERS, 'count(*)');
        if (empty($confirm_code)) {
            $errors = lang('Wrong confirmation code.');
        }
        if (empty($errors) && $confirm_code > 0) {
            unset($_SESSION['code_id']);
            $data = array(
                'status' => 200
            );
            if (!empty($_SESSION['last_login_data'])) {
                $update_user = $db->where('id', $user_id)->update(T_USERS, array('last_login_data' => serialize($_SESSION['last_login_data'])));
            } else if (!empty(get_ip_address())) {
                $getIpInfo = fetchDataFromURL("http://ip-api.com/json/" . get_ip_address());
                $getIpInfo = json_decode($getIpInfo, true);
                if ($getIpInfo['status'] == 'success' && !empty($getIpInfo['regionName']) && !empty($getIpInfo['countryCode']) && !empty($getIpInfo['timezone']) && !empty($getIpInfo['city'])) {
                    $update_user = $db->where('id', $user_id)->update(T_USERS, array('last_login_data' => serialize($getIpInfo)));

                }
            }
            $session = createUserSession($user_id);
            $_SESSION['user_id'] = $session;
            if (isset($_SESSION['last_login_data'])) {
                unset($_SESSION['last_login_data']);
            }
            if (!empty($_POST['last_url'])) {
                $data['location'] = $_POST['last_url'];
            } else {
                $data['location'] = $music->config->site_url;
            }
        }
        header("Content-type: application/json");
        if (!empty($errors)) {
            echo json_encode(array(
                'errors' => $errors
            ));
        } else {
            echo json_encode($data);
        }
        exit();
    }
}
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}
if ($option == 'update_spotlight_status') {
    if (empty($_POST['id']) || !is_numeric($_POST['id']) || $_POST['id'] == 0) {
        exit("Invalid track ID");
    } else {

        $id = Secure($_POST['id']);
        $songData = $db->where('id', $id)->where('user_id', $music->user->id)->getValue(T_SONGS,'spotlight');
        $stat = ($songData == 1) ? 0 : 1;
        $update_array = array('spotlight' => $stat);
        $update = $db->where('id', $id)->where('user_id', $music->user->id)->update(T_SONGS, $update_array);
        if ($update) {
            $data = array(
                'status' => 200
            );
        }

        header("Content-type: application/json");
        echo json_encode($data);
        exit();

    }
}
if ($option == 'general' || $option == 'profile' || $option == 'password' || $option == 'delete' || $option == 'update_two_factor' || $option == 'download_info') {
    if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] == 0) {
        exit("Invalid user ID");
    } else {
        $userData = userData($_POST['user_id']);
    }
}
if ($option == 'download_info') {
    $data['status'] = 400;
    if (!empty($_POST['my_information']) || !empty($_POST['songs']) || !empty($_POST['followers']) || !empty($_POST['following'])) {
        if (!empty($userData->info_file)) {
            unlink($userData->info_file);
        }
        $music->user_info = array();
        $html = '';
        $music->user_info['setting'] = new stdClass();
        if (!empty($_POST['my_information'])) {
            $music->user_info['setting'] = userData($userData->id);
            $sessions = array();
            $user_sessions  = $db->arrayBuilder()->where('user_id',$userData->id)->orderBy('time', 'DESC')->get(T_APP_SESSIONS);
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
                $sessions[] = $session;
            }
            $music->user_info['setting']->session = $sessions;
            $getBlocked = $db->where('user_id', $userData->id)->get(T_BLOCKS);
            $blocked = array();
            if (!empty($getBlocked)) {
                $blocked_list = '';
                foreach ($getBlocked as $key => $buser) {
                    $buser->user = userData($buser->blocked_id);
                    $blocked[] = $buser;
                }
            }
            $music->user_info['setting']->block = $blocked;
        }
        if (!empty($_POST['songs'])) {
            $db->where('user_id', $userData->id);
            $getUserSongs = $db->orderby('id', 'DESC')->get(T_SONGS, 1000000, 'id');
            $songs = array();
            if (!empty($getUserSongs)) {
                foreach ($getUserSongs as $key => $userSong) {
                    $songs[] = songData($userSong->id);
                }
            }
            $music->user_info['setting']->songs = $songs;
        }
        if (!empty($_POST['followers'])) {
            $getFollowers = $db->where('following_id', $userData->id)
                        ->where("follower_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")
                        ->orderBy('id', 'DESC')->get(T_FOLLOWERS, 1000000);
            $followers = array();
            if (!empty($getFollowers)) {
                foreach ($getFollowers as $key => $follower) {
                    $followers[] = userData($follower->follower_id);
                }
            }
            $music->user_info['setting']->followers = $followers;
        }
        if (!empty($_POST['following'])) {
            $getFollowers = $db->where('follower_id', $userData->id)
                        ->where("following_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")
                        ->orderBy('id', 'DESC')->get(T_FOLLOWERS, 1000000);
            $following = array();
            if (!empty($getFollowers)) {
                foreach ($getFollowers as $key => $follower) {
                    $following[] = userData($follower->following_id);
                }
            }
            $music->user_info['setting']->following = $following;
        }
            
        $html = loadPage('user_info/content');

        if (!file_exists('upload/files/' . date('Y'))) {
            @mkdir('upload/files/' . date('Y'), 0777, true);
        }
        if (!file_exists('upload/files/' . date('Y') . '/' . date('m'))) {
            @mkdir('upload/files/' . date('Y') . '/' . date('m'), 0777, true);
        }
        $folder   = 'files';
        $fileType = 'file';
        $dir         = "upload/files/" . date('Y') . '/' . date('m');
        $hash    = $dir . '/' . generateKey() . '_' . date('d') . '_' . md5(time()) . "_file.html";
        $file = fopen($hash, 'w');
        fwrite($file, $html);
        fclose($file);
        $update = $db->where('id', $userData->id)->update(T_USERS, array(
                'info_file' => $hash
            ));
        $data['status'] = 200;
        $data['message'] = lang("Your file is ready to download!");
    }
}
if ($option == 'request-withdrawal') {

    $error    = null;
    $balance  = $music->user->balance;
    $user_id  = $music->user->id;
    $currency = $music->config->currency;

    // Check is unprocessed requests exits
    $db->where('user_id',$user_id);
    $db->where('status',0);
    $requests = $db->getValue(T_WITHDRAWAL_REQUESTS, 'count(*)');

    if (!empty($requests)) {
        $error = lang('You can not submit withdrawal request until the previous requests has been approved / rejected');
    }

    else if ($music->user->balance < $_POST['amount']) {
        $error = lang("The amount exceeded your current balance.");
    } else if (50 > $_POST['amount']) {
        $error = lang("Minimum amount required is 50.");
    }

    else{

        if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $error = lang("Please check your details");
        }

        else if(empty($_POST['amount']) || !is_numeric($_POST['amount'])){
            $error = lang("Please check your details");
        }

        else if($_POST['amount'] < 5){
            $error = lang('The minimum withdrawal request is 50:') . " $currency";
        }
    }

    if (empty($error)) {
        $insert_data    = array(
            'user_id'   => $user_id,
            'amount'    => Secure($_POST['amount']),
            'email'     => Secure($_POST['email']),
            'requested' => time(),
            'currency' => $currency,
        );

        $insert  = $db->insert(T_WITHDRAWAL_REQUESTS,$insert_data);
        if (!empty($insert)) {
            $notif_data = array(
                'recipient_id' => 0,
                'type' => 'with',
                'admin' => 1,
                'time' => time()
            );
            $db->insert(T_NOTIFICATION,$notif_data);
            $data['status']  = 200;
            $data['message'] = lang('Your withdrawal request has been successfully sent!');
        }
    }

    else{
        $data['status']  = 400;
        $data['message'] = $error;
    }
}
if ($option == 'delete') {
    if( $music->config->delete_account == 'off' ){
        exit("You can not delete this account");
    }
    if (empty($_POST['c_pass'])) {
        $errors[] = lang("Please check your details");
    } else {
        $c_pass      = secure($_POST['c_pass']);

        if (!password_verify($c_pass, $db->where('id', $userData->id)->getValue(T_USERS, 'password'))) {
            $errors[] = lang("Your current password is invalid");
        } 
        if (empty($errors)) {
            if (isAdmin() || $userData->id == $user->id) {
                $delete = deleteUser($userData->id);
                if ($delete) {
                    $data = [
                        'status' => 200,
                        'message' => lang("Your account was successfully deleted, please wait..")
                    ];
                }
            }
        }
    }
}
if ($option == 'password') {
    if (empty($_POST['c_pass']) || empty($_POST['n_pass']) || empty($_POST['rn_pass'])) {
        $errors[] = lang("Please check your details");
    } else {
        $c_pass      = secure($_POST['c_pass']);
        $n_pass      = secure($_POST['n_pass']);
        $rn_pass     = secure($_POST['rn_pass']);
        if (!password_verify($c_pass, $db->where('id', $userData->id)->getValue(T_USERS, 'password'))) {
            $errors[] = lang("Your current password is invalid");
        } else if ($n_pass != $rn_pass) {
            $errors[] = lang("Passwords don't match");
        } else if (strlen($n_pass) < 4 || strlen($n_pass) > 32) {
            $errors[] = lang("New password is too short");
        }
        if (empty($errors)) {
            $update_data = [
                'password' => password_hash($n_pass, PASSWORD_DEFAULT),
            ];

            if (isAdmin() || $userData->id == $user->id) {
                $update = $db->where('id', $userData->id)->update(T_USERS, $update_data);
                if ($update) {
                    $delete = $db->where('user_id', $user->id)->where('session_id', $session_id, '<>')->delete(T_SESSIONS);
                    $data = [
                        'status' => 200,
                        'message' => lang("Your password was successfully updated!")
                    ];
                }
            }
        }
    }
}
if ($option == 'profile') {
    $name                 = secure($_POST['name']);
    $about_me             = secure($_POST['about_me']);
    $facebook             = secure($_POST['facebook']);
    $website              = secure($_POST['website']);
    if (!empty($website)) {
        if (!filter_var($_POST['website'], FILTER_VALIDATE_URL)) {
            $errors[] = lang("Invalid website url, format allowed: http(s)://*.*/*");
        }
    }
    if (!empty($facebook)) {
        if (filter_var($_POST['facebook'], FILTER_VALIDATE_URL)) {
            $errors[] = lang("Invalid facebook username, urls are not allowed");
        }
    }
    if (empty($errors)) {
        $update_data = [
            'name' => $name,
            'about' => $about_me,
            'facebook' => $facebook,
            'website' => $website,
        ];

        if (isAdmin() || $userData->id == $user->id) {
            $update = $db->where('id', $userData->id)->update(T_USERS, $update_data);
            if ($update) {

                $field_data = array();
                if (!empty($_POST['custom_fields'])) {
                    $fields = GetProfileFields('profile');
                    foreach ($fields as $key => $field) {
                        $name = $field['fid'];
                        if (isset($_POST[$name])) {
                            if (mb_strlen($_POST[$name]) > $field['length']) {
                                $errors[] = $field['name'] . ' field max characters is ' . $field['length'];
                            }
                            $field_data[] = array(
                                $name => $_POST[$name]
                            );
                        }
                    }
                }
                if (!empty($field_data)) {
                    $insert = UpdateUserCustomData($_POST['user_id'], $field_data);
                }


                $data = [
                    'status' => 200,
                    'message' => lang("Profile successfully updated!")
                ];
            }
        }
    }
}
if ($option == 'hide-announcement') {
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        $errors[] = lang("Please check your details");
    } else {
        $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
        $data['status'] = 400;
        if ($request === true) {
            $announcement_id = secure($_POST['id']);
            $user_id         = $music->user->id;
            $insert_data     = array(
                'announcement_id' => $announcement_id,
                'user_id'         => $user_id
            );

            $db->insert(T_ANNOUNCEMENT_VIEWS,$insert_data);
            $data['status'] = 200;
        }
    }
}
if ($option == 'interest') {
    if (!isset($_POST['genres']) || empty($_POST['genres'])) {
        $errors[] = lang("Please check your details");
    } else {
        $genres = secure($_POST['genres']);
        $arr = explode(',',$genres);
        $insert = false;
        if(!empty($arr)){
            foreach ($arr as $key){
                $is_exist = $db->where('user_id', $music->user->id)->where('category_id', $key)->getOne(T_USER_INTEREST,'count(id) as cnt')->cnt;
                if($is_exist == 0) {
                    $insert = $db->insert(T_USER_INTEREST, array('user_id' => $music->user->id, 'category_id' => $key));
                }
            }
            if($insert){
                $data = [
                    'status' => 200,
                    'message' => lang("Profile successfully updated!")
                ];
            }else{
                $errors[] = lang("Please check your details");
            }
        }else{
            $errors[] = lang("Please check your details");
        }
    }
}
if ($option == 'update-interest') {
    if (!isset($_POST['genres']) || empty($_POST['genres'])) {
        $errors[] = lang("Please check your details");
    } else {
        $genres = secure($_POST['genres']);
        $arr = explode(',',$genres);
        $insert = false;
        $db->where('user_id', $music->user->id)->delete(T_USER_INTEREST);
        if(!empty($arr)){
            foreach ($arr as $key){
                $insert = $db->insert(T_USER_INTEREST, array('user_id' => $music->user->id, 'category_id' => $key));
            }
            if($insert){
                $data = [
                    'status' => 200,
                    'message' => lang("Profile successfully updated!")
                ];
            }else{
                $errors[] = lang("Please check your details");
            }
        }else{
            $errors[] = lang("Please check your details");
        }
    }
}
if ($option == 'general') {
    if (empty($_POST['username']) || empty($_POST['email'])) {
        $errors[] = lang("Please check your details");
    } else {
        $username          = secure($_POST['username']);
        $email             = secure($_POST['email']);
        if (UsernameExits($_POST['username']) && $_POST['username'] != $userData->username) {
            $errors[] = lang("This username is already taken");
        }
        if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
            $errors[] = lang("Username length must be between 5 / 32");
        }
        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
            $errors[] = lang("Invalid username characters");
        }
        if (in_array($_POST['username'],$music->disallowed_usernames)){
            $errors[] = lang("This username is disallowed");
        }
        if (EmailExists($_POST['email']) && $_POST['email'] != $userData->email) {
            $errors[] = lang("This e-mail is already taken");
        }
        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = lang("This e-mail is invalid");
        }
        $country = $userData->country_id;
        if (in_array($_POST['country'], array_keys($countries_name))) {
            $country = secure($_POST['country']);
        }

        $gender = $userData->gender;
        if (in_array($_POST['gender'], ['male', 'female'])) {
            $gender = secure($_POST['gender']);
        }

        $age = $userData->age;
        if (is_numeric($_POST['age']) && ($_POST['age'] <= 100 || $_POST['age'] >= 0)) {
            $age = secure($_POST['age']);
        }

        $ispro = $userData->is_pro;
        if (!empty($_POST['ispro']) && IsAdmin()) {
            if ($_POST['ispro'] == 'yes') {
                $ispro = 1;
            } else if ($_POST['ispro'] == 'no') {
                $ispro = 0;
            }
            if ($ispro == $userData->is_pro) {
                $ispro = $userData->is_pro;
            }
        }

        $verified = $userData->verified;
        if (!empty($_POST['verified']) && IsAdmin()) {
            if ($_POST['verified'] == 'yes') {
                $verified = 1;
            } else if ($_POST['verified'] == 'no') {
                $verified = 0;
            }
            if ($verified == $userData->verified) {
                $verified = $userData->verified;
            }
        }

        $isartist = $userData->artist;
        if (!empty($_POST['user_type']) && IsAdmin()) {
            if ($_POST['user_type'] == 'yes') {
                $isartist = 1;
            } else if ($_POST['user_type'] == 'no') {
                $isartist = 0;
            }
            if ($isartist == $userData->artist) {
                $isartist = $userData->artist;
            }
        }

        $wallet = $userData->balance;
        if (isset($_POST['wallet']) && IsAdmin()) {
            if (is_numeric($_POST['wallet'])) {
                $wallet = $_POST['wallet'];
            }
        }

        if (empty($errors)) {

            $update_data = [
                'username' => $username,
                'email' => $email,
                'gender' => $gender,
                'age' => $age,
                'country_id' => $country,
                'is_pro' => $ispro,
                'verified' => $verified,
                'artist' => $isartist,
                'wallet' => $wallet
            ];
            $update_data['paypal_email'] = '';
            if (!empty($_POST['paypal_email']) && filter_var($_POST['paypal_email'], FILTER_VALIDATE_EMAIL)) {
                $update_data['paypal_email'] = Secure($_POST['paypal_email']);
            }

            if (isAdmin() || $userData->id == $user->id) {
                $update = $db->where('id', $userData->id)->update(T_USERS, $update_data);
                if ($update) {

                    $field_data = array();
                    if (!empty($_POST['custom_fields'])) {
                        $fields = GetProfileFields('general');
                        foreach ($fields as $key => $field) {
                            $name = $field['fid'];
                            if (isset($_POST[$name])) {
                                if (mb_strlen($_POST[$name]) > $field['length']) {
                                    $errors[] = $field['name'] . ' field max characters is ' . $field['length'];
                                }
                                $field_data[] = array(
                                    $name => $_POST[$name]
                                );
                            }
                        }
                    }
                    if (!empty($field_data)) {
                        $insert = UpdateUserCustomData($_POST['user_id'], $field_data);
                    }

                    $data = [
                        'status' => 200,
                        'message' => lang("Settings successfully updated!")
                    ];
                }
            }
        }
    }
}
if ($option == 'update-profile-cover') {
	if (!empty($_FILES)) {
		if (!empty($_FILES['cover']['tmp_name'])) {
            $type = (!empty($_REQUEST['type'])) ? secure($_REQUEST['type']) : "";
            $file_info = array(
                'file' => $_FILES['cover']['tmp_name'],
                'size' => $_FILES['cover']['size'],
                'name' => $_FILES['cover']['name'],
                'type' => $_FILES['cover']['type'],
                'crop' => array('width' => 1600, 'height' => 400),
                'allowed' => 'jpg,png,jpeg,gif'
            );
            if ($type == 'artist') {
                $file_info['crop'] = array('width' => 1400, 'height' => 800);
            }
            $file_upload = shareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $update_data['cover'] = $file_upload['filename'];
                $db->where('id', $user->id)->update(T_USERS, $update_data);
                $data['status'] = 200;
                RecordUserActivities('update_profile_cover',array('uid' => $user->id));
                $data['img'] = getMedia($file_upload['filename']);
            }
        }
	}
}
if ($option == 'update-profile-picture') {
	if (!empty($_FILES)) {
		if (!empty($_FILES['avatar']['tmp_name'])) {
            $file_info = array(
                'file' => $_FILES['avatar']['tmp_name'],
                'size' => $_FILES['avatar']['size'],
                'name' => $_FILES['avatar']['name'],
                'type' => $_FILES['avatar']['type'],
                'crop' => array('width' => 400, 'height' => 400),
                'allowed' => 'jpg,png,jpeg,gif'
            );
            $file_upload = shareFile($file_info);
            if (!empty($file_upload['filename'])) {
                $update_data['avatar'] = $file_upload['filename'];
                $db->where('id', $user->id)->update(T_USERS, $update_data);
                $data['status'] = 200;
                RecordUserActivities('update_profile_picture',array('uid' => $user->id));
                $data['img'] = getMedia($file_upload['filename']);
            }
        }
	}
}
if ($option == 'update_user_device_id') {
    if (!empty($_POST['id'])) {
        $id = Secure($_POST['id']);
        if ($id != $music->user->web_device_id) {
            $update = $db->where('id', $music->user->id)->update(T_USERS, array(
                'web_device_id' => $id
            ));
            if ($update) {
                $data = array(
                    'status' => 200
                );
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'update_notification_setting') {
    if (!empty($_POST['id'])) {
        $id = Secure($_POST['id']);
        $userDatax = $db->where('id',$music->user->id)->getOne(T_USERS);
        $stat = ($userDatax->{$id} == 1) ? 0 : 1;
        $update_array = array($id => $stat);
        $update = $db->where('id', $music->user->id)->update(T_USERS, $update_array);
        if ($update) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'remove_user_device_id'){
    if (!empty($music->user->web_device_id)) {
        $update = $db->where('id', $music->user->id)->update(T_USERS, array(
            'web_device_id' => ''
        ));
        if ($update) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete_s') {
    if (!empty($_POST['id'])) {
        $id = Secure($_POST['id']);
    }
    $check_session = $db->where('id', $id)->getOne(T_APP_SESSIONS);
    if (!empty($check_session)) {
        if (($check_session->user_id == $music->user->id) || IsAdmin()) {
            $delete_session = $db->where('id', $id)->delete(T_APP_SESSIONS);
            $delete_session = $db->where('session_id', $check_session->session_id)->delete(T_SESSIONS);
            if ($delete_session) {
                $data['status'] = 200;
            }
        }
    }
}
if ($option == 'update_two_factor') {
    $s = '';
    if (isset($_GET['s'])) {
        $s = Secure($_GET['s'], 0);
    }
    $error = '';

    if ($s == 'enable') {
        $is_phone = false;
        if (!empty($_POST['phone_number']) && ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'phone')) {
            preg_match_all('/\+(9[976]\d|8[987530]\d|6[987]\d|5[90]\d|42\d|3[875]\d|
                            2[98654321]\d|9[8543210]|8[6421]|6[6543210]|5[87654321]|
                            4[987654310]|3[9643210]|2[70]|7|1)\d{1,14}$/', $_POST['phone_number'], $matches);
            if (!empty($matches[1][0]) && !empty($matches[0][0])) {
                $is_phone = true;
            }
        }
        if ((empty($_POST['phone_number']) && $music->config->two_factor_type == 'phone') || empty($_POST['two_factor']) || $_POST['two_factor'] != 'enable') {
            $error = lang('Please check your details.');
        }
        elseif (!empty($_POST['phone_number']) && ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'phone') && $is_phone == false) {
            $error = lang('Phone number should be as this format: +90..');
        }

        if (empty($error)) {
            $code = rand(111111, 999999);
            $hash_code = md5($code);
            $message = "Your confirmation code is: $code";
            $phone_sent = false;
            $email_sent = false;
            if (!empty($_POST['phone_number']) && ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'phone')) {
                $send = SendSMSMessage($_POST['phone_number'], $message);
                if ($send) {
                    $phone_sent = true;
                    $Update_data = array(
                        'phone_number' => secure($_POST['phone_number'])
                    );
                    $update = $db->where('id', $userData->id)->update(T_USERS, $Update_data);
                }
            }
            if ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'email') {
                $send_message_data       = array(
                    'from_email' => $music->config->email,
                    'from_name' => $music->config->name,
                    'to_email' => $music->user->email,
                    'to_name' => $music->user->name,
                    'subject' => 'Please verify that it’s you',
                    'charSet' => 'utf-8',
                    'message_body' => $message,
                    'is_html' => true
                );
                $send = SendMessage($send_message_data);
                if ($send) {
                    $email_sent = true;
                }
            }
            if ($email_sent == true || $phone_sent == true) {
                $Update_data = array(
                    'two_factor' => 0,
                    'two_factor_verified' => 0,
                    'email_code' => $hash_code
                );
                $update = $db->where('id', $userData->id)->update(T_USERS, $Update_data);
                $data = array(
                    'status' => 200,
                    'message' => lang('We have sent you an email with the confirmation code.')
                );
            }
            else{
                $data = array(
                    'status' => 400,
                    'message' => lang('Something went wrong, please try again later.'),
                );
            }
        }
    }

    if ($s == 'disable') {
        if ($_POST['two_factor'] != 'disable') {
            $error = lang('Something went wrong, please try again later.');
            $data = array(
                'status' => 400,
                'message' => $error,
            );
        } else {
            $Update_data = array(
                'two_factor' => 0,
                'two_factor_verified' => 0
            );
            $update = $db->where('id', $userData->id)->update(T_USERS, $Update_data);
            $data = array(
                'status' => 200,
                'message' => lang("Settings successfully updated!")
            );
        }
    }

    if ($s == 'verify') {
        if (empty($_POST['code'])) {
            $error = lang('Something went wrong, please try again later.');
        }
        else{
            $confirm_code = $db->where('id', $userData->id)->where('email_code', md5($_POST['code']))->getValue(T_USERS, 'count(*)');
            $Update_data = array();
            if (empty($confirm_code)) {
                $error = lang('Wrong confirmation code.');
            }
            if (empty($error)) {
                $message = '';
                if ($music->config->two_factor_type == 'phone') {
                    $message = lang('Your phone number has been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        $Update_data['phone_number'] = $userData->new_phone;
                        $Update_data['new_phone'] = '';
                    }
                }
                if ($music->config->two_factor_type == 'email') {
                    $message = lang('Your E-mail has been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        $Update_data['email'] = $userData->new_email;
                        $Update_data['new_email'] = '';
                    }
                }
                if ($music->config->two_factor_type == 'both') {
                    $message = lang('Your phone number and E-mail have been successfully verified.');
                    if (!empty($_GET['setting'])) {
                        if (!empty($userData->new_email)) {
                            $Update_data['email'] = $userData->new_email;
                            $Update_data['new_email'] = '';
                        }
                        if (!empty($userData->new_phone)) {
                            $Update_data['phone_number'] = $userData->new_phone;
                            $Update_data['new_phone'] = '';
                        }
                    }
                }
                $Update_data['two_factor_verified'] = 1;
                $Update_data['two_factor'] = 1;
                $update = $db->where('id', $userData->id)->update(T_USERS, $Update_data);
                $data = array(
                    'status' => 200,
                    'message' => $message,
                );
            }
        }
        if (!empty($error)) {
            $data = array(
                'status' => 400,
                'message' => $error,
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
?>