<?php
if (IS_LOGGED == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
    echo json_encode($data);
    exit();
}
if (IsAdmin() == false && $option !== 'add_new_blog_article') {
    $data = array(
        'status' => 400,
        'error' => 'Not admin'
    );
    echo json_encode($data);
    exit();
}
if ($option == 'get_users_emails' && isset($_GET['name'])) {
    $name  = Secure($_GET['name']);
    $html  = '';
    $users = $db->rawQuery("SELECT `id`,`username` FROM " . T_USERS . " WHERE `id` <> '".$user->id."' AND `username`  LIKE '%$name%' LIMIT 10");
    $data  = array(
        'status' => 404
    );
    if (count($users) > 0) {
        foreach ($users as $user) {
            $html .= "<p data-user='" . $user->id . "'>" . $user->username . "</p>";
        }
        $data['status'] = 200;
        $data['html']   = $html;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($option == 'ReadNotify') {
    $db->where('recipient_id',0)->where('admin',1)->where('seen',0)->update(T_NOTIFICATION,array('seen' => time()));
}
if ($option == 'search_in_pages') {
    $keyword = Secure($_POST['keyword']);
    $html = '';

    $files = scandir('./admin-panel/pages');
    $not_allowed_files = array('edit-custom-page','edit-lang','edit-movie','edit-profile-field','edit-terms-pages'); 
    foreach ($files as $key => $file) {
        if (file_exists('./admin-panel/pages/'.$file.'/content.html') && !in_array($file, $not_allowed_files)) {
            
            $string = file_get_contents('./admin-panel/pages/'.$file.'/content.html');
            preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches1);

            if (!empty($matches1) && !empty($matches1[2])) {
                foreach ($matches1[2] as $key => $title) {
                    if (strpos(strtolower($title), strtolower($keyword)) !== false) {
                        $page_title = '';
                        preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches3);
                        if (!empty($matches3) && !empty($matches3[2])) {
                            foreach ($matches3[2] as $key => $title2) {
                                $page_title = $title2;
                                break;
                            }
                        }
                        $html .= '<a href="'.LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$title.'</small></div></a>';
                        break;
                    }
                }
            }

            preg_match_all("@(?s)<label([^<]*)>([^<]*)<\/label>@", $string, $matches2);
            if (!empty($matches2) && !empty($matches2[2])) {
                foreach ($matches2[2] as $key => $lable) {
                    if (strpos(strtolower($lable), strtolower($keyword)) !== false) {
                        $page_title = '';
                        preg_match_all("@(?s)<h2([^<]*)>([^<]*)<\/h2>@", $string, $matches3);
                        if (!empty($matches3) && !empty($matches3[2])) {
                            foreach ($matches3[2] as $key => $title2) {
                                $page_title = $title2;
                                break;
                            }
                        }

                        $html .= '<a href="'.LoadAdminLinkSettings($file).'?highlight='.$keyword.'"><div  style="padding: 5px 2px;">'.$page_title.'</div><div><small style="color: #333;">'.$lable.'</small></div></a>';
                        break;
                    }
                }
            }
        }
    }
    $data = array(
                'status' => 200,
                'html'   => $html
            );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete_multi_report') {
    if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('ignore','song','delete'))) {
        foreach ($_POST['ids'] as $key => $value) {
            if (is_numeric($value) && $value > 0) {
                $request_id = Secure($value);
                if ($_POST['type'] == 'delete') {
                    $db->where('id',$request_id)->delete(T_REPORTS);
                }
                elseif ($_POST['type'] == 'ignore') {
                    $db->where('id',$request_id)->update(T_REPORTS,array('ignored'=>1));
                }
                elseif ($_POST['type'] == 'song') {
                    deleteSong($request_id);
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_article') {
    if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('publish','unpublish','delete'))) {
        foreach ($_POST['ids'] as $key => $value) {
            if (is_numeric($value) && $value > 0) {
                $request_id = Secure($value);
                if ($_POST['type'] == 'delete') {
                    $article = GetArticle($request_id);
                    DeleteArticle($request_id, $article['thumbnail']);
                }
                elseif ($_POST['type'] == 'unpublish') {
                    UnPublishArticle($request_id);
                }
                elseif ($_POST['type'] == 'publish') {
                    PublishArticle($request_id);
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_payment') {
    if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('paid','decline','delete'))) {
        foreach ($_POST['ids'] as $key => $value) {
            if (is_numeric($value) && $value > 0) {
                $request_id = Secure($value);
                if ($_POST['type'] == 'delete') {
                    $db->where('id',$request_id)->delete(T_WITHDRAWAL_REQUESTS);
                }
                elseif ($_POST['type'] == 'decline') {
                    $db->where('id',$request_id)->update(T_WITHDRAWAL_REQUESTS,array('status' => 2));
                }
                elseif ($_POST['type'] == 'paid') {
                    $request_data = $db->where('id',$request_id)->getOne(T_WITHDRAWAL_REQUESTS);
                    if (!empty($request_data) && $request_data->status != 1) {
                        $requiring = $db->where('id',$request_data->user_id)->getOne(T_USERS);
                        if (!empty($requiring)) {
                            $db->where('id',$request_data->user_id)->update(T_USERS,array(
                                'balance' => ($requiring->balance -= $request_data->amount)
                            ));
                        }
                    }
                    $db->where('id',$request_id)->update(T_WITHDRAWAL_REQUESTS,array('status' => 1));
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_lang') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (in_array($value, $langs)) {
                $lang_name = Secure($value);
                $t_langs   = T_LANGS;
                $query     = mysqli_query($sqlConnect, "ALTER TABLE `$t_langs` DROP COLUMN `$lang_name`");
                if ($query) {
                    if (file_exists("assets/langs/$lang_name.php")) {
                        unlink("assets/langs/$lang_name.php");
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_blog_category') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            $db->where('lang_key',Secure($value))->delete(T_LANGS);
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'delete_multi_copy_report') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $db->where('id',Secure($value))->delete(T_COPYRIGHTS);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_page') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                DeleteCustomPage(Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_code') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                DeleteAdminInvitation('id', Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_ban') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $table  = T_BANNED_IPS;
                $ban_id = Secure($value);
                $db->where('id',$ban_id)->delete($table);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_ads') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $id = Secure($value);
                $ad = $db->where('id',$id)->getOne(T_USR_ADS);
                $s3 = ($music->config->s3_upload == 'on' || $music->config->ftp_upload = 'on') ? true : false;
                if (!empty($ad)) {
                    if (file_exists($ad->media)) {
                        unlink($ad->media);
                    }
                    else if($s3 === true){
                        PT_DeleteFromToS3($ad->media);
                    }

                    $db->where('id',$id)->delete(T_USR_ADS);
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_field') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                DeleteField(Secure($value));
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_verification') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $verification_id = Secure($value);
                $request = $db->where('id',$verification_id)->getOne(T_ARTIST_R);
                $new_user = $db->where('id',$request->user_id)->getOne(T_USERS);
                @unlink($request->photo);
                @unlink($request->passport);
                $db->where('id',$verification_id)->delete(T_ARTIST_R);
                createNotification([
                    'notifier_id' => $user->id,
                    'recipient_id' => $request->user_id,
                    'type' => 'decline_artist',
                    'track_id' => '',
                    'url' => "user/".$new_user->username
                ]);
                
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_review') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $album = $db->where('id', Secure($value))->getOne(T_REVIEWS);

                $songData = songData($album->track_id);


                if (!empty($album)) {
                    if (isAdmin() || $user->id == $songData->user_id) {
                        $dalbum = $db->where('id', $album->id)->delete(T_REVIEWS);
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_user') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $id = Secure($value);
                DeleteUser($id);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_price') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $id = Secure($value);
                $db->where('id',$id)->delete(T_SONG_PRICE);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_song') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $getSong = songData(Secure($value));
                if (file_exists($getSong->audio_location_original)) {
                    $size = filesize($getSong->audio_location_original);
                    if ($getSong->publisher->uploads > 0) {
                        $update = $db->where('id', $getSong->user_id)->update(T_USERS, ['uploads' => $db->dec($size)]);
                    }
                }
                $deleteSong = deleteSong($value);
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_playlist') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $playlist = $music->playlist = getPlayList(Secure($value));
                if (!empty($playlist)) {
                    if (isAdmin() || $user->id == $playlist->user_id) {
                        $delete = $db->where('id', $playlist->id)->delete(T_PLAYLISTS);
                        $delete = $db->where('playlist_id', $playlist->id)->delete(T_PLAYLIST_SONGS);
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_album') {
    if (!empty($_POST['ids']) && !empty($_POST['type']) && in_array($_POST['type'], array('keep','all'))) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $id = secure($value);
                $album = $db->where('id', $id)->getOne(T_ALBUMS);
                if (!empty($album)) {
                    if (isAdmin() || $user->id == $album->user_id) {
                        $dalbum = $db->where('id', $album->id)->delete(T_ALBUMS);
                        $dalbum = $db->where('album_id', $album->id)->delete(T_VIEWS);
                        PT_DeleteFromToS3($album->thumbnail);
                        @unlink($album->thumbnail);
                        if ($_POST['type'] == 'all') {
                            $getSongs = $db->where('album_id', $album->id)->get(T_SONGS);
                            foreach ($getSongs as $key => $song) {
                                deleteSong($song->id);
                            }
                        } else {
                            $update = $db->where('album_id', $album->id)->update(T_SONGS, ['album_id' => 0, 'price' => $album->price]);
                        }
                        if ($dalbum) {
                            $data['status'] = 200;
                        }
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'remove_multi_category') {
    if (!empty($_POST['ids'])) {
        foreach ($_POST['ids'] as $key => $value) {
            if (!empty($value) && is_numeric($value)) {
                $request     = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
                if ($request > 0){
                    $category = $db->where('id',(int)secure($request))->getOne(T_CATEGORIES,'background_thumb');
                    $category_name = $db->where('id',(int)secure($request))->getOne(T_CATEGORIES,'cateogry_name');
                    if( $category_name->cateogry_name == 'Other'){
                        $data = array(
                            'status' => 400,
                            'error' => 'This category can not be removed, as it is required, but you may change its name if you wish.'
                        );
                        echo json_encode($data);
                        exit();
                    }
                    else{
                        if(!empty($category->background_thumb)){
                            if(file_exists($category->background_thumb)){
                                @unlink($category->background_thumb);
                            }
                        }
                        $get_category_other = $db->where('cateogry_name','Other')->getOne(T_CATEGORIES,'id');
                        $db->where('category_id',(int)secure($request))->update(T_SONGS,array('category_id' => $get_category_other->id));
                        $db->where('id',(int)secure($request))->delete(T_CATEGORIES);
                    }
                }
            }
        }
        $data = ['status' => 200];
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
    }
}
if ($option == 'test_sms_message') {
    $message      = 'This is a test message from ' . $music->config->name;
    $send_message = sendSMSMessage($music->config->sms_phone_number, $message);
    if ($send_message === true) {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = $send_message;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'insert-invitation') {
    $data             = array(
        'status' => 200,
        'html' => ''
    );
    $wo['invitation'] = InsertAdminInvitation();
    if ($wo['invitation'] && is_array($wo['invitation'])) {
        $data['html']   = LoadAdminPage('manage-invitation-keys/list',$wo['invitation']);
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'rm-invitation' && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $data = array(
        'status' => 304
    );
    if (DeleteAdminInvitation('id', $_GET['id'])) {
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete_field') {
    if (!empty($_GET['id'])) {
        $delete = DeleteField($_GET['id']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'edit_field') {
    if (!empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['id'])) {
        $name              = Secure($_POST['name']);
        $description       = Secure($_POST['description']);
        $registration_page = 0;
        if (!empty($_POST['registration_page'])) {
            $registration_page = 1;
        }
        $profile_page = 0;
        if (!empty($_POST['profile_page'])) {
            $profile_page = 1;
        }
        $active = 0;
        if (!empty($_POST['active'])) {
            $active = 1;
        }
        $length = 32;
        if (!empty($_POST['length'])) {
            if (is_numeric($_POST['length'])) {
                $length = Secure($_POST['length']);
            }
        }
        $placement_array = array(
            'profile',
            'general',
            'social',
            'none'
        );
        $placement       = 'profile';
        if (!empty($_POST['placement'])) {
            if (in_array($_POST['placement'], $placement_array)) {
                $placement = Secure($_POST['placement']);
            }
        }
        $data_ = array(
            'name' => $name,
            'description' => $description,
            'length' => $length,
            'placement' => $placement,
            'registration_page' => $registration_page,
            'profile_page' => $profile_page,
            'active' => $active
        );
        if (!empty($_POST['options'])) {
            $options              = @explode("\n", $_POST['options']);
            $data_['type']        = implode($options, ',');
            $data_['select_type'] = 'yes';
        }
        $add = UpdateField($_POST['id'], $data_);
        if ($add) {
            $data['status'] = 200;
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'add_new_field') {
    if (!empty($_POST['name']) && !empty($_POST['type']) && !empty($_POST['description'])) {
        $type              = Secure($_POST['type']);
        $name              = Secure($_POST['name']);
        $description       = Secure($_POST['description']);
        $registration_page = 0;
        if (!empty($_POST['registration_page'])) {
            $registration_page = 1;
        }
        $profile_page = 0;
        if (!empty($_POST['profile_page'])) {
            $profile_page = 1;
        }
        $length = 32;
        if (!empty($_POST['length'])) {
            if (is_numeric($_POST['length']) && $_POST['length'] < 1001) {
                $length = Secure($_POST['length']);
            }
        }
        $placement_array = array(
            'profile',
            'general',
            'social',
            'none'
        );
        $placement       = 'profile';
        if (!empty($_POST['placement'])) {
            if (in_array($_POST['placement'], $placement_array)) {
                $placement = Secure($_POST['placement']);
            }
        }
        $data_ = array(
            'name' => $name,
            'description' => $description,
            'length' => $length,
            'placement' => $placement,
            'registration_page' => $registration_page,
            'profile_page' => $profile_page,
            'active' => 1
        );
        if (!empty($_POST['options'])) {
            $options              = @explode("\n", $_POST['options']);
            $type                 = Secure(implode($options, ','));
            $data_['select_type'] = 'yes';
        }
        $data_['type'] = $type;
        $add           = RegisterNewField($data_);
        if ($add) {
            $data['status'] = 200;
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'send_mail_to_mock_users') {
        $isset_test = 'off';
        $types = array('week','month','3month','6month','9month','year');
        if (empty($_POST['message']) || empty($_POST['subject']) || empty($_POST['send_to']) || !in_array($_POST['send_to'], $types)) {
            $send_errors = 'please check your details';
        } else {
            if (!empty($_POST['test_message'])) {
                if ($_POST['test_message'] == 'on') {
                    $isset_test = 'on';
                }
            }
            if ($isset_test == 'on') {
                $send_message_data = array(
                    'from_email' => $music->config->email,
                    'from_name' => $music->config->name,
                    'reply-to' => $user->email,
                    'to_email' => $user->email,
                    'to_name' => $user->name,
                    'subject' => $_POST['subject'],
                    'charSet' => 'utf-8',
                    'message_body' => $_POST['message'],
                    'is_html' => true
                );
                $send = sendMessage($send_message_data);
            } else {
                $users      = array();
                if (isset($_POST['selected_emails']) && strlen($_POST['selected_emails']) > 0) {
                    $user_ids = explode(',', $_POST['selected_emails']);
                    if (is_array($user_ids) && count($user_ids) > 0) {
                        foreach ($user_ids as $user_id) {
                            $users[] = userData($user_id);
                        }
                    }
                } else {
                    $users = GetUsersByTime($_POST['send_to']);
                }
                RunInBackground(array('status' => 300));
                foreach ($users as $user) {
                    $send_message_data = array(
                        'from_email' => $music->config->email,
                        'from_name' => $music->config->name,
                        'reply-to' => $user->email,
                        'to_email' => $user->email,
                        'to_name' => $user->name,
                        'subject' => $_POST['subject'],
                        'charSet' => 'utf-8',
                        'message_body' => $_POST['message'],
                        'is_html' => true
                    );
                    $send = sendMessage($send_message_data);
                    $mail->ClearAddresses();
                }
            }
        }
        header("Content-type: application/json");
        if (!empty($send_errors)) {
            $send_errors_data = array(
                'status' => 400,
                'message' => $send_errors
            );
            echo json_encode($send_errors_data);
        } else {
            $data = array(
                'status' => 200
            );
            echo json_encode($data);
        }
        exit();
    }
if ($option == 'send_mail_to_all_users') {
    $isset_test = 'off';
    if (empty($_POST['message']) || empty($_POST['subject'])) {
        $send_errors = 'please check your details';
    } else {
        if (!empty($_POST['test_message'])) {
            if ($_POST['test_message'] == 'on') {
                $isset_test = 'on';
            }
        }
        if ($isset_test == 'on') {
            $send_message_data = array(
                'from_email' => $music->config->email,
                'from_name' => $music->config->name,
                'reply-to' => $user->email,
                'to_email' => $user->email,
                'to_name' => $user->name,
                'subject' => $_POST['subject'],
                'charSet' => 'utf-8',
                'message_body' => $_POST['message'],
                'is_html' => true
            );
            $send = sendMessage($send_message_data);
        } else {
            $users_type = 'all';
            $users      = array();
            if (isset($_POST['selected_emails']) && strlen($_POST['selected_emails']) > 0) {
                $user_ids = explode(',', $_POST['selected_emails']);
                if (is_array($user_ids) && count($user_ids) > 0) {
                    foreach ($user_ids as $user_id) {
                        $users[] = userData($user_id);
                    }
                }
            } else if ($_POST['send_to'] == 'active') {
                $users = GetAllUsersByType('active');
            } else if ($_POST['send_to'] == 'inactive') {
                $users = GetAllUsersByType('inactive');
            }
            RunInBackground(array('status' => 300));
            foreach ($users as $user) {
                $send_message_data = array(
                    'from_email' => $music->config->email,
                    'from_name' => $music->config->name,
                    'reply-to' => $user->email,
                    'to_email' => $user->email,
                    'to_name' => $user->name,
                    'subject' => $_POST['subject'],
                    'charSet' => 'utf-8',
                    'message_body' => $_POST['message'],
                    'is_html' => true
                );
                $send = sendMessage($send_message_data);
                $mail->ClearAddresses();
            }
        }
    }
    header("Content-type: application/json");
    if (!empty($send_errors)) {
        $send_errors_data = array(
            'status' => 400,
            'message' => $send_errors
        );
        echo json_encode($send_errors_data);
    } else {
        $data = array(
            'status' => 200
        );
        echo json_encode($data);
    }
    exit();
}
if ($option == 'test_message') {
    $send_message_data = array(
        'from_email' => $music->config->email,
        'from_name' => $music->config->name,
        'reply-to' => $user->email,
        'to_email' => $music->config->email,
        'to_name' => $music->config->name,
        'subject' => 'Test Message From ' . $music->config->name,
        'charSet' => 'utf-8',
        'message_body' => 'If you can see this message, then your SMTP configuration is working fine.',
        'is_html' => false
    );
    $send = sendMessage($send_message_data);
    if ($send === true) {
        $data = array(
            'status' => 200
        );
    } else {
        $data = array(
            'status' => 400,
            'error' => 'Error while sending email.'
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'approve_receipt') {
    if (!empty($_GET['receipt_id'])) {
        $photo_id = Secure($_GET['receipt_id']);
        $receipt = $db->where('id',$photo_id)->getOne(T_BANK_RECEIPTS,array('*'));

        if($receipt){

            $membershipType = 0;
            $amount         = 0;
            $realprice      = $receipt->price;

            $updated = $db->where('id',$photo_id)->update(T_BANK_RECEIPTS,array('approved'=>1,'approved_at'=>time()));
            if ($updated === true) {

                createNotification([
                    'notifier_id' => $user->id,
                    'recipient_id' => $receipt->user_id,
                    'type' => 'approve_receipt',
                    'track_id' => '',
                    'text' => $music->config->currency_symbol . $realprice,
                    'url' => "/#"
                ]);


                if($receipt->mode == 'track'){
                    $trackid = $db->where('audio_id', $receipt->track_id)->getOne(T_SONGS,'id');
                    $songData = songData($trackid->id);
                    $getAdminCommission = $music->config->commission;
                    $final_price = round((($getAdminCommission * $songData->price) / 100), 2);
                    $addPurchase = [
                        'track_id' => $songData->id,
                        'user_id' => $receipt->user_id,
                        'price' => $songData->price,
                        'track_owner_id' => $songData->user_id,
                        'final_price' => $final_price,
                        'commission' => $getAdminCommission,
                        'time' => time()
                    ];
                    $createPayment = $db->insert(T_PURCHAES, $addPurchase);
                    if ($createPayment) {
                        CreatePayment(array(
                            'user_id'   => $receipt->user_id,
                            'amount'    => $final_price,
                            'type'      => 'TRACK',
                            'pro_plan'  => 0,
                            'info'      => $songData->audio_id,
                            'via'       => 'BANK TRANSFER'
                        ));
                        $addUserWallet = $db->where('id', $songData->user_id)->update(T_USERS, ['balance' => $db->inc($final_price)]);
                        $create_notification = createNotification([
                            'notifier_id' => $user->id,
                            'recipient_id' => $songData->user_id,
                            'type' => 'purchased',
                            'track_id' => $songData->id,
                            'url' => "track/$songData->audio_id"
                        ]);
                    }
                } else if($receipt->mode == 'wallet'){
                    $updateUser = $db->where('id', $receipt->user_id)->update(T_USERS, ['wallet' => $db->inc($receipt->price)]);
                    if ($updateUser) {
                        CreatePayment(array(
                            'user_id' => $receipt->user_id,
                            'amount' => $receipt->price,
                            'type' => 'WALLET',
                            'pro_plan' => 0,
                            'info' => $receipt->description,
                            'via' => 'BANK TRANSFER'
                        ));
                    }
                } else if($receipt->mode == 'pro'){

                    $updateUser = $db->where('id', $receipt->user_id)->update(T_USERS, ['is_pro' => 1, 'pro_time' => time()]);
                    if ($updateUser) {
                        $user = userData($receipt->user_id);
                        if ((!empty($_SESSION['ref']) || !empty($user->ref_user_id)) && $music->config->affiliate_type == 1 && $user->referrer == 0) {
                            if ($music->config->amount_percent_ref > 0) {
                                if (!empty($_SESSION['ref'])) {
                                    $ref_user_id = $db->where('username', secure($_SESSION['ref']))->getValue(T_USERS, 'id');
                                }
                                elseif (!empty($user->ref_user_id)) {
                                    $ref_user_id = $db->where('id', $user->ref_user_id)->getValue(T_USERS, 'id');
                                }
                                if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                                    $db->where('id', $user->user_id)->update(T_USERS,array(
                                                                                        'referrer' => $ref_user_id,
                                                                                        'src' => 'Referrer'
                                                                                    ));
                                    $ref_amount     = ($music->config->amount_percent_ref * $music->config->pro_price) / 100;
                                    $db->where('id', $ref_user_id)->update(T_USERS,array('balance' => $db->inc($ref_amount)));
                                    unset($_SESSION['ref']);
                                }
                            } else if ($music->config->amount_ref > 0) {
                                if (!empty($_SESSION['ref'])) {
                                    $ref_user_id = $db->where('username', secure($_SESSION['ref']))->getValue(T_USERS, 'id');
                                }
                                elseif (!empty($user->ref_user_id)) {
                                    $ref_user_id = $db->where('id', $user->ref_user_id)->getValue(T_USERS, 'id');
                                }
                                if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
                                    $db->where('id', $user->user_id)->update(T_USERS,array(
                                                                                        'referrer' => $ref_user_id,
                                                                                        'src' => 'Referrer'
                                                                                    ));
                                    $db->where('id', $ref_user_id)->update(T_USERS,array('balance' => $db->inc($music->config->amount_ref)));
                                    unset($_SESSION['ref']);
                                }
                            }
                        }


                        CreatePayment(array(
                            'user_id' => $receipt->user_id,
                            'amount' => $music->config->pro_price,
                            'type' => 'PRO',
                            'pro_plan' => 1,
                            'info' => '',
                            'via' => 'BANK TRANSFER'
                        ));
                    }
                }
                elseif ($receipt->mode == 'album') {
                    $getAlbum = $db->where('album_id', $receipt->track_id)->getOne(T_ALBUMS);

                    $albumData = albumData($getAlbum->id, true, true, true);
                    if (empty($albumData->price)) {
                        header('Content-Type: application/json');
                        echo json_encode(array(
                            'status' => 400,
                            'message' => 'No Price'
                        ));
                        exit();
                    }

                    $getAdminCommission = $music->config->commission;
                    $final_price = 0;

                    $createPayment = false;
                    foreach ($albumData->songs as $key => $song){
                        $final_price += round((($getAdminCommission * $song->price) / 100), 2);
                        $addPurchase = [
                            'track_id' => $song->id,
                            'user_id' => $receipt->user_id,
                            'price' => $song->price,
                            'track_owner_id' => $song->user_id,
                            'final_price' => round((($getAdminCommission * $song->price) / 100), 2),
                            'commission' => $getAdminCommission,
                            'time' => time()
                        ];

                        $createPayment = $db->insert(T_PURCHAES, $addPurchase);
                        if ($createPayment) {
                            CreatePayment(array(
                                'user_id'   => $receipt->user_id,
                                'amount'    => $final_price,
                                'type'      => 'TRACK',
                                'pro_plan'  => 0,
                                'info'      => $song->audio_id,
                                'via'       => 'Stripe'
                            ));
                            $create_notification = createNotification([
                                'notifier_id' => $receipt->user_id,
                                'recipient_id' => $song->user_id,
                                'type' => 'purchased',
                                'track_id' => $song->id,
                                'url' => "track/$song->audio_id"
                            ]);
                        }
                    }

                    if ($createPayment) {
                        $updatealbumpurchases = $db->where('album_id', $album_id)->update(T_ALBUMS, array('purchases' => $db->inc(1) ));
                        $addUserWallet = $db->where('id', $albumData->user_id)->update(T_USERS, ['balance' => $db->inc($final_price)]);
                        $data = array(
                            'status' => 200
                        );
                    } else {
                        $data = array(
                            'status' => 400,
                            'message' => 'can not create payment'
                        );
                    }

                }

                $data = array(
                    'status' => 200
                );
            }
        }
        $data = array(
            'status' => 200,
            'receipt' => $receipt,
            'addPurchase' => $addPurchase,
            'create_notification' => $create_notification,
            'songData' => $songData,
            'trackid' => $trackid,
            'receipt->track_id' => $receipt->track_id

        );
    }
}
if ($option == 'delete_receipt') {
    if (!empty($_GET['receipt_id'])) {
        $user_id = Secure($_GET['user_id']);
        $photo_id = Secure($_GET['receipt_id']);
        $photo_file = Secure($_GET['receipt_file']);
        createNotification([
            'notifier_id' => $user->id,
            'recipient_id' => $user_id,
            'type' => 'disapprove_receipt',
            'track_id' => '',
            'url' => "/contact"
        ]);
        $deleted = false;
        $db->where('id',$photo_id)->delete(T_BANK_RECEIPTS);
        $deleted = @unlink($photo_file);
        if ($deleted === true) {
            PT_DeleteFromToS3($photo_file);
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete-user-ad') {
    $request = (!empty($_POST['id']) && is_numeric($_POST['id']));

    if ($request === true) {
        $id = $_POST['id'];
        $ad = $db->where('id',$id)->getOne(T_USR_ADS);
        $s3 = ($music->config->s3_upload == 'on' || $music->config->ftp_upload = 'on') ? true : false;
        if (!empty($ad)) {
            if (file_exists($ad->media)) {
                unlink($ad->media);
            }
            else if($s3 === true){
                PT_DeleteFromToS3($ad->media);
            }

            $db->where('id',$id)->delete(T_USR_ADS);
            $data['status'] = 200;
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'add_announcement') {
    $text           = (!empty($_POST['text'])) ? secure($_POST['text']) : "";
    $data['status'] = 400;
    $re_data        = array(
        'text'      => $text,
        'active'    => '1',
        'time'      => time()
    );

    $insert_id          = $db->insert(T_ANNOUNCEMENTS,$re_data);

    if (!empty($insert_id)) {
        $announcement   = $db->where('id',$insert_id)->getOne(T_ANNOUNCEMENTS);
        $data['status'] = 200;
        $data['html']   =  LoadAdminPage("manage-announcements/active",array(
            'ANN_ID'    => $announcement->id,
            'ANN_VIEWS' => 0,
            'ANN_TEXT'  => htmlspecialchars_decode($announcement->text),
            'ANN_TIME'  => Time_Elapsed_String($announcement->time),
        ));
    }
}
if ($option == 'delete-announcement') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $announcement_id = secure($_POST['id']);
        $db->where('id',$announcement_id)->delete(T_ANNOUNCEMENTS);
        $data['status'] = 200;
    }
}
if ($option == 'enable-announcement') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $announcement_id = secure($_POST['id']);
        $db->where('id',$announcement_id)->update(T_ANNOUNCEMENTS,array('active' => '1'));
        $data['status'] = 200;
    }
}
if ($option == 'disable-announcement') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $announcement_id = secure($_POST['id']);
        $db->where('id',$announcement_id)->update(T_ANNOUNCEMENTS,array('active' => '0'));
        $data['status'] = 200;
    }
}
if ($option == 'toggle-announcement') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;

    if ($request === true) {

        $announcement_id    = secure($_POST['id']);
        $announcement       = $db->where('id',$announcement_id)->getOne(T_ANNOUNCEMENTS);
        if (!empty($announcement)) {
            $status         = ($announcement->active == 1) ? '0' : '1';

            $db->where('id',$announcement_id)->update(T_ANNOUNCEMENTS,array('active' => $status));
            $data['status'] = 200;
            echo $status;
            exit();
        }

    }
}
if ($option == 'save-settings') {
    $submit_data = array();
    foreach ($_POST as $key => $settings_to_save) {
        $submit_data[$key] = $settings_to_save;
    }
    $update = false;
    if (!empty($submit_data)) {
        foreach ($submit_data as $key => $value) {
            if ($key == 'theme') {
                $_SESSION['theme'] = secure($value, 0);
            }
            
            $updated_data = secure($value, 0);
            if ($key == 'bank_description') {
                $updated_data = secure($value, 0, false);
            }
            $update = $db->where('name', $key)->update(T_CONFIG, array('value' => secure($value, 0, false)));
            if ($key == 'ftp_upload') {
                if ($value == "on") {
                    if ($music->config->s3_upload == "on") {
                        $update = $db->where('name', 's3_upload')->update(T_CONFIG, array('value' => "off"));
                    }
                }
            }
            if ($key == 's3_upload') {
                if ($value == "on") {
                    if ($music->config->ftp_upload == "on") {
                        $update = $db->where('name', 'ftp_upload')->update(T_CONFIG, array('value' => "off"));
                    }
                }
            }
            if ($key == 'admin_com_sell_videos') {
                if (empty($value) || $value < 0 || !is_numeric($value)) {
                    $update = $db->where('name', $key)->update(T_CONFIG, array('value' => 0));
                }
            }
            if($key == 'queue_count' && (!($value >= 0) || !is_numeric($value))){
                $update = $db->where('name', $key)->update(T_CONFIG, array('value' => 0));
            }
            // if ($key == 'deezer_import' && $value == "on" && empty($music->config->deezer_token) && !empty($submit_data['deezer_app_id']) && !empty($submit_data['deezer_client_secret'])) {
            //     $update = $db->where('name', $key)->update(T_CONFIG, array('value' => 'off'));
            //     $update = $db->where('name', 'deezer_app_id')->update(T_CONFIG, array('value' => secure($submit_data['deezer_app_id'])));
            //     $update = $db->where('name', 'deezer_client_secret')->update(T_CONFIG, array('value' => secure($submit_data['deezer_client_secret'])));
            //     $dialog_url = "https://connect.deezer.com/oauth/auth.php?app_id=".$submit_data['deezer_app_id']."&redirect_uri=".urlencode(LoadAdminLinkSettings('import-settings'))."&perms=offline_access";
            //     header('Content-Type: application/json');
            //     echo json_encode(array('status' => 220,
            //                            'url' => $dialog_url));
            //     exit();
            // }
            
        }
    }
    if ($update) {
        $data = array('status' => 200);
    }
}
// if ($option == 'deezer_code' && !empty($_POST['code'])) {
//     $code = $_POST['code'];
//     $token_url = "https://connect.deezer.com/oauth/access_token.php?app_id=".$music->config->deezer_app_id."&secret=".$music->config->deezer_client_secret."&code=".$code;
 
//     $response  = file_get_contents($token_url);
//     $params    = null;
//     parse_str($response, $params);
//     if (!empty($params['access_token'])) {
//         $update = $db->where('name', 'deezer_token')->update(T_CONFIG, array('value' => secure($params['access_token'])));
//         $update = $db->where('name', "deezer_import")->update(T_CONFIG, array('value' => 'on'));
//         $data = array('status' => 200);
//     }
//     else{
//         $data = array('status' => 400);
//     }
// }
// if ($option == 'deezer_refresh') {
//     $update = $db->where('name', 'deezer_token')->update(T_CONFIG, array('value' => ''));
//     $dialog_url = "https://connect.deezer.com/oauth/auth.php?app_id=".$music->config->deezer_app_id."&redirect_uri=".urlencode(LoadAdminLinkSettings('import-settings'))."&perms=offline_access";
//     $data = array('status' => 200,
//                   'url' => $dialog_url);
// }

if ($option == 'delete-user') {
    if (!empty($_POST['id'])) {
        $delete = DeleteUser(Secure($_POST['id']));
        if ($delete) {
            $data = array('status' => 200);
        }
    }
}
if ($option == 'update-ads') {
    $updated = false;
    foreach ($_POST as $key => $ads) {
        if ($key != 'hash_id') {
            $ad_data = array(
                'code' => htmlspecialchars(base64_decode($ads)),
                'active' => (empty($ads)) ? 0 : 1
            );
            $update = $db->where('placement', Secure($key))->update(T_ADS, $ad_data);
            if ($update) {
                $updated = true;
            }
        }
    }
    if ($updated == true) {
        $data = array(
            'status' => 200
        );
    }
}
if ($option == 'submit-sitemap-settings') {
    if (!file_exists('./sitemaps')) {
        @mkdir('./sitemaps', 0777, true);
    }
    $dom = new DOMDocument();
    $filename = 'sitemaps/sitemap.xml';
    if ($_POST['completed'] == 0) {
        $completed = 0;
        $videos_file_number = (!empty($_POST['videos_file_number'])) ? (int) $_POST['videos_file_number'] : 0;
        $post_file_number = (!empty($_POST['post_file_number'])) ? (int) $_POST['post_file_number'] : 0;
        $album_file_number = (!empty($_POST['album_file_number'])) ? (int) $_POST['album_file_number'] : 0;
        $percentage = (!empty($_POST['percentage'])) ? (int) $_POST['percentage'] : 0;
        $worked = (!empty($_POST['worked'])) ? (int) $_POST['worked'] : 0;
        $total_videos =  $db->getValue(T_SONGS, 'count(*)');
        $total_posts =  $db->getValue(T_USERS, 'count(*)');
        $total_albums =  $db->getValue(T_ALBUMS, 'count(*)');
        $total =  $total_videos + $total_posts + $total_albums;
        if (!empty($_POST['post_offset']) && $_POST['post_offset'] > 0) {
            $post_offset = Secure($_POST['post_offset']);
            $db->where('id',$post_offset,'>');
        }
        $posts   = $db->get(T_USERS,500);
        if (!empty($_POST['videos_offset']) && $_POST['videos_offset'] > 0) {
            $videos_offset = Secure($_POST['videos_offset']);
            $db->where('id',$videos_offset,'>');
        }
        $mysql = $db->get(T_SONGS, 500);

        if (!empty($_POST['album_offset']) && $_POST['album_offset'] > 0) {
            $album_offset = Secure($_POST['album_offset']);
            $db->where('id',$album_offset,'>');
        }
        $album = $db->get(T_ALBUMS, 500);
        $count = count($mysql) + count($posts) + count($album) + $worked;
        $sitemap_numbers = ceil($total_videos / 20000);
        $new_file = false;

        if ($videos_file_number > 1 || $post_file_number > 1 || $album_file_number > 1) {
            $new_file = true;
        }
        if ($percentage == 0) {
            $files = glob('./sitemaps/*');
            foreach($files as $file){
                if(is_file($file))
                    unlink($file);
            }
            for ($i=1; $i <= $sitemap_numbers; $i++) {
                $open_file = fopen("sitemaps/sitemap-" . $i . ".xml", "w");
                $open_file = fopen("sitemaps/sitemap-a-" . $i . ".xml", "w");
                $open_file = fopen("sitemaps/sitemap-b-" . $i . ".xml", "w");
            }
            if (filesize('sitemaps/sitemap-' . $videos_file_number . '.xml') < 1) {
                $write_video_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
            if (filesize('sitemaps/sitemap-a-' . $post_file_number . '.xml') < 1) {
                $write_posts_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
            if (filesize('sitemaps/sitemap-b-' . $post_file_number . '.xml') < 1) {
                $write_albums_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
        }
        else if ($videos_file_number > 1) {
            if (filesize('sitemaps/sitemap-' . $videos_file_number . '.xml') < 1) {
                $write_video_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
            $write_posts_data = file_get_contents('sitemaps/sitemap-a-' . $post_file_number . '.xml');


        }else if ($post_file_number > 1) {
            if (filesize('sitemaps/sitemap-a-' . $post_file_number . '.xml') < 1) {
                $write_posts_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
            $write_video_data = file_get_contents('sitemaps/sitemap-' . $videos_file_number . '.xml');

        }else if ($album_file_number > 1) {
            if (filesize('sitemaps/sitemap-b-' . $album_file_number . '.xml') < 1) {
                $write_albums_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            }
            $write_albums_data = file_get_contents('sitemaps/sitemap-' . $album_file_number . '.xml');

        }  else {
            $write_video_data = file_get_contents('sitemaps/sitemap-' . $videos_file_number . '.xml');
            $write_posts_data = file_get_contents('sitemaps/sitemap-a-' . $post_file_number . '.xml');
            $write_albums_data = file_get_contents('sitemaps/sitemap-b-' . $album_file_number . '.xml');
        }


        if (!empty($mysql)) {
            foreach ($mysql as $key => $question) {
                $write_video_data .= '<url>
                              <loc>' . UrlLink('track/' . $question->audio_id). '</loc>
                              <lastmod>' . date('c', $question->time). '</lastmod>
                              <changefreq>monthly</changefreq>
                              <priority>0.8</priority>
                           </url>' . "\n";
            }
        }
        file_put_contents('sitemaps/sitemap-' . $videos_file_number . '.xml', $write_video_data);



        if (!empty($posts)) {
            foreach ($posts as $key => $user) {
                $write_posts_data .= '<url>
                              <loc>' . UrlLink('user/' . $user->username). '</loc>
                              <lastmod>' . date('c', $user->last_active). '</lastmod>
                              <changefreq>monthly</changefreq>
                              <priority>0.8</priority>
                           </url>' . "\n";
            }
        }
        file_put_contents('sitemaps/sitemap-a-' . $post_file_number . '.xml', $write_posts_data);

        if (!empty($album)) {
            foreach ($album as $key => $user) {
                $write_albums_data .= '<url>
                              <loc>' . UrlLink('album/' . $user->album_id). '</loc>
                              <lastmod>' . date('c', $user->time). '</lastmod>
                              <changefreq>monthly</changefreq>
                              <priority>0.8</priority>
                           </url>' . "\n";
            }
        }
        file_put_contents('sitemaps/sitemap-b-' . $album_file_number . '.xml', $write_albums_data);

        if ($total > 0) {
            $percentage = round(($count * 100)/$total, 2);
        }
        if ($count == $total) {
            $percentage = 100;
        }

        if ($percentage == 100) {
            $write_posts_data .= "\n</urlset>";
            $write_video_data .= "\n</urlset>";
            file_put_contents('sitemaps/sitemap-' . $videos_file_number . '.xml', $write_video_data);
            file_put_contents('sitemaps/sitemap-a-' . $post_file_number . '.xml', $write_posts_data);
            file_put_contents('sitemaps/sitemap-b-' . $album_file_number . '.xml', $write_albums_data);
            $files = glob('./sitemaps/*');
            $write_final_data = '<?xml version="1.0" encoding="UTF-8"?>
                                <sitemapindex  xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" >';
            foreach($files as $file){
                if (is_file($file)) {
                    $write_final_data .= "\n<sitemap>
                                          <loc>" . $music->config->site_url . '/' . str_replace('./', '', $file) . "</loc>
                                          <lastmod>" . date('c') . "</lastmod>
                                        </sitemap>";
                }
            }
            $write_final_data .= '</sitemapindex>';
            $file_final = file_put_contents('sitemap-main.xml', $write_final_data);
            $data['last_created'] = date('d-m-Y');
            $last_created_update =  $update = $db->where('name', 'last_created_sitemap')->update(T_CONFIG, array('value' => Secure($data['last_created'], 0)));
            $completed = 1;
        }

        if (!empty($posts)) {
            $last_post = $posts[count($posts)-1];
            $post_offset = $last_post->id;
        }
        else{
            $post_offset = $_POST['post_offset'];
        }
        if (!empty($mysql)) {
            $last_video = $mysql[count($mysql)-1];
            $videos_offset = $last_video->id;
        }
        else{
            $videos_offset = $_POST['videos_offset'];
        }

        if (!empty($album)) {
            $last_album = $album[count($album)-1];
            $album_offset = $last_album->id;
        }
        else{
            $album_offset = $_POST['album_offset'];
        }

        $worked = count($mysql) + count($posts) + count($album) + $worked;

        if ($total_videos > 20000 && $worked >= 20000 && !empty($mysql) && $percentage < 100) {
            $write_video_data .= "\n</urlset>";
            file_put_contents('sitemaps/sitemap-' . $videos_file_number . '.xml', $write_video_data);
            $videos_file_number = $videos_file_number + 1;
        }
        if ($total_posts > 20000 && $worked >= 20000 && !empty($posts) && $percentage < 100) {
            $write_posts_data .= "\n</urlset>";
            file_put_contents('sitemaps/sitemap-a-' . $post_file_number . '.xml', $write_posts_data);
            $post_file_number = $post_file_number + 1;
        }
        if ($total_albums > 20000 && $worked >= 20000 && !empty($album) && $percentage < 100) {
            $write_albums_data .= "\n</urlset>";
            file_put_contents('sitemaps/sitemap-b-' . $album_file_number . '.xml', $write_albums_data);
            $album_file_number = $album_file_number + 1;
        }
        $data = array('status' => 201, 'post_offset' => $post_offset, 'videos_offset' => $videos_offset , 'album_offset' => $album_offset ,'percentage_full' => $percentage . '%', 'percentage' => $percentage, 'videos_file_number' => $videos_file_number , 'post_file_number' => $post_file_number, 'album_file_number' => $album_file_number, 'worked' => $worked, 'completed' => $completed);

    }
    
}
if ($option == 'save-design') {
    $saveSetting = false;
    if (isset($_FILES['homelogo']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["homelogo"]["tmp_name"],
            'name' => $_FILES['homelogo']['name'],
            'size' => $_FILES["homelogo"]["size"],
            'homelogo' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    if (isset($_FILES['logo']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["logo"]["tmp_name"],
            'name' => $_FILES['logo']['name'],
            'size' => $_FILES["logo"]["size"],
            'logo' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    if (isset($_FILES['light-logo']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["light-logo"]["tmp_name"],
            'name' => $_FILES['light-logo']['name'],
            'size' => $_FILES["light-logo"]["size"],
            'light-logo' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    if (isset($_FILES['favicon']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["favicon"]["tmp_name"],
            'name' => $_FILES['favicon']['name'],
            'size' => $_FILES["favicon"]["size"],
            'favicon' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    $submit_data = array();
    foreach ($_POST as $key => $settings_to_save) {
        $submit_data[$key] = $settings_to_save;
    }
    $update = false;
    if (!empty($submit_data)) {
        foreach ($submit_data as $key => $value) {
            $update = $db->where('name', $key)->update(T_CONFIG, array('value' => Secure($value, 0)));
        }
    }
    if ($update) {
        $data = array('status' => 200);
    }
    $data['status'] = 200;
}
if ($option == 'save-terms') {
    $saveSetting = false;
    foreach ($_POST as $key => $value) {
        if ($key != 'hash_id') {
            $saveSetting = $db->where('type', $key)->update(T_TERMS, array('content' => Secure(base64_decode($value), 0)));
        }
    }
    if ($saveSetting) {
        $data['status'] = 200;
    }
}
if ($option == 'update-question') {
    $error = false;
    if (empty($_POST['question'])) {
        $error = 400;
    }
    else{
        if (!empty($_FILES["image"])) {
            if (!empty($_FILES["image"]["error"])) {
                $error = 404;
            }
            else if (!file_exists($_FILES["image"]["tmp_name"])) {
                $error = 405;
            }
//            else if (file_exists($_FILES["image"]["tmp_name"])) {
//                $image = getimagesize($_FILES["image"]["tmp_name"]);
//                if (!in_array($image[2], array(
//                    IMAGETYPE_GIF,
//                    IMAGETYPE_JPEG,
//                    IMAGETYPE_PNG,
//                    IMAGETYPE_BMP
//                ))){
//                    $error = 405;
//                }
//            }
        }
        else if(empty($_POST['id']) || !is_numeric($_POST['id'])){
            $error = 500;
        }
    }
    if (empty($error)) {
        $insert      = false;
        $active      = (isset($_POST['draft'])) ? '0' : '1';
        $id          = Secure($_POST['id']);
        $update_data = array(
            'question' => Secure($_POST['question']),
            'time' => time(),
            'active' => $active,
        );
        if( isset($_POST['is_anonymously']) ){
            $update_data['is_anonymously'] = Secure($_POST['is_anonymously']);
        }
//        if (!empty($_FILES["image"])) {
//            $file_info   = array(
//                'file' => $_FILES['image']['tmp_name'],
//                'size' => $_FILES['image']['size'],
//                'name' => $_FILES['image']['name'],
//                'type' => $_FILES['image']['type'],
//                'crop' => array(
//                    'width' => 600,
//                    'height' => 400
//                )
//            );
//            $file_upload     = ShareFile($file_info);
//            if (!empty($file_upload['filename'])) {
//                $update_data['image'] = Secure($file_upload['filename']);
//            }
//            else{
//                $error = true;
//            }
//        }
        $insert         = $db->where('id',$id)->update(T_QUESTIONS,$update_data);
        $data['status'] = ($insert && empty($error)) ? 200 : 500;

    }
    else{
        $data['status'] = $error;
    }
}
//if ($option == 'delete-user-ad') {
//    if (!empty($_POST['id'])) {
//        $ad_data = $db->where('id',Secure($_POST['id']))->getOne(T_USER_ADS);
//        if (!empty($ad_data)) {
//            $s3      = ($music->config->s3_upload == 'on' || $music->config->ftp_upload = 'on') ? true : false;
//            if (file_exists($ad_data->ad_media)) {
//                unlink($ad_data->ad_media);
//            }
//
//            else if ($s3 === true) {
//                DeleteFromToS3($ad_data->ad_media);
//            }
//
//            $delete  = $db->where('id',Secure($_POST['id']))->delete(T_USER_ADS);
//            if ($delete) {
//                $data = array('status' => 200);
//            }
//        }
//    }
//}
if ($option == 'backup') {
    $backup = Backup($sql_db_host, $sql_db_user, $sql_db_pass, $sql_db_name);
    if ($backup) {
        $data['status'] = 200;
        $data['date']   = date('d-m-Y');
    }
}
use Aws\S3\S3Client;

if ($option == 'testS3') {
    include_once('assets/import/s3/vendor/autoload.php');
    try {
        $s3Client = S3Client::factory(array(
            'version' => 'latest',
            'region' => $music->config->region,
            'credentials' => array(
                'key' => $music->config->amazone_s3_key,
                'secret' => $music->config->amazone_s3_s_key
            )
        ));

        $buckets  = $s3Client->listBuckets();
        if (!empty($buckets)) {
            if ($s3Client->doesBucketExist($music->config->s3_bucket_name)) {
                $data['status'] = 200;
                $array          = array(
                    'upload/photos/d-cover.jpg',
                    'upload/photos/d-avatar.jpg',
                );
                foreach ($array as $key => $value) {
                    $upload = PT_UploadToS3($value, array(
                        'delete' => 'no'
                    ));
                }
            } 

            else {
                $data['status'] = 300;
            }
        }
        else {
            $data['status'] = 500;
        }
    }

    catch (Exception $e) {
        $data['status']  = 400;
        $data['message'] = $e->getMessage();
    }
}
if ($option == 'test_ftp') {
    include_once('assets/import/ftp/vendor/autoload.php');
    try {
        $ftp = new \FtpClient\FtpClient();
        $ftp->connect($music->config->ftp_host, false, $music->config->ftp_port);
        $login = $ftp->login($music->config->ftp_username, $music->config->ftp_password);
        if (!empty($music->config->ftp_path)) {
            if ($music->config->ftp_path != "./") {
                $ftp->chdir($music->config->ftp_path);
            }
        }
        $array          = array(
            'upload/photos/d-cover.jpg',
            'upload/photos/d-avatar.jpg',
        );
        foreach ($array as $key => $value) {
            $upload = PT_UploadToS3($value, array(
                'delete' => 'no',
            ));
        }
        $data['status'] = 200;
    } catch (Exception $e) {
        $data['status']  = 400;
        $data['message'] = $e->getMessage();
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete-song-price') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $id = Secure($_POST['id']);
        $db->where('id',$id)->delete(T_SONG_PRICE);
        $data['status'] = 200;
    }
}
if ($option == 'update_price') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $id = Secure($_POST['id']);
        $price = Secure($_POST['price']);
        $db->where('id',$id)->update(T_SONG_PRICE,array('price'=>$price));
        $data['status'] = 200;
    }
}
if ($option == 'add_price') {
    $request        = (!empty($_POST['price']) && is_numeric($_POST['price']));
    $data['status'] = 400;
    if ($request === true) {
        $price = Secure($_POST['price']);
        $db->insert(T_SONG_PRICE,array('price'=>$price));
        $data['status'] = 200;
    }
}
if ($option == 'banip' && !empty($_POST['ip'])) {
    $data        = array('status' => 400);
    $request     = filter_var($_POST['ip'], FILTER_VALIDATE_IP);
    if (!empty($request)){
        $table   = T_BANNED_IPS;
        $re_data = array(
            'ip_address' => $_POST['ip'],
            'time'       => time()
        );

        $ban_id  =  $db->insert($table,$re_data);
        $ban_ip  = $db->where('id',$ban_id)->getOne($table);
        
        if (!empty($ban_ip)) {
            $data['status']       = 200;
            $data['html']         = LoadAdminPage("ban-users/list",array(
                'BANNEDIP_ID'     => $ban_ip->id,
                'BANNEDIP_TIME'   => Time_Elapsed_String($ban_ip->time),
                'BANNEDIP_ADDR'   => $ban_ip->ip_address,
            ));
        }
    }
}
if ($option == 'delete_category' && isset($_GET['key'])) {
    $data        = array('status' => 400);
    $request     = filter_var($_GET['key'], FILTER_SANITIZE_NUMBER_INT);
    if ($request > 0){
        $category = $db->where('id',(int)secure($request))->getOne(T_CATEGORIES,'background_thumb');
        $category_name = $db->where('id',(int)secure($request))->getOne(T_CATEGORIES,'cateogry_name');
        if( $category_name->cateogry_name == 'Other'){
            $data = array(
                'status' => 400,
                'error' => 'This category can not be removed, as it is required, but you may change its name if you wish.'
            );
            echo json_encode($data);
            exit();
        }
        if(!empty($category->background_thumb)){
            if(file_exists($category->background_thumb)){
                @unlink($category->background_thumb);
            }
        }
        $get_category_other = $db->where('cateogry_name','Other')->getOne(T_CATEGORIES,'id');
        $db->where('category_id',(int)secure($request))->update(T_SONGS,array('category_id' => $get_category_other->id));
        $db->where('id',(int)secure($request))->delete(T_CATEGORIES);
        $data = array(
            'status' => 200,
            'error' => 'Category deleted successfully'
        );
        echo json_encode($data);
        exit();
    }
}
if ($option == 'unbanip') {
    $data    = array('status' => 400);
    $request = (!empty($_POST['id']) && is_numeric($_POST['id']));
    if (!empty($request)){
        $table  = T_BANNED_IPS;
        $ban_id = Secure($_POST['id']);
        $db->where('id',$ban_id)->delete($table);
        $data['status'] = 200;
    }
}
if ($option == 'save-custom-design-settings') {
    $data     = array('status' => 200);
    $code     = array(); 
    $code[]   = (!empty($_POST['header_js']))  ? $_POST['header_js']  : "";
    $code[]   = (!empty($_POST['footer_js']))  ? $_POST['footer_js']  : "";
    $code[]   = (!empty($_POST['css_styles'])) ? $_POST['css_styles'] : "";
    $errors   = custom_design('save',$code);

    if (!empty($errors)) {
        $data = array('status' => 500,'errors' => $errors);
    }
}
if ($option == 'reset_apps_key') {
    $app_key     = sha1(microtime());
    $data_config = array(
        'apps_api_key' => $app_key
    );

    foreach ($data_config as $name => $value) {
        $db->where('name', $name)->update(T_CONFIG, array('value' => Secure($value, 0)));
    }

    $data['status']  = 200;
    $data['app_key'] = $app_key;
}
if ($option == 'get_lang_key' && !empty($_GET['lang_name']) && !empty($_GET['id'])) {
    $html     = '';
    $lang_key = Secure($_GET['id']);
    $lang_nm  = Secure($_GET['lang_name']);
    $langs    = $db->where('lang_key',$lang_key)->getOne(T_LANGS,array($lang_nm));

    if (!empty($langs)) {
        foreach ($langs as $key => $lang_value) {
            $html .= LoadAdminPage('edit-lang/form-list',array(
                'KEY' => ($key),
                'LANG_KEY' => ucfirst($key),
                'LANG_VALUE' => $lang_value,
            ));
        }
    }

    else {
        $html = "<h4 class='text-center'>Keyword not found</h4>";
    }

    $data['status'] = 200;
    $data['html']   = $html;
}
if ($option == 'get_all_lang_key' && !empty($_GET['id'])) {
    $html     = '';
    $lang_key = Secure($_GET['id']);
    $lang_nm  = Secure($_GET['lang_name']);
    //$langs    = $db->where('lang_key',$lang_key)->getOne(T_LANGS,array($lang_nm));
    $langs    = db_langs();

    if (count($langs) > 0) {
        foreach ($langs as $key => $lang_value) {
            $dt = $db->arrayBuilder()->where('lang_key',$lang_key)->getOne(T_LANGS,array($lang_value));
            $html .= LoadAdminPage('edit-lang/form-list',array(
                'KEY' => $lang_value,
                'LANG_KEY' => ucfirst($lang_value),
                'LANG_VALUE' => $dt[$lang_value],
            ));
        }
    }

    else {
        $html = "<h4 class='text-center'>Keyword not found</h4>";
    }

    $data['status'] = 200;
    $data['html']   = $html;
}
if ($option == 'update_lang_key' && !empty($_POST['id_of_key'])) {
    $up_data   = array(); 
    $id_of_key = Secure($_POST['id_of_key']);

    foreach ($langs as $lang) {
        if (!empty($_POST[$lang])) {
            $up_data[$lang] = Secure($_POST[$lang]);
        }
    }

    $update = $db->where('lang_key',$id_of_key)->update(T_LANGS,$up_data);

    if ($update) {
        $data['status'] = 200;
    }
}
if ($option == 'update_lang_keys') {
    $array_langs = array();
    $lang_key    = Secure($_POST['id_of_key']);
    $langs       = db_langs();
    foreach ($_POST as $key => $value) {
        if (in_array($key, $langs)) {
            $key   = Secure($key);
            $value = Secure($value);
            $query = $db->where('lang_key', $lang_key)->update(T_LANGS, array($key => $value));
            if ($query) {
                $data['status'] = 200;
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'add_new_blog_category') {
    $insert_data = array();
    $insert_data['ref'] = 'blog_categories';
    $add = false;
    foreach (db_langs() as $wo['key_']) {
        if (!empty($_POST[$wo['key_']])) {
            $insert_data[$wo['key_']] = Secure($_POST[$wo['key_']]);
            $add = true;
        }
    }
    if ($add == true) {
        $id = $db->insert(T_LANGS, $insert_data);
        $db->where('id', $id)->update(T_LANGS, array('lang_key' => $id));
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['message'] = 'please check details';
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete_blog_category') {
    header("Content-type: application/json");
    if (!empty($_GET['key'])) {
        $db->where('lang_key',Secure($_GET['key']))->delete(T_LANGS);
        $data['status'] = 200;
    }
    echo json_encode($data);
    exit();
}
if ($option == 'publish_blog_article') {
    if (!empty($_GET['id'])) {
        $delete = PublishArticle($_GET['id']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'unpublish_blog_article') {
    if (!empty($_GET['id'])) {
        $delete = UnPublishArticle($_GET['id']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'add_new_blog_article') {
    if (!empty($_POST['category']) && !empty($_POST['title']) && !empty($_POST['description'])) {
        $category           = Secure($_POST['category']);
        $title              = Secure($_POST['title']);
        $description        = Secure($_POST['description']);
        $tags               = Secure($_POST['tags']);
        $content            = Secure(base64_decode($_POST['content']));

        $media_file = 'upload/photos/d-blog.jpg';
        if (isset($_FILES['thumbnail'])) {
            if (!empty($_FILES['thumbnail']["tmp_name"])) {
                $filename = "";
                $fileInfo = array(
                    'file' => $_FILES["thumbnail"]["tmp_name"],
                    'name' => $_FILES['thumbnail']['name'],
                    'size' => $_FILES["thumbnail"]["size"],
                    'type' => $_FILES["thumbnail"]["type"],
                    'types' => 'jpg,png,gif,jpeg'
                );
                $media = shareFile($fileInfo, 0);
                if (!empty($media)) {
                    $filename = $media['filename'];
                }
                $media_file = Secure($filename);
            }
        }
        $posted = 0;
        if($music->user->admin === 1){
            $posted = 1;
        }
        $data_ = array(
            'title'         => $title,
            'content'       => $content,
            'description'   => $description,
            'category'      => $category,
            'tags'          => $tags,
            'thumbnail'     => $media_file,
            'created_at'    => time(),
            'created_by'    => $music->user->id,
            'posted'		=> $posted
        );
        $add   = RegisterNewBlogPost($data_);
        if ($add) {
            $data['status'] = 200;
            $data['message'] = lang('Article saved successfully');
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'edit_blog_article') {
    if (!empty($_POST['id']) && !empty($_POST['category']) && !empty($_POST['title']) && !empty($_POST['description'])) {

        $id                 = Secure($_POST['id']);
        $category           = Secure($_POST['category']);
        $title              = Secure($_POST['title']);
        $description        = Secure($_POST['description']);
        $tags               = Secure($_POST['tags']);
        $content            = base64_decode($_POST['content']);

        $article            = GetArticle($id);
        $remove_prev_img    = false;
        $old_thumb          = $article['thumbnail'];
        if (isset($_FILES['thumbnail'])) {
            if (!empty($_FILES['thumbnail']["tmp_name"])) {
                $filename = "";
                $fileInfo = array(
                    'file' => $_FILES["thumbnail"]["tmp_name"],
                    'name' => $_FILES['thumbnail']['name'],
                    'size' => $_FILES["thumbnail"]["size"],
                    'type' => $_FILES["thumbnail"]["type"],
                    'types' => 'jpg,png,gif,jpeg'
                );
                $media = shareFile($fileInfo, 0);
                if (!empty($media)) {
                    $filename = $media['filename'];
                    $remove_prev_img    = true;
                }
                $media_file = Secure($filename);
            }
        }else{
            $media_file = $article['thumbnail'];
        }

        $data_ = array(
            'title'         => $title,
            'content'       => $content,
            'description'   => $description,
            'category'      => $category,
            'tags'          => $tags,
            'thumbnail'     => $media_file
        );
        $add   = $db->where('id',$id)->update(T_BLOG, $data_);
        if ($add) {
            if( $old_thumb !== '' && $remove_prev_img == true ) {
                PT_DeleteFromToS3($old_thumb);
            }
            $data['status'] = 200;
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete_blog_article') {
    if (!empty($_GET['id'])) {
        $delete = DeleteArticle($_GET['id'], $_GET['thumbnail']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($option == 'add_new_lang' && !empty($_POST['lang'])) {

    if (in_array(strtolower($_POST['lang']), $langs)) {
        $data['status']  = 400;
    } 

    else {
        $lang_name = Secure($_POST['lang']);
        $lang_name = strtolower($lang_name);
        $t_langs   = T_LANGS;

        $sql       = "
            ALTER TABLE `$t_langs` ADD `$lang_name` 
            TEXT CHARACTER 
            SET utf8 COLLATE utf8_unicode_ci 
            NULL DEFAULT NULL
        ";

        $query       = mysqli_query($sqlConnect,$sql);

        if ($query) {

            $english = get_langs('english');
            $content = file_get_contents('assets/langs/english.php');
            $fp      = fopen("assets/langs/$lang_name.php", "wb");
            fwrite($fp, $content);
            fclose($fp);

            foreach ($english as $key => $lang) {
                mysqli_query($sqlConnect,"UPDATE `$t_langs` SET `{$lang_name}` = '$lang' WHERE `lang_key` = '{$key}'");
            }

            $data['status'] = 200;
        }
    }
}
if ($option == 'add_new_lang_key' && !empty($_POST['lang_key'])) {
    $lang_key  = Secure($_POST['lang_key']);
    $mysqli    = $db->where('lang_key',$lang_key)->getValue(T_LANGS,'count(*)');

    if ($mysqli == 0) {

        $insert_id = $db->insert(T_LANGS,array('lang_key' => $lang_key));

        if ($insert_id) {
            $data['status'] = 200;
            $data['url']    = LoadAdminLinkSettings('manage-languages');
        }
    } 

    else {
        $data['status']  = 400;
    }
}
if ($option == 'delete_lang' && !empty($_GET['id'])) {
    if (in_array($_GET['id'], $langs)) {
        $lang_name = Secure($_GET['id']);
        $t_langs   = T_LANGS;
        $query     = mysqli_query($sqlConnect, "ALTER TABLE `$t_langs` DROP COLUMN `$lang_name`");
        if ($query) {
            if (file_exists("assets/langs/$lang_name.php")) {
                unlink("assets/langs/$lang_name.php");
            }
            $data['status'] = 200;
        }
    }
}
if ($option == 'get_user_ad' && !empty($_POST['id'])) {
    $data['status'] = 400;
    if (is_numeric($_POST['id']) && $_POST['id'] > 0) {
        $id = Secure($_POST['id']);
        $ad = $db->where('id',$id)->getOne(T_USR_ADS);
        if (!empty($ad)) {
            $user_data      = UserData($ad->user_id);
            $data['html']   = LoadAdminPage('manage-user-ads/view',array(
                'ID' => $ad->id,
                'USERNAME' => $user_data->name,
                'USER_AVATAR' => $user_data->avatar,
                'DATE' => date("Y-F-d",$ad->posted),
                'IMG' => GetMedia($ad->media),
            ));
            $data['status'] = 200;
        }
    }
}
if ($option == 'generate_fake_users') {
    require "assets/import/fake-users/vendor/autoload.php";
    $faker = Faker\Factory::create();
    if (empty($_POST['password'])) {
        $_POST['password'] = '123456789';
    }
    $count_users = $_POST['count_users'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $avatar = $_POST['avatar'];
    RunInBackground(array('status' => 200));
    for ($i=0; $i < $count_users; $i++) {
        $genders = array("male", "female");
        $random_keys = array_rand($genders, 1);
        $gender = array_rand(array("male", "female"), 1);
        $gender = $genders[$random_keys];
        $re_data  = array(
            'email' => Secure(str_replace(".", "_", $faker->userName) . '_' . rand(111, 999) . "@yahoo.com", 0),
            'username' => Secure($faker->userName . '_' . rand(111, 999), 0),
            'password' => Secure($password, 0),
            'email_code' => Secure(md5($faker->userName . '_' . rand(111, 999)), 0),
            'src' => 'Fake',
            'gender' => Secure($gender),
            'last_active' => time(),
            'active' => 1,
            'time' => time()
            //'registered' => date('Y') . '/' . intval(date('m')),
            //'first_name' => $faker->firstName($gender),
            //'last_name' => $faker->lastName
        );
        if ($avatar == 1) {
            $re_data['avatar'] = ImportImageFromFile($faker->imageUrl(150, 150));
        }
        $add_user = $db->insert(T_USERS, $re_data);
    }

    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'notifications-get-users') {
    $data  = array(
        'status' => 404,
        'html' => ''
    );
    $html  = '';
    $users = GetUsersByName($_POST['name']);
    if ($users && count($users) > 0) {
        foreach ($users as $key) {
            $html .= LoadAdminPage('mass-notifications/list',['NOTIFICATION_DATA' => $key ]);
        }
        $data['status'] = 200;
        $data['html']   = $html;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'notifications-send') {
    $data  = array(
        'status' => 304,
        'message' => 'please check details'
    );
    $error = false;
    $users = array();
    if (!isset($_POST['url']) || !isset($_POST['description'])) {
        $error = true;
    } else {
        if (!filter_var($_POST['url'], FILTER_VALIDATE_URL)) {
            $error = true;
        }
        if (strlen($_POST['description']) < 5 || strlen($_POST['description']) > 300) {
            $error = true;
        }
    }
    if (!$error) {
        if (empty($_POST['notifc-users'])) {
            $users = GetUserIds();
        } elseif ($_POST['notifc-users'] && strlen($_POST['notifc-users']) > 0) {
            $users = explode(',', $_POST['notifc-users']);
        }
        $url               = Secure($_POST['url']);
        $message           = Secure($_POST['description']);
        $registration_data = array(
            'full_link' => $url,
            'text' => $message,
            'recipients' => $users
        );
        if (RegisterAdminNotification($registration_data)) {
            $data = array(
                'status' => 200,
                'message' => 'notification sent'
            );
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'auto_friend') {
    header("Content-type: application/json");
    if (!empty($_GET['users'])) {
        $save = $db->where('name', 'auto_friend_users')->update(T_CONFIG, array('value' => Secure($_GET['users'], 0)));
        if ($save) {
            $data['status'] = 200;
        }
    }
    echo json_encode($data);
    exit();
}
if ($option == 'import_upload_enable') {
    if (!empty($_GET['key'])) {
        $db->where('id',Secure($_GET['key']))->update(T_USERS, array('upload_import' => 1));
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'import_upload_disable') {
    if (!empty($_GET['key'])) {
        $db->where('id',Secure($_GET['key']))->update(T_USERS, array('upload_import' => 0));
        $data['status'] = 200;
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete-questions') {
    if (!empty($_POST['id'])) {
        $delete = DeleteQuestion(Secure($_POST['id']));
        if ($delete) {
            $data = array('status' => 200);
        }
    }
}
if ($option == 'add_followers') {
    $data           = array();
    $data['status'] = 200;
    $data['error']  = false;
    if (empty($_POST['followers']) || empty($_POST['user_id'])) {
        $data['status'] = 500;
        $data['error']  = __('please_check_details');
    }
    if (!is_numeric($_POST['followers']) || !is_numeric($_POST['user_id'])) {
        $data['status'] = 500;
        $data['error']  = 'Numbers only are allowed';
    }
    if ($_POST['followers'] < 0 || $_POST['user_id'] < 0) {
        $data['status'] = 500;
        $data['error']  = 'Integer numbers only are allowed';
    }
    $userData = UserData($_POST['user_id']);
    if (empty($data['error']) && $data['status'] != 500) {
        $followers = floor($_POST['followers']);
        $usersCount = $db->getValue(T_USERS, 'COUNT(*)');
        if ($followers > $usersCount) {
            $data['status'] = 500;
            $data['error']  = "Followers can't be more than your users: $usersCount";
        }
        if ($db->getValue(T_USERS, "MAX(id)") <= $userData->last_follow_id) {
            $data['status'] = 500;
            $data['error']  = "No more users left to follow, all the users are following {$userData->name}.";
        }
    }
    if (empty($data['error']) && $data['error'] != 500) {
        $users_id = array();

        $users = $db->where('id', $userData->last_follow_id, ">")->get(T_USERS, $followers, 'id');
        foreach ($users as $key => $i) {
            $users_id[] = $i->id;
        }
        if (empty($data['error']) && $data['status'] != 500 && !empty($users_id)) {
            RunInBackground(array(
                'status' => 200
            ));
            $followed  = RegisterFollow($_POST['user_id'], $users_id);
            $update_user = $db->where('id', $_POST['user_id'])->update(T_USERS, array("last_follow_id" => Secure(end($users_id))));
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if ($option == 'delete-reports') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $report_id = Secure($_POST['id']);
        $db->where('id',$report_id)->delete(T_REPORTS);
        $data['status'] = 200;
    }
}
if ($option == 'get_category') {
    $request        = (!empty($_GET['id']) && is_numeric($_GET['id']));
    $data['status'] = 400;
    if ($request === true) {
        $category_data = $db->arrayBuilder()->where('id', secure($_GET['id']))->getOne(T_CATEGORIES);
        $category_data['lang'] = $db->arrayBuilder()->where('lang_key', 'cateogry_' . secure($_GET['id']))->getOne(T_LANGS);
        $data['status'] = 200;
        $data['message'] = 'Success';
        $data['html'] = LoadAdminPage('manage-categories/model', $category_data);
    }
}
if ($option == 'add_new_category') {
    $request        = (!empty($_POST['english']) && isset($_POST['english']));
    $data['status'] = 400;
    if ($request === true) {
        $cateogry_name = secure($_POST['english']);
        $color = (isset($_POST['favcolor'])) ? secure($_POST['favcolor']) : '#333';
        $img_path = '';
        if (!empty($_FILES)) {
            if (!empty($_FILES['bg_img']['tmp_name'])) {
                $file_info = array(
                    'file' => $_FILES['bg_img']['tmp_name'],
                    'size' => $_FILES['bg_img']['size'],
                    'name' => $_FILES['bg_img']['name'],
                    'type' => $_FILES['bg_img']['type'],
                    'crop' => array('width' => 400, 'height' => 400),
                    'allowed' => 'jpg,png,jpeg,gif'
                );
                $file_upload = shareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $img_path = $file_upload['filename'];
                }
            }
        }
        $categoryid = $db->insert(T_CATEGORIES, array('cateogry_name' => $cateogry_name, 'color' => $color, 'time' => time(), 'background_thumb' => $img_path));
        unset($_POST['favcolor']);
        unset($_POST['bg_img']);
        unset($_POST['hash_id']);

        $lang_data = $_POST;
        $lang_data['lang_key'] = 'cateogry_' . $categoryid;
        $lang_data['options'] = '';
        $db->insert(T_LANGS, $lang_data);

        $data['status'] = 200;
        $data['message'] = 'Success';
    }
}
if ($option == 'update_category') {
    $request        = (!empty($_POST['id_of_key']) && is_numeric($_POST['id_of_key']));
    if(!isset($_POST['english']) || !isset($_POST['favcolor'])){
        $request = false;
    }
    $update = [];
    $data['status'] = 400;
    if ($request === true) {
        $cateogry_id = secure($_POST['id_of_key']);
        $category_name = $db->where('id',(int)secure($cateogry_id))->getOne(T_CATEGORIES,'cateogry_name');
        if( $category_name->cateogry_name == 'Other'){
            $update['cateogry_name'] = 'Other';
        }else {
            $update['cateogry_name'] = secure($_POST['english']);
        }
        $update['color'] = secure($_POST['favcolor']);
        $img_path = '';
        if (!empty($_FILES)) {
            if (!empty($_FILES['bg_img']['tmp_name'])) {
                $file_info = array(
                    'file' => $_FILES['bg_img']['tmp_name'],
                    'size' => $_FILES['bg_img']['size'],
                    'name' => $_FILES['bg_img']['name'],
                    'type' => $_FILES['bg_img']['type'],
                    'crop' => array('width' => 400, 'height' => 400),
                    'allowed' => 'jpg,png,jpeg,gif'
                );
                $file_upload = shareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $update['background_thumb'] = $file_upload['filename'];
                }
            }
        }
        $categoryid = $db->where('id',$cateogry_id)->update(T_CATEGORIES, $update);
        unset($_POST['bg_img']);
        unset($_POST['favcolor']);
        unset($_POST['hash_id']);
        unset($_POST['id_of_key']);

        $lang_data = $_POST;
        //$lang_data['lang_key'] = 'cateogry_' . $cateogry_id;
        $db->where('lang_key', 'cateogry_' . $cateogry_id)->update(T_LANGS, $lang_data);

        $data['status'] = 200;
        $data['message'] = 'Success';
    }
}
if ($option == 'delete_verification') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $report_id = Secure($_POST['id']);
        $username = Secure($_POST['username']);
        $verification_id = Secure($_POST['verification_id']);
        $db->where('id',$verification_id)->delete(T_ARTIST_R);
        createNotification([
            'notifier_id' => $user->id,
            'recipient_id' => $report_id,
            'type' => 'decline_artist',
            'track_id' => '',
            'url' => "user/".$username
        ]);
        $request = $db->where('id',$verification_id)->getOne(T_ARTIST_R,'photo,passport');
        @unlink($request->photo);
        @unlink($request->passport);

        $data['status'] = 200;
    }
}
if ($option == 'update-ads') {
    $updated = false;
    foreach ($_POST as $key => $ads) {
        if ($key != 'hash_id') {
            $ad_data = array(
                'code' => htmlspecialchars(base64_decode($ads)),
                'active' => (empty($ads)) ? 0 : 1
            );
            $update = $db->where('placement', Secure($key))->update(T_ADS, $ad_data);
            if ($update) {
                $updated = true;
            }
        }
    }
    if ($updated == true) {
        $data = array(
            'status' => 200
        );
    }
}
if ($option == 'verify_user') {
    $uid        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $request        = (!empty($_POST['verification_id']) && is_numeric($_POST['verification_id']));
    $data['status'] = 400;
    if ($request === true) {
        $id = Secure($_POST['id']);
        $username = Secure($_POST['username']);
        $verification_id = Secure($_POST['verification_id']);
        $db->where('id',$id)->update(T_USERS,array('artist'=>1));
        $db->where('id',$verification_id)->delete(T_ARTIST_R);
        $not = createNotification([
            'notifier_id' => $user->id,
            'recipient_id' => $id,
            'type' => 'approved_artist',
            'track_id' => '',
            'url' => "user/".$username
        ]);
        $data['not'] = $not;
        $request = $db->where('id',$verification_id)->getOne(T_ARTIST_R,'photo,passport');
        @unlink($request->photo);
        @unlink($request->passport);

        $data['status'] = 200;
    }
}
if ($option == 'save-design') {
    $saveSetting = false;
    if (isset($_FILES['logo']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["logo"]["tmp_name"],
            'name' => $_FILES['logo']['name'],
            'size' => $_FILES["logo"]["size"]
        );
        $media    = UploadLogo($fileInfo);
    }
    if (isset($_FILES['light-logo']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["light-logo"]["tmp_name"],
            'name' => $_FILES['light-logo']['name'],
            'size' => $_FILES["light-logo"]["size"],
            'light-logo' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    if (isset($_FILES['favicon']['name'])) {
        $fileInfo = array(
            'file' => $_FILES["favicon"]["tmp_name"],
            'name' => $_FILES['favicon']['name'],
            'size' => $_FILES["favicon"]["size"],
            'favicon' => true
        );
        $media    = UploadLogo($fileInfo);
    }
    $submit_data = array();
    foreach ($_POST as $key => $settings_to_save) {
        $submit_data[$key] = $settings_to_save;
    }
    $update = false;
    if (!empty($submit_data)) {
        foreach ($submit_data as $key => $value) {
            $update = $db->where('name', $key)->update(T_CONFIG, array('value' => secure($value, 0)));
        }
    }
    if ($update) {
        $data = array('status' => 200);
    }
    $data['status'] = 200;
}
if ($option == 'delete-copyrights') {
    $request        = (!empty($_POST['id']) && is_numeric($_POST['id']));
    $data['status'] = 400;
    if ($request === true) {
        $copyright_id = Secure($_POST['id']);
        $db->where('id',$copyright_id)->delete(T_COPYRIGHTS);
        $data['status'] = 200;
    }
}
if ($option == 'withdrawal-requests' && !empty($_POST['id']) && !empty($_POST['a'])) {
    $request = (is_numeric($_POST['id']) && is_numeric($_POST['a']) && in_array($_POST['a'], array(1,2,3)));

    if ($request === true) {
        $request_id = Secure($_POST['id']);
        if ($_POST['a'] == 1) {
            $request_data = $db->where('id',$request_id)->getOne(T_WITHDRAWAL_REQUESTS);
            if (!empty($request_data) && $request_data->status != 1) {
                $requiring = $db->where('id',$request_data->user_id)->getOne(T_USERS);
                if (!empty($requiring)) {
                    $db->where('id',$request_data->user_id)->update(T_USERS,array(
                        'balance' => ($requiring->balance -= $request_data->amount)
                    ));
                }
            }
            $db->where('id',$request_id)->update(T_WITHDRAWAL_REQUESTS,array('status' => 1));
        }
        else if ($_POST['a'] == 2) {
            $db->where('id',$request_id)->update(T_WITHDRAWAL_REQUESTS,array('status' => 2));
        }
        else if ($_POST['a'] == 3) {
            $db->where('id',$request_id)->delete(T_WITHDRAWAL_REQUESTS);
        }
        $data['status'] = 200;
    }
}
if ($option == 'ignore-report') {
    $request        = (!empty($_GET['id']) && is_numeric($_GET['id']));
    $data['status'] = 400;
    if ($request === true) {
        $id = Secure($_GET['id']);
        $db->where('id',$id)->update(T_REPORTS,array('ignored'=>1));
        $data['status'] = 200;
    }
}
if ($option == 'add_new_page') {
    if (!empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
        $page_name    = Secure($_POST['page_name']);
        $page_content = Secure(str_replace(array("\r","\n"),"",$_POST['page_content']));
        $page_title   = Secure($_POST['page_title']);
        $page_type    = 0;
        if (!empty($_POST['page_type'])) {
            $page_type = 1;
        }
        if (!preg_match('/^[\w]+$/', $page_name)) {
            $data = array(
                'status' => 400,
                'message' => 'Invalid page name characters'
            );
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
        $data_ = array(
            'page_name' => $page_name,
            'page_content' => $page_content,
            'page_title' => $page_title,
            'page_type' => $page_type
        );
        $add   = RegisterNewPage($data_);
        if ($add) {
            $data['status'] = 200;
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
}
if ($option == 'delete_page') {
    if (!empty($_POST['id'])) {
        $delete = DeleteCustomPage($_POST['id']);
        if ($delete) {
            $data = array(
                'status' => 200
            );
        }
    }
}
if ($option == 'edit_page') {
    if (!empty($_POST['page_id']) && !empty($_POST['page_name']) && !empty($_POST['page_content']) && !empty($_POST['page_title'])) {
        $page_name    = $_POST['page_name'];
        $page_content = $_POST['page_content'];
        $page_title   = $_POST['page_title'];
        $page_type    = 0;
        if (!empty($_POST['page_type'])) {
            $page_type = 1;
        }
        if (!preg_match('/^[\w]+$/', $page_name)) {
            $data = array(
                'status' => 400,
                'message' => 'Invalid page name characters'
            );
            header("Content-type: application/json");
            echo json_encode($data);
            exit();
        }
        $data_ = array(
            'page_name' => $page_name,
            'page_content' => $page_content,
            'page_title' => $page_title,
            'page_type' => $page_type
        );
        $add   = UpdateCustomPageData($_POST['page_id'], $data_);
        if ($add) {
            $data['status'] = 200;
        }
    } else {
        $data = array(
            'status' => 400,
            'message' => 'Please fill all the required fields'
        );
    }
}