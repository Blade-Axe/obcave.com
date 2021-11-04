<?php
require_once('./assets/init.php');
if (IS_LOGGED || $music->config->soundcloud_login == 'on') {
    header("Location: $site_url");
    exit;
}
$client_id = $music->config->sound_cloud_client_id; // enter your client id
$client_secret = $music->config->sound_cloud_client_secret; // enter your client secret
$redirect_uri = $music->config->site_url . "/deepsound-login.php"; // enter your redirect url

try {

    $code = $_GET['code'];
    $method = 1;
    $header = 0;
    $json = 1;
    $url = "https://api.soundcloud.com/oauth2/token";
    $data = array(
        "client_id" => $client_id,
        "client_secret" => $client_secret,
        "redirect_uri" => $redirect_uri,
        "grant_type" => "authorization_code",
        "code" => $code
    );
    $get_access_token = http_request_call($method, $url, $header, $data, $json);
    $access_token = $get_access_token['access_token'];
    $get_user_info = file_get_contents("https://api.soundcloud.com/me?oauth_token=$access_token");
    $result = json_decode($get_user_info, true);
    /*
     * https://developers.soundcloud.com/docs/api/reference#me
     * http://qass.im/login-with-soundcloud/index.php
    {
      "id": 3207,
      "permalink": "jwagener",
      "username": "Johannes Wagener",
      "uri": "https://api.soundcloud.com/users/3207",
      "permalink_url": "https://soundcloud.com/jwagener",
      "avatar_url": "https://i1.sndcdn.com/avatars-000001552142-pbw8yd-large.jpg?142a848",
      "country": "Germany",
      "full_name": "Johannes Wagener",
      "city": "Berlin",
      "description": "<b>Hacker at SoundCloud</b>\r\n\r\nSome of my recent Hacks:\r\n\r\nsoundiverse.com \r\nbrowse recordings with the FiRe app by artwork\r\n\r\ntopbillin.com \r\nfind people to follow on SoundCloud\r\n\r\nchatter.fm \r\nget your account hooked up with a voicebox\r\n\r\nrecbutton.com \r\nrecord straight to your soundcloud account",
      "discogs_name": null,
      "myspace_name": null,
      "website": "http://johannes.wagener.cc",
      "website_title": "johannes.wagener.cc",
      "online": true,
      "track_count": 12,
      "playlist_count": 1,
      "followers_count": 416,
      "followings_count": 174,
      "public_favorites_count": 26,
      "plan": "Pro Plus",
      "private_tracks_count": 63,
      "private_playlists_count": 3,
      "primary_email_confirmed": true
    }
    */


    $provider = 'soundcloud';
    $name = $result['full_name'];
    $avatar_url = $result['avatar_url'];
    $user_name = 'ds_' . $result['permalink'];
    $user_email = $user_name . '@soundcloud.com';
    $str = md5(microtime());
    $id = substr($str, 0, 9);
    $password = substr(md5(time()), 0, 9);
    $user_uniq_id = (empty($db->where('username', $id)->getValue(T_USERS, 'id'))) ? $id : 'u_' . $id;

    if (EmailExists($user_email) === true) {
        $db->where('email', $user_email);
        $login = $db->getOne(T_USERS);
        createUserSession($login->id);
        header("Location: $site_url");
        exit();
    } else {

        $re_data = array(
            'username' => secure($user_uniq_id, 0),
            'email' => secure($user_email, 0),
            'password' => secure(sha1($password), 0),
            'email_code' => secure(sha1($user_uniq_id), 0),
            'name' => secure($name),
            'avatar' => secure(importImageFromLogin($avatar_url)),
            'src' => secure($provider),
            'active' => '1'
        );
        $re_data['language'] = $music->config->language;
        if (!empty($_SESSION['lang'])) {
            if (in_array($_SESSION['lang'], $langs)) {
                $re_data['language'] = $_SESSION['lang'];
            }
        }

        $insert_id = $db->insert(T_USERS, $re_data);
        if ($insert_id) {
            createUserSession($insert_id);
            header("Location: $site_url");
            exit();
        }

    }

}catch (Exception $e) {
        exit($e->getMessage());
}