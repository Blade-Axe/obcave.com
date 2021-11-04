<?php
if ($option == 'get') {
	$offset             = (isset($_POST['offset']) && is_numeric($_POST['offset']) && $_POST['offset'] > 0) ? secure($_POST['offset']) : 0;
	$limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 20;
	$offset_text = '';
    if ($offset > 0) {
        $offset_text = ' WHERE `id` < ' . $offset;
    }
	$limit_text = '';
    if ($limit > 0) {
        $limit_text = ' limit ' . $limit;
    }
	$sql = 'SELECT * FROM `'.T_BLOG.'` '.$offset_text.' ORDER BY `id` DESC '.$limit_text;
	$articles            = $db->objectBuilder()->rawQuery($sql);
	$articles_data = array();
	if (!empty($articles)) {
	    foreach ($articles as $key => $art) {
	        $article = GetArticle($art->id);
	        $article['thumbnail'] = getMedia($article['thumbnail']);
	        $article['created_at'] = time_Elapsed_String($article['created_at']);
	        $articles_data[] = $article;
	    }
	}
	$data['status'] = 200;
    $data['data'] = $articles_data;
}
if ($option == 'get_blog') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$id = secure($_POST['id']);
		$db->where('id', $id)->update('blog', array('view' => $db->inc()));
		$article_data = array();
		$article = GetArticle($id);
		if (!empty($article)) {
			$article_data = $article;
			$article_data['thumbnail'] = getMedia($article['thumbnail']);
	        $article_data['text_time'] = time_Elapsed_String($article['created_at']);
		}
		$data['status'] = 200;
	    $data['data'] = $article_data;
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'id can not be empty'
        ];
	}
}
if ($option == 'get_comments') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$id = secure($_POST['id']);
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
		$sql = 'SELECT * FROM `'.T_BLOG_COMMENTS.'` WHERE `article_id` = '.$id.' '.$offset_text.' ORDER BY `id` DESC '.$limit_text;
		$comments            = $db->objectBuilder()->rawQuery($sql);
		$comments_data = array();
		if (!empty($comments)) {
			foreach ($comments as $key => $value) {
				$comment = getComment($value, false);
				$comment->user_data = userData($comment->user_id);
				$comment->IsLikedComment = BlogLikeExists([
								'comment_user_id' => $comment->user_data->id,
								'article_id' => $id,
								'user_id' => (IS_LOGGED) ? $user->id : 0,
								'comment_id' => $comment->id
							]);
				unset($comment->user_data->password);
				$comments_data[] = $comment;
			}
			
		}
		$data['status'] = 200;
	    $data['data'] = $comments_data;
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'id can not be empty'
        ];
	}
}
if ($option == 'delete_comment') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$id = secure($_POST['id']);
		$getComment = getBlogComment($id);
		if (!empty($getComment)) {
			if ($getComment->user_id == $user->id || !isAdmin()) {
				if ($db->where('id', $id)->delete(T_BLOG_COMMENTS)) {
					$db->where('comment_id', $id)->delete(T_BLOG_LIKES);
					$data['status'] = 200;
					$data['data'] = 'comment deleted';
				}
				else{
					$data = [
			            'status' => 400,
			            'error' => 'something went wrong'
			        ];
				}
			}
			else{
				$data = [
		            'status' => 400,
		            'error' => 'You can not delete this Comment'
		        ];
			}
		}
		else{
			$data = [
	            'status' => 400,
	            'error' => 'Invalid Comment ID'
	        ];
		}
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'id can not be empty'
        ];
	}
}
if ($option == 'create_comment') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0 && !empty(trim($_POST['text']))) {
		$id = secure($_POST['id']);
		$blog = $db->where('id', $id)->getOne(T_BLOG);
		if (!empty($blog)) {
			$text = '';

			if (!empty($_POST['text'])) {
				$link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
			    $i          = 0;
			    preg_match_all($link_regex, secure($_POST['text']), $matches);
			    foreach ($matches[0] as $match) {
			        $match_url            = strip_tags($match);
			        $syntax               = '[a]' . urlencode($match_url) . '[/a]';
			        $_POST['text'] = str_replace($match, $syntax, $_POST['text']);
			    }
				$text = secure($_POST['text']);
			}


			$final_array = [
				'user_id' => $user->id,
				'article_id' => $blog->id,
				'value' => $text,
				'time' => time()
			];


			$insert = $db->insert(T_BLOG_COMMENTS, $final_array);
			if ($insert) {
				$comment = getBlogComment($insert);
				if (!empty($comment)) {
					$comment->user_data = userData($comment->user_id);
					unset($comment->user_data->password);
					$data = [
						'status' => 200,
						'data' => $comment
					];
				}
			}
		}
		else{
			$data = [
	            'status' => 400,
	            'error' => 'blog not found'
	        ];
		}
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'id text can not be empty'
        ];
	}
}
if ($option == 'like_comment') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		if (!empty($_POST['type']) && in_array($_POST['type'], array('like','unlike'))) {
			$id = secure($_POST['id']);
			$getComment = getBlogComment($id);
			if (!empty($getComment)) {
				if ($_POST['type'] == 'like') {
					$like = BlogLikeComment([
					    'url' => 'user/' . $user->username,
					    'comment_user_id' => $getComment->user_id,
					    'article_id' => $getComment->article_id,
					    'user_id' => $user->id,
					    'comment_id' => $id
					]);
					if($like) {
					    $data['status'] = 200;
					    $data['data'] = 'comment liked';
					}
				}
				else{
					$unlike = BlogUnLikeComment([
					    'comment_user_id' => $getComment->user_id,
					    'article_id' => $getComment->article_id,
					    'user_id' => $user->id,
					    'comment_id' => $id
					]);
					if($unlike) {
					    $data['status'] = 200;
					    $data['data'] = 'comment like removed';
					}
				}
			}
			else{
				$data = [
		            'status' => 400,
		            'error' => 'comment not found'
		        ];
			}
		}
		else{
			$data = [
	            'status' => 400,
	            'error' => 'type can not be empty'
	        ];
		}
	}
	else{
		$data = [
            'status' => 400,
            'error' => 'id can not be empty'
        ];
	}
}