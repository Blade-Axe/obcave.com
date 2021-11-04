<?php
if (!empty($_POST['user_id']) && is_numeric($_POST['user_id']) && $_POST['user_id'] > 0) {
    $user_id = secure($_POST['user_id']);
    // $offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
    $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
    // $offset_text = '';
    // if ($offset > 0) {
    //     $offset_text = ' AND `id` < ' . $offset;
    // }
    // $limit_text = '';
    // if ($limit > 0) {
    //     $limit_text = ' limit ' . $limit;
    // }
    $ids_text = '';
    if (!empty($_POST['ids'])) {
        $ids = secure($_POST['ids']);
        $ids_text = " AND " . T_SONGS . ".id NOT IN ( $ids ) ";
    }
    $having = "";
    if (!empty($_POST['last_view'])) {
        $last_view = secure($_POST['last_view']);
        $having = " HAVING $last_view >= views ";
    }

    
    

    $query = "SELECT " . T_SONGS . ".*, COUNT(" . T_VIEWS . ".id) AS " . T_VIEWS . " FROM " . T_SONGS . " LEFT JOIN " . T_VIEWS . " ON " . T_SONGS . ".id = " . T_VIEWS . ".track_id WHERE ";

    
    $query .=  T_SONGS . ".user_id = " . $user_id.$ids_text;

    if (!IS_LOGGED) {
        $query .= " AND availability = 0";
    } else {
        if ($user->id != $user_id) {
            $query .= " AND availability = 0";
        }
    }
    $query .= " GROUP BY " . T_SONGS . ".id $having ORDER BY " . T_VIEWS . " DESC LIMIT ".$limit;
    

    $getUserSongs = $db->rawQuery($query);

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
