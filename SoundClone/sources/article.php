<?php
if( empty($path['options'][1]) ) {
    header("Location: $site_url/404");
    exit();
}

if (!empty($path['options'][1])) {
    $arr = explode("_",$path['options'][1]);
    if( isset($arr[0]) && $arr[0] > 0 ){
        $article = Secure((int)$arr[0]);
    }
}

if( !empty($article) ) {
    if ($db->where('id', $article)->getValue(T_BLOG, 'id') === NULL) {
        header("Location: $site_url/404");
        exit();
    }

    if( !isset( $_SESSION['blog_view'][$article] ) ) {
        $db->where('id', $article)->update('blog', array('view' => $db->inc()));
        $_SESSION['blog_view'][$article] = true;
    }

}
$articleData = $db->arrayBuilder()->where('id', $article)->getOne(T_BLOG);



$music->site_pagename = 'blog_article';//$path['options'][1];
$music->site_description = $articleData['description'];
$music->site_title = $articleData['title'];
//$articleData['url'] = urlencode( $site_url . '/' . $articleData['id'] . '_' . url_slug(html_entity_decode($articleData['title'])) );
$articleData['url'] = urlencode( $site_url . '/article' . '/' . $articleData['id'] . '_' . url_slug(html_entity_decode($articleData['title'])) );

if($articleData['created_by'] == 0){
    $articleData['userData'] = $music->user;
}else{
    $articleData['userData'] = userData($articleData['created_by']);
}
$music->articleData = $articleData;

$getSongComments = $db->where('article_id', $articleData['id'])->orderBy('id', 'DESC')->get(T_BLOG_COMMENTS, 20);
$music->comment_count = 0;
$comment_html = '';
if (!empty($getSongComments)) {
	foreach ($getSongComments as $key => $comment) {
        $music->comment_count++;
		$comment = getComment($comment, false);
		$commentUser = userData($comment->user_id);
		$music->comment = $comment;
		$comment_html .= loadPage('blogs/comment-list', [
			'comment_id' => $comment->id,
			'USER_DATA' => $commentUser,
			'comment_text' => $comment->value,
			'comment_posted_time' => $comment->time,
            'tcomment_posted_time' => date('c',$comment->time),
            'comment_song_article_id' => $articleData['id']
		]);
	}
} else {
	$comment_html = '<div class="no-track-found bg_light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M9,22A1,1 0 0,1 8,21V18H4A2,2 0 0,1 2,16V4C2,2.89 2.9,2 4,2H20A2,2 0 0,1 22,4V16A2,2 0 0,1 20,18H13.9L10.2,21.71C10,21.9 9.75,22 9.5,22V22H9Z" /></svg>' . lang("No comments found") . '</div>';
}

$music->site_content = loadPage("blogs/article", [
    'thumbnail' => getMedia($articleData['thumbnail']),
    'id' => $articleData['id'],
    'title' => $articleData['title'],
    'content' => $articleData['content'],
    'description' => $articleData['description'],
    'posted' => $articleData['posted'],
    'category' => $articleData['category'],
    'category_text' => lang($articleData['category']),
    'view' => $articleData['view'],
    'shared' => $articleData['shared'],
    'tags' => $articleData['tags'],
    'created_at' => $articleData['created_at'],
    'created_by' => $articleData['userData'],
    'url' => $articleData['url'],
    'comment_song_article_id' => $articleData['id'],
    'comment_count' => number_format_mm($db->where('article_id', $articleData['id'])->getValue(T_BLOG_COMMENTS, 'count(*)')),
    'comment_list' => $comment_html
]);