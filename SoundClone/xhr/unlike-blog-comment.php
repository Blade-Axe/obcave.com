<?php
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

$comment_id = 0;
if (!empty($_GET['id'])) {
    if (is_numeric($_GET['id'])) {
        $comment_id = secure($_GET['id']);
    }
}
if (empty($comment_id)) {
    exit("Invalid Comment ID");
}

$getComment = getBlogComment($comment_id);
if (empty($getComment)) {
    exit("Invalid Comment ID");
}

$data['status'] = 400;

$unlike = BlogUnLikeComment([
    'comment_user_id' => $getComment->user_id,
    'article_id' => $getComment->article_id,
    'user_id' => $user->id,
    'comment_id' => $comment_id
]);
if($unlike) {
    $songID = songData($getComment->track_id);
    RecordUserActivities('unlike_blog_comment',array('like_blog_comment' => $getComment->user_id,'article_id' => $getComment->article_id));
    $data['status'] = 200;
}

?>