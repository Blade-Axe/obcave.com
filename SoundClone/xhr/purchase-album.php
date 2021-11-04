<?php
$track_id = 0;
if (!empty($_GET['id'])) {
    $track_id = secure($_GET['id']);
}
if (empty($track_id)) {
    exit("Invalid Track ID");
}

$id = secure($_GET['id']);
$getAlbum = $db->where('album_id', $id)->getOne(T_ALBUMS);
if (empty($getAlbum)) {
    exit("Invalid Album ID");
}
if (empty($_GET['type'])) {
	exit("Invalid Type");
}


$data['status'] = 400;

if (IS_LOGGED == false) {
    $data['status'] = 300;
} else {
	if ($_GET['type'] == 'paypal') {
		$getLink = createPurchasePayPalAlbumLink($getAlbum);
	    if (!empty($getLink)) {
	        $data['status'] = 200;
	        $data['url'] = $getLink['url'];
	    }
	}
	elseif ($_GET['type'] == 'stripe') {
		require_once('assets/import/stripe-php-3.20.0/vendor/autoload.php');
		global $music;
		$data = array();
		$realprice      = Secure($_POST[ 'price' ]);
		$price          = Secure($_POST[ 'price' ]) * 100;
		$currency       = strtolower($music->config->stripe_currency);
		$token          = $_POST[ 'stripeToken' ];
		$album_id = $getAlbum->album_id;

		$stripe = array(
		    'secret_key' => $music->config->stripe_secret,
		    'publishable_key' => $music->config->stripe_id
		);
		\Stripe\Stripe::setApiKey($stripe[ 'secret_key' ]);
		try {
		    $customer = \Stripe\Customer::create(array(
		        'source' => $token
		    ));
		    $charge   = \Stripe\Charge::create(array(
		        'customer' => $customer->id,
		        'amount' => $price,
		        'currency' => $currency
		    ));
		    if ($charge) {
		    	$albumData = albumData($getAlbum->id, true, true, true);
		    	if (empty($albumData->price)) {
		    		header('Content-Type: application/json');
	                echo json_encode(array(
		                'status' => 400,
		                'message' => 'No Price'
		            ));
	                exit();
				}

				$getAdminCommission = $music->config->commission;
		        $final_price = 0;

		        $createPayment = false;
		        foreach ($albumData->songs as $key => $song){
		            $final_price += round((($getAdminCommission * $song->price) / 100), 2);
		            $addPurchase = [
		                'track_id' => $song->id,
		                'user_id' => $user->id,
		                'price' => $song->price,
		                'track_owner_id' => $song->user_id,
		                'final_price' => round((($getAdminCommission * $song->price) / 100), 2),
		                'commission' => $getAdminCommission,
		                'time' => time()
		            ];

		            $createPayment = $db->insert(T_PURCHAES, $addPurchase);
		            if ($createPayment) {
		                CreatePayment(array(
		                    'user_id'   => $user->id,
		                    'amount'    => $final_price,
		                    'type'      => 'TRACK',
		                    'pro_plan'  => 0,
		                    'info'      => $song->audio_id,
		                    'via'       => 'Stripe'
		                ));
		                $create_notification = createNotification([
		                    'notifier_id' => $user->id,
		                    'recipient_id' => $song->user_id,
		                    'type' => 'purchased',
		                    'track_id' => $song->id,
		                    'url' => "track/$song->audio_id"
		                ]);
		            }
		        }

		        if ($createPayment) {
		            $updatealbumpurchases = $db->where('album_id', $album_id)->update(T_ALBUMS, array('purchases' => $db->inc(1) ));
		            $addUserWallet = $db->where('id', $albumData->user_id)->update(T_USERS, ['balance' => $db->inc($final_price)]);
		            $data = array(
		                'status' => 200,
		                'url' => "$site_url/album/{$album_id}"
		            );
		    	} else {
		    		$data = array(
		                'status' => 400,
		                'message' => 'can not create payment'
		            );
		    	}
		    }
		}
		catch (Exception $e) {
		    $data = array(
		        'status' => 400,
		        'message' => $e->getMessage()
		    );
		}

	}
    
}
?>