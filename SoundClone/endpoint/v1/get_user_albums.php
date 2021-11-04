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
    $sql = 'SELECT * FROM `'.T_ALBUMS.'` WHERE `user_id` = "'.$user_id.'" '.$offset_text.' ORDER BY `id` DESC '.$limit_text;
    $getAlbums            = $db->objectBuilder()->rawQuery($sql);
    $albums_data = array();

    if (!empty($getAlbums)) {

        foreach ($getAlbums as $key => $album) {
            $album->url = getLink("album/$album->album_id");
            $album->thumbnail = getMedia($album->thumbnail);
            $album->user_data = userData($album->user_id);
            unset($album->user_data->password);
            $album->songs_count = $db->where('album_id', $album->id)->getValue(T_SONGS, 'COUNT(*)');
            $albums_data[] = $album;
        }
    }
    $data['status'] = 200;
    $data['data'] = $albums_data;
}
else{
    $data = [
        'status' => 400,
        'error' => 'user_id can not be empty'
    ];
}
