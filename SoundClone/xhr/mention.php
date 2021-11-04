<?php
$term = "";
if (!empty($_GET['term'])) {
    $term = secure($_GET['term']);
}
if (empty($term)) {
    exit("Invalid Term");
}
if (IS_LOGGED == false) {
    $data['status'] = 300;
} else {
    $logged_user_id = $music->user->id;
    $sql = "SELECT `username`,`name` AS `label`,`avatar` AS `img` FROM `".T_USERS."` WHERE `active` = '1' AND ((`username` LIKE '%" . $term . "%') OR (`name` LIKE '%" . $term . "%')) ";
    $sql .= "AND `id` NOT IN (SELECT `user_id` FROM `" . T_BLOCKS . "` WHERE `blocked_id` = '{$logged_user_id}') ";
    $sql .= "AND `id` NOT IN (SELECT `blocked_id` FROM `" . T_BLOCKS . "` WHERE `user_id` = '{$logged_user_id}') ";
    $sql .= "AND (`id` IN (SELECT `following_id` FROM `" . T_FOLLOWERS . "` WHERE `follower_id` = {$logged_user_id} AND `following_id` <> {$logged_user_id} AND `active` = '1') ";
    $sql .= "OR `id` IN (SELECT `follower_id` FROM `" . T_FOLLOWERS . "` WHERE `follower_id` <> {$logged_user_id} AND `following_id` = {$logged_user_id} AND `active` = '1')) LIMIT 5;";
    $data = array();
    $query_get = mysqli_query($sqlConnect,$sql);
    if (mysqli_num_rows($query_get) > 0) {
        while ($users = mysqli_fetch_assoc($query_get)) {
            $users['img'] = GetMedia($users['img']);
            $data[] = $users;
        }
    }
}
