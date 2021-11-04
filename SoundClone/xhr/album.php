<?php
if (IS_LOGGED == false) {
    $data = array(
        'status' => 400,
        'error' => 'Not logged in'
    );
    echo json_encode($data);
    exit();
}
if ($option == 'remove-album-song'){
    if (IS_LOGGED == false) {
        $data['status'] = 300;
    } else {
        if (!empty($_REQUEST['id'])) {
            if (is_numeric($_REQUEST['id'])) {
                $id = secure($_REQUEST['id']);
                $db->where('user_id', $user->id)->where('id', $id)->delete(T_SONGS);
                $data['status'] = 200;
            }
        }
    }
}
if ($option == 'update_album_song_sorting') {
    $album_id = (int)Secure($_GET['album_id']);
    $arrayItems = $_POST['album_song'];
    $order = 0;
    foreach ($arrayItems as $item) {
        $db->where('album_id', $album_id)->where('id', $item)->update(T_SONGS, array('sort_order' => $order));
        $order++;
    }
    $_SESSION['songs'] = array();
    $data = array(
        'status' => 200
    );
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}

if ($option == 'get-albums') {
    if (IS_LOGGED == false) {
        $data['status'] = 300;
    } else {
        if (!empty($_REQUEST['id'])) {
            if (is_numeric($_REQUEST['id'])) {
                $id = secure($_REQUEST['id']);
                $songData = songData($id);
                $html = lang("No playlists found");
                if (!empty($songData)) {
                    $getAlbums = $db->where('user_id', $user->id)->orderBy('id', 'DESC')->get(T_ALBUMS);
                    if (!empty($getAlbums)) {
                        $html = '';
                        foreach ($getAlbums as $key => $album) {
                            $html .= loadPage('albums/add-to-album', [
                                't_thumbnail' => $album->thumbnail,
                                't_id' => $album->id,
                                't_uid' => $album->user_id,
                                't_title' => $album->title,
                            ]);
                        }
                    }
                }
                $data['status'] = 200;
                $data['html'] = loadPage('modals/my-albums', ['t_id' => $id, 'list' => $html]);
            }
        }
    }
}

if ($option == 'add-to-album') {
    if (!empty($_REQUEST['album']) && !empty($_REQUEST['id'])) {
        $_REQUEST['album'] = secure($_REQUEST['album']);
        $songData = songData($_REQUEST['id']);
        if (!empty($songData)) {
            $album = $_REQUEST['album'];
            if (!empty($album) && is_numeric($album)) {
                if ($songData->album_id !== $album) {
                    $last=$db->rawQuery('SELECT MAX(`sort_order`) as SRT FROM `'.T_SONGS.'` WHERE `album_id` = '.$album);
                    $db->where('id', $songData->id)->update(T_SONGS, array('album_id' => $album, 'sort_order' => $last[0]->SRT + 1));
                }
            }
            $data['status'] = 200;
        } else {
            $data['status'] = 300;
        }
    }
}
