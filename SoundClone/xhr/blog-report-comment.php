<?php
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

$comment_id = 0;
if (!empty($_REQUEST['id'])) {
    $comment_id = secure($_REQUEST['id']);
}
if (empty($comment_id)) {
    exit("Invalid Comment ID");
}

$getComment = $db->where('id', $comment_id)->getOne(T_BLOG_COMMENTS);
if (empty($getComment)) {
    exit("Invalid Comment ID");
}

$data['status'] = 400;

if (empty($_POST['comment_description'])) {
    $errors[] = lang("Please describe your request.");
}

if (empty($errors)) {
    $description = secure($_POST['comment_description']);
    $insert_report = $db->insert(T_REPORTS, ['track_id' => $getComment->article_id,'comment_id' => $comment_id, 'description' => $description, 'time' => time(), 'user_id' => $user->id, 'mode' => 'blog']);
    if ($insert_report) {
        $notif_data = array(
            'recipient_id' => 0,
            'type' => 'report',
            'admin' => 1,
            'time' => time()
        );
        $db->insert(T_NOTIFICATION,$notif_data);
        $data['status'] = 200;
    }
} else {
    $data['status'] = 400;
    $data['errors'] = $errors;
}