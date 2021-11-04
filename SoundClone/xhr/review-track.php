<?php
if (IS_LOGGED == false) {
    exit("You ain't logged in!");
}

$track_stars = 0;
if (!empty($_REQUEST['track_stars'])) {
    $track_stars = secure($_REQUEST['track_stars']);
}
if (empty($track_stars)) {
    exit("Invalid Rating");
}

$track_id = 0;
if (!empty($_REQUEST['id'])) {
    $track_id = secure($_REQUEST['id']);
}
if (empty($track_id)) {
    exit("Invalid Track ID");
}

$getTrack = $db->where('id', $track_id)->getOne(T_SONGS);
if (empty($getTrack)) {
    exit("Invalid Track ID");
}

$data['status'] = 400;

if (empty($_POST['track_review_description'])) {
    $errors[] = lang("Please describe your request.");
}

if (empty($errors)) {
    $description = secure($_POST['track_review_description']);
    $insert_report = $db->insert(T_REVIEWS, ['track_id' => $track_id, 'description' => $description, 'time' => time(), 'user_id' => $user->id, 'rate' => $track_stars]);
    if ($insert_report) {

        $delete_notification = $db->where('notifier_id', $user->id)->where('recipient_id', $getTrack->user_id)->where('type', 'reviewed_track')->where('track_id', $getTrack->id)->delete(T_NOTIFICATION);
        deleteActivity([
            'user_id' => $user->id,
            'type' => 'reviewed_track',
            'track_id' => $getTrack->id,
        ]);

        $create_notification = createNotification([
            'notifier_id' => $user->id,
            'recipient_id' => $getTrack->user_id,
            'type' => 'reviewed_track',
            'track_id' => $getTrack->id,
            'url' => "track-reviews/$getTrack->audio_id"
        ]);
        $create_activity = createActivity([
            'user_id' => $user->id,
            'type' => 'reviewed_track',
            'track_id' => $getTrack->id,
        ]);

        RecordUserActivities('review_track',array('track_user_id' => $getTrack->user_id));
        $data['status'] = 200;
    }
} else {
    $data['status'] = 400;
    $data['errors'] = $errors;
}