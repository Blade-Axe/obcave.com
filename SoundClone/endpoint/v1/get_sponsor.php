<?php
$ad_data = get_user_ads(rand(1,2));
$ad_info = array();
if (!empty($ad_data)) {
    $ad_data->user_data      = UserData($ad_data->user_id);
    unset($ad_data->user_data->password);
    if ($ad_data->type == 2 && $ad_data->user_id !== $user->id) {
        register_ad_views($ad_data->id, $ad_data->user_id);
    }
    $ad_data->re_url = $music->config->site_url.'/redirect/'.$ad_data->id.'?type=pagead';
    if (!empty($ad_data->media)) {
    	$ad_data->media = getMedia($ad_data->media);
    }
    if (!empty($ad_data->audio_media)) {
    	$ad_data->audio_media = getMedia($ad_data->audio_media);
    }
    $ad_data->url = urldecode($ad_data->url);
    
    $ad_info = $ad_data;
}
$data['status'] = 200;
$data['data'] = $ad_info;