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

    $getActivties = getActivties($limit, $offset, $user_id);
    $activity_array = array();
    if (!empty($getActivties)) {
        foreach ($getActivties as $key => $activity) {
            $getActivity = getActivity($activity, false);
            unset($getActivity['USER_DATA']->password);
            $activity_array[] = $getActivity;
        }
    }
    $data['status'] = 200;
    $data['data'] = $activity_array;
}
else{
    $data = [
        'status' => 400,
        'error' => 'user_id can not be empty'
    ];
}
