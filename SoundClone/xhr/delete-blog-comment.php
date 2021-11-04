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

if ($getComment->user_id != $user->id && isAdmin() == false) {
	exit("You can't delete this Comment");
}

$data['status'] = 400;


if ($db->where('id', $comment_id)->delete(T_BLOG_COMMENTS)) {
	$db->where('comment_id', $comment_id)->delete(T_BLOG_LIKES);
	$data['status'] = 200;
}

?>