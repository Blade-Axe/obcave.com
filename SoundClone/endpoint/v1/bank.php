<?php
if (!empty($_FILES['receipt_img']['tmp_name'])) {
    if ($_POST['mode'] == 'album' && !empty($_POST['track_id'])) {
        $getAlbum = $db->where('album_id', Secure($_POST['track_id']))->getOne(T_ALBUMS);
        $_POST['price'] = $getAlbum->price;
    }
    $file_info   = array(
        'file' => $_FILES['receipt_img']['tmp_name'],
        'size' => $_FILES['receipt_img']['size'],
        'name' => $_FILES['receipt_img']['name'],
        'type' => $_FILES['receipt_img']['type'],
        'crop' => array(
            'width' => 600,
            'height' => 600
        )
    );
    $file_upload = shareFile($file_info);
    if (!empty($file_upload['filename'])) {
        $thumbnail = secure($file_upload['filename'], 0);
        $info                  = array();
        $info[ 'user_id' ]     = $user->id;
        $info[ 'receipt_file' ]= $thumbnail;
        $info[ 'created_at' ]  = date('Y-m-d H:i:s');
        $info[ 'description' ] = (isset($_POST['description'])) ? Secure($_POST['description']) : '';
        $info[ 'price' ]       = (isset($_POST['price'])) ? Secure($_POST['price']) : '0';
        $info[ 'mode' ]        = (isset($_POST['mode'])) ? Secure($_POST['mode']) : '';
        $info[ 'track_id' ]    = (isset($_POST['track_id'])) ? Secure($_POST['track_id']) : '';
        $info[ 'approved' ]    = 0;
        $saved                 = $db->insert(T_BANK_RECEIPTS, $info);
        $data['status'] = 200;
	    $data['data'] = "Bank receipt created";

    }
    else{
    	$data = [
	        'status' => 400,
	        'error' => 'Invalid file'
	    ];
    }
}
else{
	$data = [
        'status' => 400,
        'error' => 'Please check your details'
    ];
}