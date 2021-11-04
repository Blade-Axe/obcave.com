<?php
if ($option == 'get_modal') {
    $types = array('pro','wallet','pay','subscribe');
    $data['status'] = 400;
    if (!empty($_POST['type']) && in_array($_POST['type'], $types)) {
        $user = $db->where('id',$music->user->id)->getOne(T_USERS);

        $price = 0;
        $video_id = 0;
        $user_id = 0;
        if (!empty($_POST['price'])) {
            $price = Secure($_POST['price']);
        }
        if (!empty($_POST['user_id'])) {
            $user_id = Secure($_POST['user_id']);
        }

        $music->show_wallet = 1;

        $html = LoadPage('modals/wallet-payment-modal',array('TYPE' => Secure($_POST['type']),'PRICE' => $price,'USER_ID' => $user_id));
        if (!empty($html)) {
            $data['status'] = 200;
            $data['html'] = $html;
        }
    }
}