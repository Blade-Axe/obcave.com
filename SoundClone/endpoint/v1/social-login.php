<?php
$data   = array(
    'status' => 400,
    'error' => "Please check your details"
);
function _fetchDataFromURL($url = '') {
    if (empty($url)) {
        return false;
    }
    $ch = curl_init($url);
    curl_setopt( $ch, CURLOPT_POST, false );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt( $ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
    curl_setopt( $ch, CURLOPT_HEADER, false );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt( $ch, CURLOPT_TIMEOUT, 5);
    return curl_exec( $ch );
}
if (empty($_POST['social_access_token']) || empty($_POST['provider']) || empty($_POST['mobile_device_id'])) {
    $data   = array(
        'status' => 400,
        'error' => "Please check your details"
    );
} else {

    $social_id          = 0;
    $access_token       = $_POST['social_access_token'];
    $provider           = $_POST['provider'];
    $social_email = '';
    $social_name = '';
    if ($provider == 'facebook') {
        $get_user_details = _fetchDataFromURL("https://graph.facebook.com/me?fields=email,id,name,age_range&access_token={$access_token}");
        $json_data = json_decode($get_user_details);
        if (!empty($json_data->error)) {
            $error_code    = 4;
            $error_message = $json_data->error->message;
        } else if (!empty($json_data->id)) {
            $social_id = $json_data->id;
            $social_email = $json_data->email;
            $social_name = $json_data->name;
            if (empty($social_email)) {
                $social_email = 'fb_' . $social_id . '@facebook.com';
            }
        }
    } else if ($provider == 'google') {
        $get_user_details = fetchDataFromURL("https://oauth2.googleapis.com/tokeninfo?id_token={$access_token}");
        $json_data = json_decode($get_user_details);
        if (!empty($json_data->error)) {
            $error_code    = 4;
            $error_message = $json_data->error;
        } else if (!empty($json_data->kid)) {
            $social_id = $json_data->kid;
            $social_email = $json_data->email;
            $social_name = $json_data->name;
            if (empty($social_email)) {
                $social_email = 'go_' . $social_id . '@google.com';
            }
        }
    }
    else if ($provider == 'wowonder') {
        $udata = json_decode(base64_decode($access_token));
        if(isset($udata->user_data->username)){

            $social_id = (int)secure($udata->user_data->user_id);
            $social_email = $udata->user_data->email;
            $social_name = trim($udata->user_data->name);
            if (empty($social_email)) {
                $social_email = 'wowonder_' . $social_id . '@wowonder.com';
            }

        }
    }

    $create_session = false;
    if (!empty($social_id)) {
        if (EmailExists($social_email) === true) {
            $create_session = true;
        } else {
            $str          = md5(microtime());
            $id           = substr($str, 0, 9);
            $user_uniq_id = (UserExists($id) === false) ? $id : 'u_' . $id;
            $password = rand(1111, 9999);
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);
            $email_code = sha1(time() + rand(111,999));
            $insert_data = array(
                'username' => Secure($user_uniq_id, 0),
                'password' => $password_hashed,
                'email' => $social_email,
                'name' => $social_name,
                'ip_address' => get_ip_address(),
                'active' => 1,
                'email_code' => $email_code,
                'src' => Secure($provider),
                'last_active' => time(),
                'registered' => date('Y') . '/' . intval(date('m'))
            );
            $insert_data['language'] = $music->config->language;
            if (!empty($_SESSION['lang'])) {
                if (in_array($_SESSION['lang'], $langs)) {
                    $insert_data['language'] = $_SESSION['lang'];
                }
            }
            $user_id             = $db->insert(T_USERS, $insert_data);
            if (!empty($user_id)) {
                $create_session = true;
            }
        }
    }

    if ($create_session == true) {
        $user_id        = UserIdForLogin($social_email);
        createUserSession($user_id,'mobile');
        $music->loggedin = true;

        if (!empty($_POST['android_device_id'])) {
            $device_id  = Secure($_POST['android_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `android_device_id` = '{$device_id}' WHERE `id` = '{$user_id}'");
        }
        if (!empty($_POST['ios_device_id'])) {
            $device_id  = Secure($_POST['ios_device_id']);
            $update  = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `ios_device_id` = '{$device_id}' WHERE `id` = '{$user_id}'");
        }

        $music->user = userData($user_id);
        unset($music->user->password);
        $data = array(
            'status' => 200,
            'access_token' => $_SESSION['user_id'],
            'data' => $music->user
        );
    }

}
