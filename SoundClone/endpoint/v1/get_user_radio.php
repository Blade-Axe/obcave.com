<?php
if (!empty($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
    $user_id = secure($_POST['user_id']);
    $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
    $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
    $offset_text = '';
    if ($offset > 0) {
        $offset_text = ' AND `id` < ' . $offset;
    }
    $limit_text = '';
    if ($limit > 0) {
        $limit_text = ' limit ' . $limit;
    }
    $sql = 'SELECT * FROM `'.T_SONGS.'` WHERE `src` = "radio" AND `user_id` = "'.$user_id.'" '.$offset_text.' ORDER BY `id` DESC '.$limit_text;
    $getUserSongs            = $db->objectBuilder()->rawQuery($sql);
    $songs_data = array();

    if (!empty($getUserSongs)) {

        foreach ($getUserSongs as $key => $song) {
            $userSong = songData($song->id);
            unset($userSong->publisher->password);
            foreach ($userSong->songArray as $key => $value) {
                unset($userSong->songArray->{$key}->USER_DATA->password);
            }
            $songs_data[] = $userSong;
        }
    }
    $data['status'] = 200;
    $data['data'] = $songs_data;
}
else{
    $data = [
        'status' => 400,
        'error' => 'user_id can not be empty'
    ];
}
