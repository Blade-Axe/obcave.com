<?php

$getAlbums = $db->orderBy('id', 'DESC')->get(T_ALBUMS, 10);
$records = 0;
$html_list = '';
if (!empty($getAlbums)) {
    $records = count($getAlbums);
    $html_list = '';
    foreach ($getAlbums as $key => $album) {
        if (!empty($album)) {
            $publisher = userData($album->user_id);
            $html_list .= loadPage('store/albums', [
                'id' => $album->id,
                'album_id' => $album->album_id,
                'user_id' => $album->user_id,
                'artist' => $publisher->username,
                'artist_name' => $publisher->name,
                'title' => $album->title,
                'description' => $album->description,
                'category_id' => $album->category_id,
                'thumbnail' => getMedia($album->thumbnail),
                'time' => $album->time,
                'registered' => $album->registered,
                'price' => $album->price,
                'songs' => number_format_mm($db->where('album_id', $album->id)->getValue(T_SONGS, 'count(*)'))
            ]);
        }
    }
}

$music->site_title = lang("Albums");
$music->site_description = $music->config->description;
$music->site_pagename = "albums";
$music->site_content = loadPage("albums/content", [
    'records' => $records,
    'html_content' => $html_list
]);