<?php
if ($option == 'new') {

	if (!empty($_POST['id']) && (!empty($_POST['new-message']) || !empty($_FILES['media']['tmp_name']))  && !empty($_POST['hash-message'])) {
        
        $id = Secure($_POST['id']);
        if ($id != $music->user->id) {
            $chat_exits = $db->where("user_one", $music->user->id)->where("user_two", $id)->getValue(T_CHATS, 'count(*)');
            if (!empty($chat_exits)) {
                $db->where("user_two", $music->user->id)->where("user_one", $id)->update(T_CHATS, array('time' => time()));
                $db->where("user_one", $music->user->id)->where("user_two", $id)->update(T_CHATS, array('time' => time()));
                if ($db->where("user_two", $music->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)') == 0) {
                    $db->insert(T_CHATS, array('user_two' => $music->user->id, 'user_one' => $id,'time' => time()));
                }
            } else {
                $db->insert(T_CHATS, array('user_one' => $music->user->id, 'user_two' => $id,'time' => time()));
                if (empty($db->where("user_two", $music->user->id)->where("user_one", $id)->getValue(T_CHATS, 'count(*)'))) {
                    $db->insert(T_CHATS, array('user_two' => $music->user->id, 'user_one' => $id,'time' => time()));
                }
            }

            if (!empty($_FILES['media']['tmp_name'])) {
                $file_info   = array(
                    'file' => $_FILES['media']['tmp_name'],
                    'size' => $_FILES['media']['size'],
                    'name' => $_FILES['media']['name'],
                    'type' => $_FILES['media']['type'],
                    'crop' => array(
                        'width' => 600,
                        'height' => 600
                    )
                );
                $music->config->s3_upload = 'off';
                $music->config->ftp_upload = 'off';
                $file_upload = shareFile($file_info);
                if (!empty($file_upload['filename'])) {
                    $thumbnail = secure($file_upload['filename'], 0);
                    $new_message = '[img]'.$thumbnail.'[/img]';
                    $insert_message = array(
                        'from_id' => $music->user->id,
                        'to_id' => $id,
                        'text' => $new_message,
                        'time' => time()
                    );
                    $insert = $db->insert(T_MESSAGES, $insert_message);
                    if ($insert) {
                        $music->message = GetMessageData($insert);
                        $music->message->hash = $_POST['hash-message'];
                        $data = array(
		                    'status' => 200,
		                    'message_id' => $insert,
		                    'data' => $music->message 
		                );
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
			            'error' => 'invalid media'
			        ];
                }
            }else{
            	$link_regex = '/(http\:\/\/|https\:\/\/|www\.)([^\ ]+)/i';
		        $i          = 0;
		        preg_match_all($link_regex, Secure($_POST['new-message']), $matches);
		        foreach ($matches[0] as $match) {
		            $match_url           = strip_tags($match);
		            $syntax              = '[a]' . urlencode($match_url) . '[/a]';
		            $_POST['new-message'] = str_replace($match, $syntax, $_POST['new-message']);
		        }
		        $new_message = Secure($_POST['new-message']);
                $insert_message = array(
	                'from_id' => $music->user->id,
	                'to_id' => $id,
	                'text' => $new_message,
	                'time' => time()
	            );
	            $insert = $db->insert(T_MESSAGES, $insert_message);
	            if ($insert) {
	                $music->message = GetMessageData($insert);
	                $music->message->hash = $_POST['hash-message'];
	                $data = array(
	                    'status' => 200,
	                    'message_id' => $insert,
	                    'data' => $music->message 
	                );
	            }
	            else{
	            	$data = [
			            'status' => 400,
			            'error' => 'something went wrong'
			        ];
	            }
            }
	            
        }
        else{
        	$data = [
	            'status' => 400,
	            'error' => 'id can not be empty'
	        ];
        }
    }
    else{
    	$data = [
            'status' => 400,
            'error' => 'id , hash-message ,(new-message OR media) can not be empty'
        ];

    }
}
if ($option == 'fetch') {
    if (empty($_POST['last_id'])) {
        $_POST['last_id'] = 0;
    }
    if (empty($_POST['user_id'])) {
        $_POST['user_id'] = 0;
    }
    if (empty($_POST['first_id'])) {
        $_POST['first_id'] = 0;
    }
    $limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 75;
    $messages_data = array();

    $messages = GetMessages($_POST['user_id'], array('last_id' => $_POST['last_id'], 'first_id' => $_POST['first_id'], 'return_method' => 'obj'),$limit);
    if (!empty($messages)) {
    	foreach ($messages as $key => $value) {
    		$messages_data[] = GetMessageData($value->id);
    	}
    }

    $data = array(
        'status' => 200,
        'data' => $messages_data
    );

    
}
if ($option == 'get_chats') {
	$limit             = (isset($_POST['limit']) && is_numeric($_POST['limit']) && $_POST['limit'] > 0) ? secure($_POST['limit']) : 50;
	$offset             = (!empty($_POST['offset'])) ? secure($_POST['offset']) : '';
	$messages_data = array();
	$keyword = '';
	if (!empty($_POST['keyword'])) {
		$keyword = secure($_POST['keyword']);
	}
	$messages = GetMessagesUserList(array('return_method' => 'obj','keyword' => $keyword),$limit,$offset);
	if (!empty($messages)) {
		foreach ($messages as $key => $value) {
			$new = $value;
			unset($new->user->password);
			$new->get_last_message = GetMessageData($new->get_last_message->id);
			$messages_data[] = $new;
		}
	}

    $data = array(
        'status' => 200,
        'data' => $messages_data
    );

}
if ($option == 'delete_chat') {
	if (!empty($_POST['user_id'])) {
        $id = Secure($_POST['user_id']);
        $messages = $db->where("(from_id = {$music->user->id} AND to_id = {$id}) OR (from_id = {$id} AND to_id = {$music->user->id})")->get(T_MESSAGES);
        $update1 = array();
        $update2 = array();
        $erase = array();
        $images = array();
        if (!empty($messages)) {
	        foreach ($messages as $key => $message) {
	            if ($message->from_deleted == 1 || $message->to_deleted == 1) {
	                $erase[] = $message->id;

	                $img = $message->text;
	                if( substr($img, 0, 5) == '[img]' && substr($img, -6) == '[/img]'){
	                    $img = str_replace(array('[img]','[/img]'), '', $img);
	                    if( @file_exists( $img ) ){
	                        $images[] = $img;
	                    }
	                }

	            } else {
	                if ($message->to_id == $music->user->id) {
	                    $update2[] = $message->id;
	                } else {
	                    $update1[] = $message->id;
	                }
	            }
	        }
	        if (!empty($erase)) {
	            $erase = implode(',', $erase);
	            $final_query = "DELETE FROM " . T_MESSAGES . " WHERE id IN ($erase)";
	            $db->rawQuery($final_query);
	            if(!empty($images)){
	                foreach ($images as $image){
	                    @unlink($image);
	                }
	            }
	        }
	        if (!empty($update1)) {
	            $update1 = implode(',', $update1);
	            $final_query = "UPDATE " . T_MESSAGES . " set `from_deleted` = '1' WHERE `id` IN({$update1}) ";
	            $db->rawQuery($final_query);
	        }
	        if (!empty($update2)) {
	            $update2 = implode(',', $update2);
	            $final_query = "UPDATE " . T_MESSAGES . " set `to_deleted` = '1' WHERE `id` IN({$update2}) ";
	            $db->rawQuery($final_query);
	        }
	        $delete_chats = $db->rawQuery("DELETE FROM " . T_CHATS . " WHERE user_one = {$music->user->id} AND user_two = $id");
	        $data = array(
		        'status' => 200,
		        'data' => 'Chat deleted'
		    );
	    }
	    else{
	    	$data = [
	            'status' => 400,
	            'error' => 'user not found'
	        ];
	    }
    }
    else{
    	$data = [
            'status' => 400,
            'error' => 'user_id can not be empty'
        ];
    }
}
if ($option == 'delete_message') {
	if (!empty($_POST['id']) && is_numeric($_POST['id']) && $_POST['id'] > 0) {
		$id = Secure($_POST['id']);
        $messages = $db->where("id",$id)->get(T_MESSAGES);
        $update1 = array();
        $update2 = array();
        $erase = array();
        $images = array();
        if (!empty($messages)) {
	        foreach ($messages as $key => $message) {
	            if ($message->from_deleted == 1 || $message->to_deleted == 1) {
	                $erase[] = $message->id;

	                $img = $message->text;
	                if( substr($img, 0, 5) == '[img]' && substr($img, -6) == '[/img]'){
	                    $img = str_replace(array('[img]','[/img]'), '', $img);
	                    if( @file_exists( $img ) ){
	                        $images[] = $img;
	                    }
	                }

	            } else {
	                if ($message->to_id == $music->user->id) {
	                    $update2[] = $message->id;
	                } else {
	                    $update1[] = $message->id;
	                }
	            }
	        }
	        if (!empty($erase)) {
	            $erase = implode(',', $erase);
	            $final_query = "DELETE FROM " . T_MESSAGES . " WHERE id IN ($erase)";
	            $db->rawQuery($final_query);
	            if(!empty($images)){
	                foreach ($images as $image){
	                    @unlink($image);
	                }
	            }
	        }
	        if (!empty($update1)) {
	            $update1 = implode(',', $update1);
	            $final_query = "UPDATE " . T_MESSAGES . " set `from_deleted` = '1' WHERE `id` IN({$update1}) ";
	            $db->rawQuery($final_query);
	        }
	        // if (!empty($update2)) {
	        //     $update2 = implode(',', $update2);
	        //     $final_query = "UPDATE " . T_MESSAGES . " set `to_deleted` = '1' WHERE `id` IN({$update2}) ";
	        //     $db->rawQuery($final_query);
	        // }
	        //$delete_chats = $db->rawQuery("DELETE FROM " . T_CHATS . " WHERE user_one = {$music->user->id} AND user_two = $id");
	        $data = array(
		        'status' => 200,
		        'data' => 'Message deleted'
		    );
	    }
	    else{
	    	$data = [
	            'status' => 400,
	            'error' => 'user not found'
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