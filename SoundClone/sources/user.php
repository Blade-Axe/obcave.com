<?php 

if (empty($path['page'])) {
	header("Location: $site_url/404");
	exit();
}
$record_count = 0;
$username = secure($path['page']);
if (IS_LOGGED) {
	$db->where("id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)");
}
$getIDfromUser = $db->where('username', $username)->getValue(T_USERS, 'id');
if (empty($getIDfromUser)) {
	header("Location: $site_url/404");
	exit();
}

$userData = userData($getIDfromUser);

$userData->owner  = false;

if ($music->loggedin == true) {
    $userData->owner  = ($user->id == $userData->id) ? true : false;
}

$profile_content = "";
$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M19,9H2V11H19V9M19,5H2V7H19V5M2,15H15V13H2V15M17,13V19L22,16L17,13Z" /></svg>' . lang("No tracks found") . '</div>';
$music->third_url = $third_url = (!empty($path['options'][1])) ? $path['options'][1] : '';
$file = 'songs';


$music->userData = $userData;
$music->userData->fields = UserFieldsData($userData->id);

if (empty($path['options'][1]) || $path['options'][1] == 'activities') {
	$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M3,7H9V13H3V7M3,3H21V5H3V3M21,11V13H11V11H21M3,15H17V17H3V15M3,19H21V21H3V19Z" /></svg>' . lang("No activties found") . '</div>';
	$getActivties = getActivties(10, 0, $userData->id);
	if (!empty($getActivties)) {
		$html = '';
		foreach ($getActivties as $key => $activity) {
            $record_count++;
			$getActivity = getActivity($activity, false);
			$html .= loadPage("user/activity", $getActivity);
		}
	}
} else if ($path['options'][1] == 'liked') {
	$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M23,10C23,8.89 22.1,8 21,8H14.68L15.64,3.43C15.66,3.33 15.67,3.22 15.67,3.11C15.67,2.7 15.5,2.32 15.23,2.05L14.17,1L7.59,7.58C7.22,7.95 7,8.45 7,9V19A2,2 0 0,0 9,21H18C18.83,21 19.54,20.5 19.84,19.78L22.86,12.73C22.95,12.5 23,12.26 23,12V10M1,21H5V9H1V21Z" /></svg>' . lang("No activties found") . '</div>';
	$getActivties = getActivties(10, 0, $userData->id, ['likes' => true]);
	if (!empty($getActivties)) {
		$html = '';
		foreach ($getActivties as $key => $activity) {
            $record_count++;
			$getActivity = getActivity($activity, false);
			$html .= loadPage("user/activity", $getActivity);
		}
	}
} else if ($path['options'][1] == 'songs') {
	$db->where('user_id', $userData->id);
	if (!IS_LOGGED) {
		$db->where('availability', '0');
	} else {
		if ($user->id != $userData->id) {
			$db->where('availability', '0');
		}
	}
	$getUserSongs = $db->orderby('id', 'DESC')->get(T_SONGS, 10, 'id');
	if (!empty($getUserSongs)) {
		$html = '';
		foreach ($getUserSongs as $key => $userSong) {
            $record_count++;
			$userSong = songData($userSong->id);
			$music->isSongOwner = false;
			if (IS_LOGGED == true) {
				$music->isSongOwner = ($user->id == $userSong->publisher->id) ? true : false;
			}
			$music->songData = $userSong;
			$music->dark_wave = $userSong->dark_wave;
			$music->light_wave = $userSong->light_wave;
			$html .= loadPage("user/posts", $userSong->songArray);
		}
	}
} else if ($path['options'][1] == 'top-songs') {
	$getUserSongs = $getLatestSongs = $db->rawQuery("
SELECT " . T_SONGS . ".*, COUNT(" . T_VIEWS . ".id) AS " . T_VIEWS . "
FROM " . T_SONGS . " LEFT JOIN " . T_VIEWS . " ON " . T_SONGS . ".id = " . T_VIEWS . ".track_id
WHERE " . T_SONGS . ".user_id = " . $userData->id . "
GROUP BY " . T_SONGS . ".id
ORDER BY " . T_VIEWS . " DESC LIMIT 20");
	
	if (!empty($getUserSongs)) {
		$html = "";
		foreach ($getUserSongs as $key => $userSong) {
            
			$userSong = songData($userSong->id);
			if (!empty($userSong)) {
				$record_count++;
				$music->isSongOwner = false;
				if (IS_LOGGED == true) {
					$music->isSongOwner = ($user->id == $userSong->publisher->id) ? true : false;
				}
				$music->songData = $userSong;
				$music->dark_wave = $userSong->dark_wave;
				$music->light_wave = $userSong->light_wave;
				$html .= loadPage("user/posts", $userSong->songArray);
			}
				
		}
	}
}  else if ($path['options'][1] == 'store') {
	$getUserSongs = $db->where('user_id', $userData->id)->where('price', '0', '<>')->orderBy('id', 'DESC')->get(T_SONGS, 10);
	if (!empty($getUserSongs)) {
		$html = "";
		foreach ($getUserSongs as $key => $userSong) {
            $record_count++;
			$userSong = songData($userSong->id);
			$music->isSongOwner = false;
			if (IS_LOGGED == true) {
				$music->isSongOwner = ($user->id == $userSong->publisher->id) ? true : false;
			}
			$music->songData = $userSong;
			$music->dark_wave = $userSong->dark_wave;
			$music->light_wave = $userSong->light_wave;
			$music->store = true;
			$html .= loadPage("user/posts", $userSong->songArray);
		}
	}
} else if ($path['options'][1] == 'playlists') {
	$file = 'playlists';
	if ($userData->owner == true) {
		$getPlayLists = $db->where('user_id', $userData->id)->orderBy('id', 'DESC')->get(T_PLAYLISTS, 9);
	} else {
		$getPlayLists = $db->where('user_id', $userData->id)->where('privacy', 0)->orderBy('id', 'DESC')->get(T_PLAYLISTS, 9);
	}
	if (!empty($getPlayLists)) {
		$html = "";
		foreach ($getPlayLists as $key => $playlist) {
            $record_count++;
			$playlist = getPlayList($playlist, false);
			$html .= loadPage("user/playlist-list", [
				't_thumbnail' => $playlist->thumbnail_ready,
				't_id' => $playlist->id,
				's_artist' => $playlist->publisher->name,
				't_uid' => $playlist->uid,
				't_title' => $playlist->name,
				't_privacy' => $playlist->privacy_text,
				't_url' => $playlist->url,
				't_url_original' => $playlist->url,
				't_songs' => $playlist->songs,
				'USER_DATA' => $playlist->publisher
			]);
		}
	}
} else if ($path['options'][1] == 'albums') {
	$file = 'albums';
	$getAlbums = $db->where('user_id', $userData->id)->orderBy('id', 'DESC')->get(T_ALBUMS, 9);
	if (!empty($getAlbums)) {
		$html = "";
		foreach ($getAlbums as $key => $album) {
            $record_count++;
			$key = ($key + 1);
			$html .= loadPage("user/album-list", [
				'url' => getLink("album/$album->album_id"),
				'title' => $album->title,
				'thumbnail' => getMedia($album->thumbnail),
				'id' => $album->id,
				'album_id' => $album->album_id,
				'USER_DATA' => userData($album->user_id),
				'key' => $key,
				'songs' => $db->where('album_id', $album->id)->getValue(T_SONGS, 'COUNT(*)')
			]);
		}
	}
} else if ($path['options'][1] == 'followers') {
    $file = 'followers';
    $getFollowers = $db->where('following_id', $userData->id)
                        ->where("follower_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")
                        ->orderBy('id', 'DESC')->get(T_FOLLOWERS, 9);
	$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16,13C15.71,13 15.38,13 15.03,13.05C16.19,13.89 17,15 17,16.5V19H23V16.5C23,14.17 18.33,13 16,13M8,13C5.67,13 1,14.17 1,16.5V19H15V16.5C15,14.17 10.33,13 8,13M8,11A3,3 0 0,0 11,8A3,3 0 0,0 8,5A3,3 0 0,0 5,8A3,3 0 0,0 8,11M16,11A3,3 0 0,0 19,8A3,3 0 0,0 16,5A3,3 0 0,0 13,8A3,3 0 0,0 16,11Z" /></svg>' . lang("No followers found") . '</div>';
    if (!empty($getFollowers)) {
        $html = "";
        foreach ($getFollowers as $key => $follower) {
            $record_count++;
            $key = ($key + 1);
            $html .= loadPage("user/follower-list", [
                'f_id' => $follower->id,
                'USER_DATA' => userData($follower->follower_id),
            ]);
        }
    }
} else if ($path['options'][1] == 'following') {
    $file = 'following';
    $getFollowings = $db->where('follower_id', $userData->id)
                        ->where("following_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")
                        ->orderBy('id', 'DESC')->get(T_FOLLOWERS, 9);
	$html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M16,13C15.71,13 15.38,13 15.03,13.05C16.19,13.89 17,15 17,16.5V19H23V16.5C23,14.17 18.33,13 16,13M8,13C5.67,13 1,14.17 1,16.5V19H15V16.5C15,14.17 10.33,13 8,13M8,11A3,3 0 0,0 11,8A3,3 0 0,0 8,5A3,3 0 0,0 5,8A3,3 0 0,0 8,11M16,11A3,3 0 0,0 19,8A3,3 0 0,0 16,5A3,3 0 0,0 13,8A3,3 0 0,0 16,11Z" /></svg>' . lang("No following found") . '</div>';
    if (!empty($getFollowings)) {
        $html = "";
        foreach ($getFollowings as $key => $following) {
            $record_count++;
            $key = ($key + 1);
            $html .= loadPage("user/following-list", [
                'f_id' => $following->id,
                'USER_DATA' => userData($following->following_id),
            ]);
        }
    }
} else if ($path['options'][1] == 'stations' || $path['options'][1] == 'add_station' ) {
    if ($music->config->radio_station_import == 'off' ) {
        header("Location: $site_url/404");
        exit();
    }

    $file = 'stations';
    $html = '<div class="no-track-found bg_light"><svg height="512" viewBox="0 0 60 60" width="512" xmlns="http://www.w3.org/2000/svg"><g id="Page-1" fill="currentColor" fill-rule="evenodd"><g id="008---Radio" fill="rgb(0,0,0)" fill-rule="nonzero" transform="translate(-1)"><path fill="currentColor" id="Shape" d="m31 41.54-4.71 3.13 4.71 1.47 4.71-1.47z"/><path fill="currentColor" id="Shape" d="m31 51.95 5.74-1.92-5.74-1.79-5.74 1.79z"/><path fill="currentColor" id="Shape" d="m38.138 38.433c.1890241.1863264.4443684.2898625.7097801.2877992s.5191154-.1095566.7052199-.2987992c4.6310411-4.7058584 4.5906399-12.2688196-.0904128-16.9249329-4.6810527-4.6561134-12.2441217-4.6561134-16.9251744 0-4.6810527 4.6561133-4.7214539 12.2190745-.0904128 16.9249329.3910651.367396 1.0023888.3606722 1.3852785-.0152363s.4008601-.9870051.0407215-1.3847637c-3.8596105-3.9214057-3.8262409-10.2241334.0746755-14.1044513 3.9009165-3.8803178 10.2037325-3.8803178 14.104649 0 3.9009164 3.8803179 3.934286 10.1830456.0746755 14.1044513-.3853619.3931749-.380445 1.023881.011 1.411z"/><path fill="currentColor" id="Shape" d="m41.411 44.44c.2217496.0004433.4372408-.0734997.612-.21 6.0842634-4.7130528 8.4989854-12.7728945 6.0084468-20.0549509s-9.3352706-12.1750692-17.0314468-12.1750692-14.5409082 4.8930128-17.0314468 12.1750692-.0758166 15.3418981 6.0084468 20.0549509c.2806177.2378475.6691625.302426 1.0116313.1681392.3424687-.1342867.5835605-.4457545.6277166-.8109503s-.1157538-.725149-.4163479-.9371889c-5.4088636-4.1890317-7.5559052-11.3535235-5.342314-17.8268383 2.2135913-6.4733149 8.2979843-10.8230151 15.139314-10.8230151s12.9257227 4.3497002 15.139314 10.8230151c2.2135912 6.4733148.0665496 13.6378066-5.342314 17.8268383-.3377747.2621414-.47152.7100284-.3327933 1.1144598.1387268.4044315.5192307.6759223.9467933.6755402z"/><path fill="currentColor" id="Shape" d="m43.921 50.03c.2003247.0002752.3959909-.0604127.561-.174 8.7184311-5.9199045 12.5551169-16.8346318 9.4584539-26.9077141-3.0966629-10.0730824-12.4021281-16.94765485-22.9404539-16.94765485s-19.843791 6.87457245-22.94045395 16.94765485c-3.09666296 10.0730823.74002286 20.9878096 9.45845395 26.9077141.4572918.3106601 1.0798398.1917917 1.3904999-.2655.3106602-.4572918.1917918-1.0798398-.2654999-1.3905-7.9926195-5.4261086-11.51034146-15.4314606-8.67201582-24.6655565 2.83832562-9.234096 11.36854892-15.53621679 21.02901582-15.53621679s18.1906902 6.30212079 21.0290158 15.53621679c2.8383257 9.2340959-.6793963 19.2394479-8.6720158 24.6655565-.3633259.2469906-.5228956.7021872-.3933077 1.1219694.129588.4197821.5179793.7058216.9573077.7050306z"/><path fill="currentColor" id="Shape" d="m31 0c-13.3810514-.00550229-25.14792834 8.85160355-28.84472764 21.7118618-3.69679929 12.8602582 1.57039241 26.6139635 12.91172764 33.7151382.302604.1902439.6837155.2045993.9997735.0376585.3160581-.1669407.5190459-.4898153.5325-.847.0134541-.3571846-.1646695-.6944146-.4672735-.8846585-10.5793892-6.6295049-15.49136857-19.4623106-12.04240232-31.4614124 3.44896624-11.9991017 14.42545892-20.26499854 26.91040232-20.26499854s23.4614361 8.26589684 26.9104023 20.26499854c3.4489663 11.9991018-1.4630131 24.8319075-12.0424023 31.4614124-.4677852.2940916-.6085916.9117148-.3145 1.3795.2940917.4677851.9117148.6085916 1.3795.3145 11.3413352-7.1011747 16.6085269-20.85488 12.9117276-33.7151382-3.6967993-12.86025825-15.4636762-21.71736409-28.8447276-21.7118618z"/><path fill="currentColor" id="Shape" d="m33.542 33.231c-.0223633-.0368103-.0474324-.071907-.075-.105 1.343049-1.0522974 1.8707718-2.8406649 1.3141735-4.4535223-.5565984-1.6128574-2.0749758-2.6951141-3.7811735-2.6951141s-3.2245751 1.0822567-3.7811735 2.6951141c-.5565983 1.6128574-.0288755 3.4012249 1.3141735 4.4535223-.0275676.033093-.0526367.0681897-.075.105l-11.37 25.36c-.1595593.3279594-.1279169.7167773.082571 1.0146219s.5664356.4574746.9288472.4165553.6738047-.2758977.8125818-.6131772l1.81-4.036 7.118-2.373-5.268-1.753 1.285-2.868 3.793-1.189-2.859-.89 1.931-4.306 2.478-1.654-1.337-.9.826-1.843 2.311 1.543 2.311-1.54.826 1.843-1.337.9 2.478 1.65 1.931 4.307-2.859.894 3.793 1.189 1.285 2.868-5.268 1.749 7.118 2.373 1.81 4.036c.1387771.3372795.4501702.5722579.8125818.6131772s.7183593-.1187107.9288472-.4165553.2421303-.6866625.082571-1.0146219z"/></g></g></svg>' . lang("No stations found") . '</div>';

        $my_stations = $db->arrayBuilder()->where('user_id', $getIDfromUser)->where('src','radio')->orderBy('time', 'DESC')->get(T_SONGS);

    if (!empty($my_stations)) {
        $html = "";
        $music->stations = count($my_stations);
        $station_html = '';
        foreach ($my_stations as $key => $station) {
            $station = songData($station['id']);
            $station_html .= loadPage("stations/list", [
                'STATION_DATA' => $station
            ]);
        }

        $html .= loadPage("stations/content", [
            'STATIONS' => $station_html
        ]);
    }


    if($path['options'][1] == 'add_station'){
        $html = loadPage("user/add_station");
    }
} else if ($path['options'][1] == 'spotlight') {
    if (!IS_LOGGED || $userData->owner === false) {
        header("Location: $site_url/404");
        exit();
    }
    if ($userData->is_pro == 0) {
        header("Location: $site_url/404");
        exit();
    }

    $tracks = $db->where('user_id', $userData->id)->orderBy('time', 'DESC')->get(T_SONGS,15,array('*'));
    $htmlTracks = '';
    foreach ($tracks as $key => $track){
        $track = songData($track->id);
        $htmlTracks .= loadPage("spotlight/posts", $track);
    }

    $html = loadPage("spotlight/edit", [
        'tracks' => $htmlTracks
    ]);

}

$where = '';
if (IS_LOGGED) {
    $where = "`id` <> ". $user->id ." AND ";
}

$result_artists = $db->rawQuery("SELECT * FROM `".T_USERS."` WHERE ". $where ."  `artist` = 1 ORDER BY rand() DESC LIMIT 10;");
$artists_html = '';
foreach ($result_artists as $artists) {
    $pagedata = [
        'ARTIST_DATA' => userData( $artists->id )
    ];
    $artists_html = loadPage("user/artist-item", $pagedata);
}
$music->artists_html = $artists_html;


$query4 = "SELECT " . T_SONGS . ".*, COUNT(" . T_VIEWS . ".id) AS " . T_VIEWS . "
FROM " . T_SONGS . " LEFT JOIN " . T_VIEWS . " ON " . T_SONGS . ".id = " . T_VIEWS . ".track_id
WHERE " . T_SONGS . ".availability = '0'";

if (IS_LOGGED) {
    $query4 .= " AND " . T_SONGS . ".user_id NOT IN (SELECT user_id FROM blocks WHERE blocked_id = $user->id)";
}

$time = strtotime(date('l').", ".date('M')." ".date('d').", ".date('Y'));

if (date('l') == 'Saturday') {
    $week_start = strtotime(date('M')." ".date('d').", ".date('Y')." 12:00am");
}
else{
    $week_start = strtotime('last saturday, 12:00am', $time);
}

if (date('l') == 'Friday') {
    $week_end = strtotime(date('M')." ".date('d').", ".date('Y')." 11:59pm");
}
else{
    $week_end = strtotime('next Friday, 11:59pm', $time);
}

$query4 .= " AND " . T_VIEWS .".time >= " . $week_start . " AND " . T_VIEWS .".time <= " . $week_end;

$query4 .= " GROUP BY " . T_SONGS . ".id
ORDER BY " . T_VIEWS . " DESC LIMIT 10";
$top_weekly = $db->rawQuery($query4);

$music->top_weekly_html = [];
foreach ($top_weekly as $song) {
    $music->top_weekly[] = songData($song, false, false);
//    $pagedata = [
//        'ARTIST_DATA' => userData( $artists->id )
//    ];
//    $top_weekly_html = loadPage("user/artist-item", $pagedata);
}


$user_profile = ($userData->artist == 0) ? "content" : "artist";


$music->html = $html;
$music->record_count = $record_count;
$profile_content = loadPage("user/$file", [
    'USER_DATA' => $userData,
	'HTML' => $html
]);


$music->site_title = $userData->name;
$music->site_description = $music->config->description;
$music->site_pagename = "user";
$userFinalData = [
	'USER_DATA' => $userData,
    'MESSAGE_BUTTON'  => GetMessageButton($userData->username),
	'COUNT_FOLLOWERS' => $db->where('following_id', $userData->id)->where("follower_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")->getValue(T_FOLLOWERS, 'COUNT(*)'),
	'COUNT_FOLLOWING' => $db->where('follower_id', $userData->id)->where("following_id NOT IN (SELECT blocked_id FROM blocks WHERE user_id = $userData->id)")->getValue(T_FOLLOWERS, 'COUNT(*)'),
	'COUNT_TRACKS' => $db->where('user_id', $userData->id)->getValue(T_SONGS, 'COUNT(*)'),
	'PROFILE_CONTENT' => $profile_content
];

$music->site_content = loadPage("user/$user_profile", $userFinalData);