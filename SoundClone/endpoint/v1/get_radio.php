<?php
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
$sql = 'SELECT * FROM `'.T_SONGS.'` WHERE `src` = "radio" '.$offset_text.' ORDER BY `id` DESC '.$limit_text;
$stations            = $db->objectBuilder()->rawQuery($sql);
$stations_data = array();
if (!empty($stations)) {
	foreach ($stations as $key => $station) {
	    $stations_data[] = songData($station->id);
	}
}

$data['status'] = 200;
$data['data'] = $stations_data;