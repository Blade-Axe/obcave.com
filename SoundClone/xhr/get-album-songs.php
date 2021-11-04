<?php 
if (!empty($_REQUEST['id'])) {
	if (is_numeric($_REQUEST['id'])) {
		$getAlbum = $db->where('id', secure($_REQUEST['id']))->getOne(T_ALBUMS);
		if (!empty($getAlbum)) {
		 	$getAlbumSongs = $db->where('album_id', $getAlbum->id)->orderBy('sort_order', 'DESC')->get(T_SONGS, null, ['id']);
		 	$final_ids = [];
		 	if (!empty($getAlbumSongs)) {
		 		foreach ($getAlbumSongs as $key => $song) {
		 			$songData = songData($song->id);
		 			if (!empty($songData)) {
		 				$purchase = 'false';

						if ($songData->price > 0) {
						    if (!isTrackPurchased($songData->id)) {
						        $purchase = 'true';
						        if (IS_LOGGED == true) {
						            if ($user->id == $songData->user_id) {
						                $purchase = 'false';
						            }
						        }
						    }
						}
		 				$final_ids[] = [
						    'songTitle' => $songData->title,
						    'artistName' => $songData->publisher->name,
						    'albumName' => 'Album',
						    'songURL' => ($songData->src == 'radio') ? str_replace($music->config->site_url . '/', '', $songData->audio_location) : $songData->secure_url,
						    'coverURL' => $songData->thumbnail,
						    'songID' => $songData->id,
						    'songAudioID' => $songData->audio_id,
						    'songPageURL' => $songData->url,
						    'duration' => formatSeconds($songData->duration),
						    'songDuration' => $songData->duration,
						    'purchase' => $purchase,
						    'price' => $songData->price,
						    'favorite_button' => getFavButton($songData->id, 'fav-icon'),
						    'is_favoriated' => isFavorated($songData->id),
						    'age' => false,
						    'showDemo' => (!empty($songData->price) && $music->config->ffmpeg_system == 'on' && !empty($songData->demo_track) && !isTrackPurchased($songData->id)) ? 'true' : 'false',
						];

		 				//$final_ids[] = $songData->audio_id;
		 			}
		 		}
		 	    $data['status'] = 200;
		 	    $data['songs'] = array_reverse($final_ids);
		 	}
		}
	}
}
?>