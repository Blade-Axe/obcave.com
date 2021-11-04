<?php
if ($option == 'general' || $option == 'profile' || $option == 'password' || $option == 'delete' || $option == 'get-profile' || $option == 'update_notification_setting') {
    if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] == 0) {
        $errors[] = "Invalid user ID";
    } else {
        $userData = userData($_POST['user_id']);
    }
}
if ($option == 'purchase_track') {
    if (empty($_POST['user_id']) || !is_numeric($_POST['user_id']) || $_POST['user_id'] == 0) {
        $errors[] = "Invalid user ID";
    }
    else if (empty($_POST['track_id'])) {
        $errors[] = "Please check your details";
    } else {

        if (!filter_var($_POST['track_id'], FILTER_SANITIZE_NUMBER_INT)) {
            $errors[] = "Invalid id";
        }
        if ($_POST['track_id'] == 0) {
            $errors[] = "Invalid id";
        }
        if (empty($errors)) {
            $payment_via = (isset($_POST['via'])) ? secure($_POST['via']) : 'PayPal';
            $track_id                 = secure($_POST['track_id']);
            $songData = songData($track_id);
            if (empty($songData->price)) {
                $data = array('status' => 400, 'error' => "Please check your details");
                echo json_encode($data);
                exit();
            }

            $getAdminCommission = $music->config->commission;
            $final_price = round((($getAdminCommission * $songData->price) / 100), 2);
            $addPurchase = [
                'track_id' => $songData->id,
                'user_id' => $user->id,
                'price' => $songData->price,
                'track_owner_id' => $songData->user_id,
                'final_price' => $final_price,
                'commission' => $getAdminCommission,
                'time' => time()
            ];

            $createPayment = $db->insert(T_PURCHAES, $addPurchase);
            if ($createPayment) {
                CreatePayment(array(
                    'user_id'   => $user->id,
                    'amount'    => $final_price,
                    'type'      => 'TRACK',
                    'pro_plan'  => 0,
                    'info'      => $songData->audio_id,
                    'via'       => $payment_via
                ));
                $addUserWallet = $db->where('id', $songData->user_id)->update(T_USERS, ['balance' => $db->inc($final_price)]);
                $create_notification = createNotification([
                    'notifier_id' => $user->id,
                    'recipient_id' => $songData->user_id,
                    'type' => 'purchased',
                    'track_id' => $songData->id,
                    'url' => "track/$songData->audio_id"
                ]);
                $data = [
                    'status' => 200,
                    'message' => 'Track purchased successfully'
                ];
            } else {
                $data = array('status' => 400, 'error' => "Please check your details");
                echo json_encode($data);
                exit();
            }
        }else{
            $data = array('status' => 400, 'error' => $errors);
            echo json_encode($data);
            exit();
        }
    }
}
if ($option == 'my-purchases') {
    if (empty($_POST['id'])) {
        $errors[] = "Please check your details";
    } else {

        if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
            $errors[] = "Invalid id";
        }
        if ($_POST['id'] == 0) {
            $errors[] = "Invalid id";
        }
        if (empty($errors)) {
            $id                 = secure($_POST['id']);
            $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
            $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
            $data = [
                'status' => 200,
                'data' => GetPurchased($id,$limit,$offset)
            ];
        }
    }
}
if ($option == 'get-profile') {
    if (empty($_POST['user_id']) || empty($_POST['fetch'])) {
        $errors[] = "Please check your details";
    } else {

        if (!filter_var($_POST['user_id'], FILTER_SANITIZE_NUMBER_INT)) {
            $errors[] = "Invalid id";
        }
        if ($_POST['user_id'] == 0) {
            $errors[] = "Invalid id";
        }
        if (empty($errors)) {
            $count                      = [];
            
            if( isset( $_POST['access_token'] ) ) {
                $request_uid = getUserFromSessionID($_POST['access_token'], 'mobile');
            }else{
                $request_uid = secure($_POST['user_id']);
            }

            $id          = secure($_POST['user_id']);
            $music->user = userData($id);
            unset($music->user->password);
            $count['followers']         = $db->where('following_id', $id)->where("follower_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $id)")->getValue(T_FOLLOWERS, 'COUNT(*)');
            $count['following']         = $db->where('follower_id', $id)->where("following_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $id)")->getValue(T_FOLLOWERS, 'COUNT(*)');
            $count['albums']            = $db->where('user_id', $id)->getValue(T_ALBUMS, 'COUNT(*)');
            $count['playlists']         = $db->where('user_id', $id)->getValue(T_PLAYLISTS, 'COUNT(*)');
            $count['blocks']            = $db->where('user_id', $id)->getValue(T_BLOCKS, 'COUNT(*)');
            $count['favourites']        = $db->where('user_id', $id)->getValue(T_FOV, 'COUNT(*)');
            $count['recently_played']   = $db->where('user_id', $id)->getValue(T_VIEWS, 'COUNT(*)');;
            $count['liked']             = $db->where('user_id', $id)->where('track_id', 0,"<>")->getValue(T_LIKES, 'COUNT(*)');
            $count['activities']        = $db->where('user_id', $id)->getValue(T_ACTIVITIES, 'COUNT(*)');
            $count['latest_songs']      = $db->where('user_id', $id)->getValue(T_SONGS, 'COUNT(*)');
            $count['top_songs']         = count($db->rawQuery("
                                            SELECT " . T_SONGS . ".*, COUNT(" . T_VIEWS . ".id) AS " . T_VIEWS . "
                                            FROM " . T_SONGS . " LEFT JOIN " . T_VIEWS . " ON " . T_SONGS . ".id = " . T_VIEWS . ".track_id
                                            WHERE " . T_SONGS . ".user_id = ".$id."
                                            GROUP BY " . T_SONGS . ".id
                                            ORDER BY " . T_VIEWS . " DESC"));
            $count['store']             = $db->where('user_id', $id)->where('price', '0', '<>')->getValue(T_SONGS, 'COUNT(*)');
            $count['stations']             = $db->where('user_id', $id)->where('src', 'radio')->getValue(T_SONGS, 'COUNT(*)');

            $user_data = $music->user;
            $user_data->IsFollowing = isFollowing($request_uid, $id);
            $user_data->IsBloked = isBlocked($id);

            $fetch = explode(',',secure($_POST['fetch']));
            if(in_array('followers',$fetch)){
                $followers = GetFollowers($id);
                $user_data->followers = $followers['data'];
                //$count['followers'] = $followers['count'];
            }
            if(in_array('following',$fetch)){
                $following = GetFollowing($id);
                $user_data->following = $following['data'];
                //$count['following'] = $following['count'];
            }
            if(in_array('albums',$fetch)){
                $albums = GetAlbums($id);
                $user_data->albums = $albums['data'];
                //$count['albums'] = $albums['count'];
            }
            if(in_array('playlists',$fetch)){
                $playlists = GetPlaylists($id);
                $user_data->playlists = $playlists['data'];
                //$count['playlists'] = $playlists['count'];
            }
            if(in_array('blocks',$fetch)){
                $blocks = GetBlocks($id);
                $user_data->blocks = $blocks['data'];
                //$count['blocks'] = $blocks['count'];
            }
            if(in_array('favourites',$fetch)){
                $favourites = GetFavourites($id);
                $user_data->favourites = $favourites['data'];
                //$count['favourites'] = $favourites['count'];
            }
            if(in_array('recently_played',$fetch)){
                $recently_played = GetRecentlyPlayed($id);
                $user_data->recently_played = $recently_played['data'];
                //$count['recently_played'] = $recently_played['count'];
            }
            if(in_array('liked',$fetch)){
                $liked = GetLiked($id);
                $user_data->liked = $liked['data'];
                //$count['liked'] = $liked['count'];
            }
            if(in_array('activities',$fetch)){
                $activities = GetActivities($id);
                $user_data->activities = $activities['data'];
                //$count['activities'] = $activities['count'];
            }
            if(in_array('latest_songs',$fetch)){
                $latestsongs = GetLatestSongs($id);
                $user_data->latestsongs = $latestsongs['data'];
                //$count['latest_songs'] = $latestsongs['count'];
            }
            if(in_array('top_songs',$fetch)){
                $top_songs = GetTopSongs($id);
                $user_data->top_songs = $top_songs['data'];
                //$count['top_songs'] = $top_songs['count'];
            }
            if(in_array('store',$fetch)){
                $store = GetStore($id);
                $user_data->store = $store['data'];
                //$count['store'] = $store['count'];
            }
            if(in_array('stations',$fetch)){
                $sql = 'SELECT * FROM `'.T_SONGS.'` WHERE `src` = "radio" AND `user_id` = "'.$id.'" limit 20';
                $getUserStations            = $db->objectBuilder()->rawQuery($sql);
                $songs_data = array();
                if (!empty($getUserStations)) {

                    foreach ($getUserStations as $key => $song) {
                        $userSong = songData($song->id);
                        unset($userSong->publisher->password);
                        foreach ($userSong->songArray as $key => $value) {
                            unset($userSong->songArray->{$key}->USER_DATA->password);
                        }
                        $songs_data[] = $userSong;
                    }
                }
                $user_data->stations = $songs_data;
            }
            if(secure($_POST['fetch']) == "all"){
                $followers = GetFollowers($id);
                $user_data->followers = $followers['data'];
                //$count['followers'] = $followers['count'];

                $following = GetFollowing($id);
                $user_data->following = $following['data'];
                //$count['following'] = $following['count'];

                $albums = GetAlbums($id);
                $user_data->albums = $albums['data'];
                //$count['albums'] = $albums['count'];

                $playlists = GetPlaylists($id);
                $user_data->playlists = $playlists['data'];
                //$count['playlists'] = $playlists['count'];

                $blocks = GetBlocks($id);
                $user_data->blocks = $blocks['data'];
                //$count['blocks'] = $blocks['count'];

                $favourites = GetFavourites($id);
                $user_data->favourites = $favourites['data'];
                //$count['favourites'] = $favourites['count'];

                $recently_played = GetRecentlyPlayed($id);
                $user_data->recently_played = $recently_played['data'];
                //$count['recently_played'] = $recently_played['count'];

                $liked = GetLiked($id);
                $user_data->liked = $liked['data'];
                //$count['liked'] = $liked['count'];

                $activities = GetActivities($id);
                $user_data->activities = $activities['data'];
                //$count['activities'] = $activities['count'];

                $latestsongs = GetLatestSongs($id);
                $user_data->latestsongs = $latestsongs['data'];
                //$count['latest_songs'] = $latestsongs['count'];

                $top_songs = GetTopSongs($id);
                $user_data->top_songs = $top_songs['data'];
                //$count['top_songs'] = $top_songs['count'];

                $store = GetStore($id);
                $user_data->store = $store['data'];
                //$count['store'] = $store['count'];

                $sql = 'SELECT * FROM `'.T_SONGS.'` WHERE `src` = "radio" AND `user_id` = "'.$id.'" limit 20';
                $getUserStations            = $db->objectBuilder()->rawQuery($sql);
                $songs_data = array();
                if (!empty($getUserStations)) {

                    foreach ($getUserStations as $key => $song) {
                        $userSong = songData($song->id);
                        unset($userSong->publisher->password);
                        foreach ($userSong->songArray as $key => $value) {
                            unset($userSong->songArray->{$key}->USER_DATA->password);
                        }
                        $songs_data[] = $userSong;
                    }
                }
                $user_data->stations = $songs_data;
            }

            $data = [
                'status' => 200,
                'data' => $user_data,
                'details' => $count
            ];
        }
    }
}

if ($option == 'get-pro-user') {
    $users = [];
    $pro_users = $db->where('is_pro','1')->objectbuilder()->orderBy('id', 'DESC')->get(T_USERS, 20);
    foreach ($pro_users as $key => $value){
        $users[] = userData($value->id);
    }
    $data = [
        'status' => 200,
        'data' => $users
    ];
}
if ($option == 'get-genres') {
    $genres = [];
    $categories = getCategories(false);
    foreach ($categories as $key => $value) {
        $value->background_thumb = (empty($value->background_thumb)) ? $music->config->theme_url . '/img/crowd.jpg' : getMedia($value->background_thumb);
        $genres[] = $value;
    }
    $data = [
        'status' => 200,
        'data' => $genres
    ];
}
if ($option == 'get-following') {
    if (empty($_POST['id'])) {
        $errors[] = "Please check your details";
    } else {

        if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
            $errors[] = "Invalid id";
        }
        if ($_POST['id'] == 0) {
            $errors[] = "Invalid id";
        }
        if (empty($errors)) {
            $id                 = secure($_POST['id']);
            $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
            $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
            $data = [
                'status' => 200,
                'data' => GetFollowing($id,$limit,$offset)
            ];
        }
    }
}
if ($option == 'get-follower') {
    if (empty($_POST['id'])) {
        $errors[] = "Please check your details";
    } else {

        if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
            $errors[] = "Invalid id";
        }
        if ($_POST['id'] == 0) {
            $errors[] = "Invalid id";
        }
        if (empty($errors)) {
            $id                 = secure($_POST['id']);
            $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
            $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
            $data = [
                'status' => 200,
                'data' => GetFollowers($id,$limit,$offset)
            ];
        }
    }
}
if ($option == 'get-artists') {
    $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
    $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
    $data = [
        'status' => 200,
        'data' => GetArtists($limit,$offset)
    ];
}

if(!in_array($option, $whitelist)) {
    if (IS_LOGGED == false) {
        $errors[] = "You ain't logged in!";
    }
}

if (empty($errors)) {

    if ($option == 'profile') {
        if (empty($_POST['name'])) {
            $errors[] = "Please check your details";
        } else {
            $name                 = secure($_POST['name']);
            $about_me             = secure($_POST['about_me']);
            $facebook             = secure($_POST['facebook']);
            $website              = secure($_POST['website']);
            if (!filter_var($_POST['website'], FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid website url, format allowed: http(s)://*.*/*";
            }
            if (filter_var($_POST['facebook'], FILTER_VALIDATE_URL)) {
                $errors[] = "Invalid facebook username, urls are not allowed";
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
                        $data = [
                            'status' => 200,
                            'message' => "Profile successfully updated!"
                        ];
                    }
                }
            }
        }
    }

    if ($option == 'delete') {
        if (empty($_POST['c_pass'])) {
            $errors[] = "Please check your details";
        } else {
            $c_pass      = secure($_POST['c_pass']);

            if (!password_verify($c_pass, $db->where('id', $userData->id)->getValue(T_USERS, 'password'))) {
                $errors[] = "Your current password is invalid";
            }
            if (empty($errors)) {
                if (isAdmin() || $userData->id == $user->id) {
                    $delete = deleteUser($userData->id);
                    if ($delete) {
                        $data = [
                            'status' => 200,
                            'message' => "Your account was successfully deleted, please wait.."
                        ];
                    }
                }
            }
        }
    }

    if ($option == 'password') {
        if (empty($_POST['c_pass']) || empty($_POST['n_pass']) || empty($_POST['rn_pass'])) {
            $errors[] = "Please check your details";
        } else {
            $c_pass      = secure($_POST['c_pass']);
            $n_pass      = secure($_POST['n_pass']);
            $rn_pass     = secure($_POST['rn_pass']);
            if (!password_verify($c_pass, $db->where('id', $userData->id)->getValue(T_USERS, 'password'))) {
                $errors[] = "Your current password is invalid";
            } else if ($n_pass != $rn_pass) {
                $errors[] = "Passwords don't match";
            } else if (strlen($n_pass) < 4 || strlen($n_pass) > 32) {
                $errors[] = "New password is too short";
            }
            if (empty($errors)) {
                $update_data = [
                    'password' => password_hash($n_pass, PASSWORD_DEFAULT),
                ];

                if (isAdmin() || $userData->id == $user->id) {
                    $update = $db->where('id', $userData->id)->update(T_USERS, $update_data);
                    if ($update) {
                        $data = [
                            'status' => 200,
                            'message' => "Your password was successfully updated!"
                        ];
                    }
                }
            }
        }
    }



    if ($option == 'get-recently-played') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {

            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
                $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
                $data = [
                    'status' => 200,
                    'data' => GetRecentlyPlayed($id,$limit,$offset)
                ];
            }
        }
    }

    if ($option == 'get-favourites') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {

            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
                $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
                $data = [
                    'status' => 200,
                    'data' => GetFavourites($id,$limit,$offset)
                ];
            }
        }
    }

    if ($option == 'get-blocks') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {

            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
                $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
                $data = [
                    'status' => 200,
                    'data' => GetBlocks($id,$limit,$offset)
                ];
            }
        }
    }

    if ($option == 'get-liked') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {

            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $limit              = (isset($_POST['limit'])) ? secure($_POST['limit']) : 20;
                $offset             = (isset($_POST['offset'])) ? secure($_POST['offset']) : 0;
                $data = [
                    'status' => 200,
                    'data' => GetLiked($id,$limit,$offset)
                ];
            }
        }
    }

    if ($option == 'get-recommended') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {
            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $data = [
                    'status' => 200,
                    'data' => GetRecommendedSongs()
                ];
            }
        }
    }

    if ($option == 'general') {
        if (empty($_POST['username']) || empty($_POST['email'])) {
            $errors[] = "Please check your details";
        } else {
            $username          = secure($_POST['username']);
            $email             = secure($_POST['email']);
            if (UsernameExits($_POST['username']) && $_POST['username'] != $userData->username) {
                $errors[] = "This username is already taken";
            }
            if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
                $errors[] = "Username length must be between 5 / 32";
            }
            if (!preg_match('/^[\w]+$/', $_POST['username'])) {
                $errors[] = "Invalid username characters";
            }
            if (EmailExists($_POST['email']) && $_POST['email'] != $userData->email) {
                $errors[] = "This e-mail is already taken";
            }
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "This e-mail is invalid";
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

            if (empty($errors)) {
                $update_data = [
                    'username' => $username,
                    'email' => $email,
                    'gender' => $gender,
                    'age' => $age,
                    'country_id' => $country,
                    'is_pro' => $ispro,
                    'verified' => $verified
                ];

                if (isAdmin() || $userData->id == $user->id) {
                    $update = $db->where('id', $userData->id)->update(T_USERS, $update_data);
                    if ($update) {
                        $data = [
                            'status' => 200,
                            'message' => "Settings successfully updated!"
                        ];
                    }
                }
            }
        }
    }

    if ($option == 'update-profile-cover') {
        if (empty($_FILES)) {
            $errors[] = "Please check your details";
        }
        if (empty($errors)) {
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
                    $data['img'] = getMedia($file_upload['filename']);
                }
            }
        }
    }

    if ($option == 'update-profile-picture') {
        if (empty($_FILES)) {
            $errors[] = "Please check your details";
        }
        if (empty($errors)) {
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
                    $data['img'] = getMedia($file_upload['filename']);
                }
            }
        }
    }

    if ($option == 'upgrade-membership') {
        if (empty($_POST['id'])) {
            $errors[] = "Please check your details";
        } else {

            if (!filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT)) {
                $errors[] = "Invalid id";
            }
            if ($_POST['id'] == 0) {
                $errors[] = "Invalid id";
            }
            if (empty($errors)) {
                $id                 = secure($_POST['id']);
                $updated = $db->where('id',$id)->update(T_USERS,array('is_pro'=> 1, 'pro_time'=> time()));
                if($updated) {
                    CreatePayment(array(
                        'user_id'   => $id,
                        'amount'    => $music->config->pro_price,
                        'type'      => 'PRO',
                        'pro_plan'  => 1,
                        'info'      => '',
                        'via'       => '-'
                    ));

                    $data = [
                        'status' => 200,
                        'data' => 'Upgraded successfully'
                    ];
                }else{
                    $data = [
                        'status' => 400,
                        'error' => 'Error While Upgrading Account'
                    ];
                }
            }
        }
    }
    if ($option == 'update_two_factor') {
        $error = '';
        if ($_POST['two_factor'] == 'enable') {
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
                $error = 'Please check your details.';
            }
            elseif (!empty($_POST['phone_number']) && ($music->config->two_factor_type == 'both' || $music->config->two_factor_type == 'phone') && $is_phone == false) {
                $error = 'Phone number should be as this format: +90..';
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
                        $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
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
                    $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                    $data = [
                        'status' => 200,
                        'data' => 'We have sent you an email with the confirmation code.'
                    ];
                }
                else{
                    $data = [
                        'status' => 400,
                        'error' => 'Something went wrong, please try again later.'
                    ];
                }
            }
            else{
                $data = [
                        'status' => 400,
                        'error' => $error
                    ];
            }
        }
        elseif($_POST['two_factor'] == 'disable'){
            $Update_data = array(
                'two_factor' => 0,
                'two_factor_verified' => 0
            );
            $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
            $data = [
                        'status' => 200,
                        'data' => 'Settings successfully updated!'
                    ];
        }

    }
    if ($option == 'verify_two_factor') {
        if (empty($_POST['code'])) {
            $data = [
                    'status' => 400,
                    'error' => 'code can not be empty'
                ];
        }
        else{
            $confirm_code = $db->where('id', $user->id)->where('email_code', md5($_POST['code']))->getValue(T_USERS, 'count(*)');
            $Update_data = array();
            if (empty($confirm_code)) {
                $data = [
                    'status' => 400,
                    'error' => 'Wrong confirmation code.'
                ];
            }
            if (empty($error)) {
                $message = '';
                if ($music->config->two_factor_type == 'phone') {
                    $message = 'Your phone number has been successfully verified.';
                    if (!empty($user->new_phone)) {
                        $Update_data['phone_number'] = $user->new_phone;
                        $Update_data['new_phone'] = '';
                    }
                }
                if ($music->config->two_factor_type == 'email') {
                    $message = 'Your E-mail has been successfully verified.';
                    if (!empty($user->new_email)) {
                        $Update_data['email'] = $user->new_email;
                        $Update_data['new_email'] = '';
                    }
                }
                if ($music->config->two_factor_type == 'both') {
                    $message = 'Your phone number and E-mail have been successfully verified.';
                    if (!empty($_GET['setting'])) {
                        if (!empty($user->new_email)) {
                            $Update_data['email'] = $user->new_email;
                            $Update_data['new_email'] = '';
                        }
                        if (!empty($user->new_phone)) {
                            $Update_data['phone_number'] = $user->new_phone;
                            $Update_data['new_phone'] = '';
                        }
                    }
                }
                $Update_data['two_factor_verified'] = 1;
                $Update_data['two_factor'] = 1;
                $Update_data['email_code'] = '';
                $update = $db->where('id', $user->id)->update(T_USERS, $Update_data);
                $data = [
                        'status' => 200,
                        'data' => $message
                    ];
            }
        }

    }
    if ($option == 'update_notification_setting') {
        $array = array('email_on_follow_user' => 0,
                      'email_on_liked_track' => 0,
                      'email_on_reviewed_track' => 0,
                      'email_on_liked_comment' => 0,
                      'email_on_artist_status_changed' => 0,
                      'email_on_receipt_status_changed' => 0,
                      'email_on_new_track' => 0);
        if (!empty($_POST)) {
            foreach ($_POST as $key => $value) {
                if (in_array($key, array_keys($array)) && is_numeric($value) && $value == 1) {
                    $array[$key] = '1';
                }
            }
            if (isAdmin() || $userData->id == $user->id) {
                $update = $db->where('id', $userData->id)->update(T_USERS, $array);
                if ($update) {
                    $data = [
                        'status' => 200,
                        'message' => "Settings successfully updated!"
                    ];
                }
                else{
                    $data = [
                        'status' => 400,
                        'error' => 'something went wrong'
                    ];
                }
            }
            else{
                $data = [
                    'status' => 400,
                    'error' => 'you are not the profile owner'
                ];
            }
        }
        else{
            $data = [
                    'status' => 400,
                    'error' => 'check your details'
                ];
        }
    }
}
?>