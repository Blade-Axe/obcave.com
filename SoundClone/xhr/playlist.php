<?php 
if ($option == 'remove-playlist-song'){
    if (IS_LOGGED == false) {
        $data['status'] = 300;
    } else {
        if (!empty($_REQUEST['id'])) {
            if (is_numeric($_REQUEST['id'])) {
                $id = secure($_REQUEST['id']);
                $db->where('user_id', $user->id)->where('id', $id)->delete(T_PLAYLIST_SONGS);
                $data['status'] = 200;
            }
        }
    }
}
if ($option == 'get-playlists') {
	if (IS_LOGGED == false) {
		$data['status'] = 300;
	} else {
		if (!empty($_REQUEST['id'])) {
			if (is_numeric($_REQUEST['id'])) {
				$id = secure($_REQUEST['id']);
				$songData = songData($id);
				$html = lang("No playlists found");
				if (!empty($songData)) {
					$getPlayLists = $db->where('user_id', $user->id)->orderBy('id', 'DESC')->get(T_PLAYLISTS);
					if (!empty($getPlayLists)) {
						$html = '';
						foreach ($getPlayLists as $key => $playlist) {
							$playlist = getPlayList($playlist, false);
							$html .= loadPage('playlists/add-to-list', [
								't_thumbnail' => $playlist->thumbnail_ready,
								't_id' => $playlist->id,
								't_uid' => $playlist->uid,
								't_title' => $playlist->name,
							]);
						}
					}
				}
				$data['status'] = 200;
				$data['html'] = loadPage('modals/my-playlists', ['t_id' => $id, 'list' => $html]);
			}
		}
	}
}

if ($option == 'add-to-playlist') {
	if (!empty($_REQUEST['playlists']) && !empty($_REQUEST['id'])) {
		$_REQUEST['playlists'] = secure($_REQUEST['playlists']);
		$songData = songData($_REQUEST['id']);
		if (!empty($songData)) {
			$explodePlaylistIDS = explode(',', $_REQUEST['playlists']);
			if (!empty($explodePlaylistIDS)) {
				foreach ($explodePlaylistIDS as $key => $playlist) {
					if (!empty($playlist) && is_numeric($playlist)) {
						$playlist = $music->playlist = getPlayList($playlist);
						$checkIfSongInPlayList = $db->where('track_id', $songData->id)->where('playlist_id', $playlist->id)->getValue(T_PLAYLIST_SONGS, 'count(*)');
						if (empty($checkIfSongInPlayList)) {
							$addSong = [
								'track_id' => $songData->id,
								'user_id' => $user->id,
								'time' => time(),
								'playlist_id' => $playlist->id
							];
							$insert = $db->insert(T_PLAYLIST_SONGS, $addSong);
						}
					}
				}
                RecordUserActivities('add_to_playlist',array('song_data' => $songData));
                $data['status'] = 200;
			} else {
				$data['status'] = 300;
			}
		}
	}
}


if ($option == 'create') {
	if (IS_LOGGED == false) {
		exit();
	}
	if (!empty($_POST)) {
	    if (empty($_FILES['avatar']) || empty($_POST['name']) || !isset($_POST['privacy'])) {
	        $errors[] = lang("Please check your details");
	    } else {
	        $name        = secure($_POST['name']);
	        $privacy     = secure($_POST['privacy']);
	        $file_info   = array(
		        'file' => $_FILES['avatar']['tmp_name'],
		        'size' => $_FILES['avatar']['size'],
		        'name' => $_FILES['avatar']['name'],
		        'type' => $_FILES['avatar']['type'],
		        'crop' => array(
		            'width' => 600,
		            'height' => 600
		        )
		    );
		    $thumbnail = '';
		    $file_upload = shareFile($file_info);
		    if (!empty($file_upload['filename'])) {
		        $thumbnail = secure($file_upload['filename'], 0);
		    }
		    if (empty($thumbnail)) {
		    	$errors[] = lang("Error found while uploading the playlist avatar, Please try again later.");
		    }
		    $privacy = 0;
		    if (isset($_POST['privacy'])) {
	            if (in_array($_POST['privacy'], array(0, 1))) {
	                $privacy = secure($_POST['privacy']);
	            }
	        }
	        if (empty($errors)) {
	        	$uid = generateKey(12, 12);
	           	$create_playList = [
	           		'uid' => $uid,
	           		'name' => $name,
	           		'user_id' => $user->id,
	           		'privacy' => $privacy,
	           		'thumbnail' => $thumbnail,
	           		'time' => time()
	           	];
	           	$create = $db->insert(T_PLAYLISTS, $create_playList);
	           	if ($create) {
                    RecordUserActivities('create_new_playlist',array('playlist' => $create_playList));
	           		$data['status'] = 200;
	           	}
	        }
	    }
	}
}

if ($option == 'get-edit-form') {
	if (!empty($_REQUEST['id'])) {
		if (is_numeric($_REQUEST['id'])) {
			$playlist = $music->playlist = getPlayList($_REQUEST['id']);
			if (!empty($playlist)) {
			 	$data = [
			 		'status' => 200,
			 		'html' => loadPage('modals/edit-playlist', [
			 			't_thumbnail' => $playlist->thumbnail_ready,
						't_id' => $playlist->id,
						't_title' => $playlist->name,
			 		])
			 	];
			}
		}
	}
}

if ($option == 'get-share-modal') {
	if (!empty($_REQUEST['id'])) {
		if (is_numeric($_REQUEST['id'])) {
			$playlist = $music->playlist = getPlayList($_REQUEST['id']);
			if (!empty($playlist)) {
			 	$data = [
			 		'status' => 200,
			 		'html' => loadPage('modals/share-playlist', [
			 			't_thumbnail' => $playlist->thumbnail_ready,
						't_id' => $playlist->id,
						's_artist' => $playlist->publisher->name,
						't_uid' => $playlist->uid,
						't_title' => $playlist->name,
						't_privacy' => $playlist->privacy_text,
						't_url' => urlencode($playlist->url),
						't_url_original' => $playlist->url,
						't_songs' => $playlist->songs,
			 		])
			 	];
			}
		}
	}
}

if ($option == 'get-playlist-songs') {
	if (!empty($_REQUEST['id'])) {
		if (is_numeric($_REQUEST['id'])) {
			$playlist = $music->playlist = getPlayList($_REQUEST['id']);
			if (!empty($playlist)) {
			 	$getPlaylistSongs = $db->where('playlist_id', $playlist->id)->orderBy('id','ASC')->get(T_PLAYLIST_SONGS, null, ['track_id']);
			 	$final_ids = [];
			 	if (!empty($getPlaylistSongs)) {
			 		foreach ($getPlaylistSongs as $key => $song) {
			 			$songData = songData($song->track_id);
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
			 	    $data['songs'] = $final_ids;
			 	}
			}
		}
	}
}

if ($option == 'delete-playlist') {
	if (IS_LOGGED == false) {
		exit();
	}
	if (!empty($_REQUEST['id'])) {
		if (is_numeric($_REQUEST['id'])) {
			$playlist = $music->playlist = getPlayList($_REQUEST['id']);
			if (!empty($playlist)) {
			 	if (isAdmin() || $user->id == $playlist->user_id) {
	           		$delete = $db->where('id', $playlist->id)->delete(T_PLAYLISTS);
	           		$delete = $db->where('playlist_id', $playlist->id)->delete(T_PLAYLIST_SONGS);
		           	if ($delete) {
		           		$data['status'] = 200;
		           	}
	           	}
			}
		}
	}
}

if ($option == 'delete-playlist-song') {
    if (IS_LOGGED == false) {
        exit();
    }
    if (!empty($_REQUEST['id'])) {
        if (is_numeric($_REQUEST['id'])) {
            $playlist = getPlayListSong($_REQUEST['id']);
            if ($playlist) {
                $delete = $db->where('id', $_REQUEST['id'])->delete(T_PLAYLIST_SONGS);
                if ($delete) {
                    $data['status'] = 200;
                }
            }
        }
    }
}

if ($option == 'update') {
	if (IS_LOGGED == false) {
		exit();
	}
	if (!empty($_REQUEST['id'])) {
		if (is_numeric($_REQUEST['id'])) {
			$playlist = $music->playlist = getPlayList($_REQUEST['id']);
			if (!empty($playlist)) {
			 	if (empty($_POST['name']) || !isset($_POST['privacy'])) {
			        $errors[] = lang("Please check your details");
			    } else {
			    	$thumbnail   = $playlist->thumbnail;
			        $name        = secure($_POST['name']);
			        $privacy     = secure($_POST['privacy']);
			        if (!empty($_FILES['avatar'])) {
			        	$file_info   = array(
					        'file' => $_FILES['avatar']['tmp_name'],
					        'size' => $_FILES['avatar']['size'],
					        'name' => $_FILES['avatar']['name'],
					        'type' => $_FILES['avatar']['type'],
					        'crop' => array(
					            'width' => 600,
					            'height' => 600
					        )
					    );
					    $file_upload = shareFile($file_info);
					    if (!empty($file_upload['filename'])) {
					        $thumbnail = secure($file_upload['filename'], 0);
					    }
			        }
			        if (empty($thumbnail)) {
			        	$errors[] = lang("Error found while updating the playlist avatar, Please try again later.");
			        }
			        if ($music->config->s3_upload != 'on' && $music->config->ftp_upload != 'on' && !file_exists($thumbnail)) {
			        	$errors[] = lang("Error found while updating the playlist avatar, Please try again later.");
			        }
				    $privacy = $playlist->privacy;
				    if (isset($_POST['privacy'])) {
			            if (in_array($_POST['privacy'], array(0, 1))) {
			                $privacy = secure($_POST['privacy']);
			            }
			        }
			        if (empty($errors)) {
			           	$update_playList = [
			           		'name' => $name,
			           		'privacy' => $privacy,
			           		'thumbnail' => $thumbnail,
			           	];
			           	if (isAdmin() || $user->id == $playlist->user_id) {
			           		$update = $db->where('id', $playlist->id)->update(T_PLAYLISTS, $update_playList);
				           	if ($update) {
				           		$data['status'] = 200;
				           	}
			           	}
			        }
			    }
			}
		}
	}
}

?>