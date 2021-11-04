<?php 
if ($option == 'login') {
	if (!empty($_POST)) {
	    if (empty($_POST['username']) || empty($_POST['password'])) {
	    	if ($music->config->prevent_system == 1) {
                AddBadLoginLog();
            }
	        $errors[] = lang("Please check your details");
	    } else {
	    	if ($music->config->prevent_system == 1) {
	            if (!CanLogin()) {
	                $errors[] = lang("Too many login attempts please try again later");
	                header("Content-type: application/json");
	                echo json_encode(array(
				        'status' => 400,
				        'errors' => $errors
				    ));
	                exit();
	            }
	        }
	        $username        = secure($_POST['username']);
	        $password        = secure($_POST['password']);
            $phone           = 0;

	        $getUser = $db->where("(username = ? or email = ?)", array(
	            $username,
	            $username
	        ))->getOne(T_USERS, ["password", "id", "active","admin"]);

	        if (empty($getUser)) {
	        	if ($music->config->prevent_system == 1) {
	                AddBadLoginLog();
	            }
	        	$errors[] = lang("Incorrect username or password");
	        } else if (!password_verify($password, $getUser->password)) {
	        	if ($music->config->prevent_system == 1) {
	                AddBadLoginLog();
	            }
	        	$errors[] = lang("Incorrect username or password");
	        } else if ($getUser->active == 0) {
	        	$errors[] = lang("Your account is not activated yet, please check your inbox for the activation link");
	        }

            if ( $music->config->maintenance_mode == 'on' ) {
                if ($getUser->admin === 0) {
                    $errors[] = lang("Website maintenance mode is active, Login for user is forbidden");
                }
            }
            if (empty($errors)) {
            	if (VerifyIP($getUser->id) === false) {
	                $_SESSION['code_id'] = $getUser->id;
	                $data                = array(
	                    'status' => 600,
	                    'location' => getLink('unusual-login')
	                );
	                $phone               = 1;
	            }
	            if (TwoFactor($getUser->id) === false) {
	                $_SESSION['code_id'] = $getUser->id;
	                $data                = array(
	                    'status' => 600,
	                    'location' => getLink('unusual-login?type=two-factor')
	                );
	                $phone               = 1;
	            }
            }
            
	        if (empty($errors) && $phone == 0) {
	            createUserSession($getUser->id);
	            $music->loggedin = true;
	            $music->user = userData($getUser->id);
                $data = array(
		            'status' => 200,
		            'header' => loadPage('header/logged_head', [
		            	'site_search_bar' => loadPage('header/search-bar'), 
		            ])
		        );
	        }
	    }
	}
}

if ($option == 'forgot-password') {
	if (!empty($_POST)) {
	    if (empty($_POST['email'])) {
	        $errors[] = lang("Please check your details");
	    } else {
	        $email        = secure($_POST['email']);

	        $getUser = $db->where("email = ?", array(
	            $email,
	        ))->getOne(T_USERS, ["password", "id", "active", "email_code"]);

	        if (empty($getUser)) {
	        	$errors[] = lang("This e-mail is not found");
	        }

            if ( $music->config->maintenance_mode == 'on' ) {
                $errors[] = lang("Website maintenance mode is active");
            }

	        if (empty($errors)) {
	        	$user_id             = $getUser->id;
	            $email_code          = sha1(rand(11111, 99999) . $getUser->password);
                $rest_user           = userData($user_id);
	            $update              = $db->where('id', $getUser->id)->update(T_USERS, ['email_code' => $email_code]);

	            $update_data['USER_DATA'] = $rest_user;
	            $update_data['email_code'] = $email_code;

	            $send_email_data = array(
	           		'from_email' => $music->config->email,
	           		'from_name' => $music->config->name,
	           		'to_email' => $email,
	           		'to_name' => $rest_user->name,
	           		'subject' => lang("Reset Password"),
	           		'charSet' => 'UTF-8',
	           		'message_body' => loadPage('emails/reset-password', $update_data),
	           		'is_html' => true
	           	);

	            $send_message = sendMessage($send_email_data);
	            if ($send_message) {
	            	$data = array(
			            'status' => 200,
			            'message' => lang("Please check your inbox / spam folder for the reset email.")
			        );
	            } else {
	            	$errors[] = lang("Error found while sending the reset link, please try again later.");
		        }
            }
	    }
	}
}

if ($option == 'reset-password') {
	if (!empty($_POST)) {
	    if (empty($_POST['password']) || empty($_POST['c_password']) || empty($_POST['email_code'])) {
	        $errors[] = lang("Please check your details");
	    } else {
	        $password        = secure($_POST['password']);
	        $c_password  = secure($_POST['c_password']);
	        $old_email_code = secure($_POST['email_code']);

	        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
	        if ($password != $c_password) {
	            $errors[] = lang("Passwords don't match");
	        } else if (strlen($password) < 4 || strlen($password) > 32) {
	            $errors[] = lang("Password is too short");
	        }

            if ( $music->config->maintenance_mode == 'on' ) {
                $errors[] = lang("Website maintenance mode is active");
            }
	        if (empty($errors)) {

	        	$user_id = $db->where('email_code', $old_email_code)->getValue(T_USERS, "id");
	        	$email_code = sha1(time() + rand(1111,9999));
	        	$update = $db->where('id', $user_id)->update(T_USERS, ['password' => $password_hashed, 'email_code' => $email_code]);
	        	if ($update) {
	        		createUserSession($user_id);
		            $data = ['status' => 200];
	        	}
            }
	    }
	}
}

if ($option == 'signup') {
	if (( isset($_GET['invite']) && !empty($_GET['invite']) && !IsAdminInvitationExists( $_GET[ 'invite' ] ))) {
		$data = array(
            'status' => 200,
            'link' => $site_url
        );
        header("Content-type: application/json");
        echo json_encode($data);
        exit();
	}
    $fields = GetWelcomeFields();
	if (!empty($_POST)) {
	    if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['c_password']) || empty($_POST['name'])) {
	        $errors[] = lang("Please check your details");
	    } else {

            if ( $music->config->user_registration == 'on' && isset( $_GET[ 'invite' ] ) && !IsAdminInvitationExists( $_GET[ 'invite' ] ) ) {
                $data = array(
                    'status' => 200,
                    'link' => $site_url
                );
                header("Content-type: application/json");
                echo json_encode($data);
                exit();
            }

	        $username        = secure($_POST['username']);
	        $name            = secure($_POST['name']);
	        $password        = secure($_POST['password']);
	        $c_password      = secure($_POST['c_password']);
	        $password_hashed = password_hash($password, PASSWORD_DEFAULT);
	        $email           = secure($_POST['email']);
	        if (UsernameExits($_POST['username'])) {
	            $errors[] = lang("This username is already taken");
	        }
	        if (strlen($_POST['username']) < 4 || strlen($_POST['username']) > 32) {
	            $errors[] = lang("Username length must be between 5 / 32");
	        }
	        if (!preg_match('/^[\w]+$/', $_POST['username'])) {
	            $errors[] = lang("Invalid username characters");
	        }
            if (in_array($_POST['username'],$music->disallowed_usernames)){
                $errors[] = lang("This username is disallowed");
            }
	        if (EmailExists($_POST['email'])) {
	            $errors[] = lang("This e-mail is already taken");
	        }
	        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	            $errors[] = lang("This e-mail is invalid");
	        }
	        if ($password != $c_password) {
	            $errors[] = lang("Passwords don't match");
	        }
	        if (strlen($password) < 4) {
	            $errors[] = lang("Password is too short");
	        }
	        if ($music->config->recaptcha == 'on') {
	            if (!isset($_POST['g-recaptcha-response']) || empty($_POST['g-recaptcha-response'])) {
	                $errors[] = lang("Please check the re-captcha");
	            }
	        }

            if ( $music->config->maintenance_mode == 'on' ) {
                $errors[] = lang("Website maintenance mode is active");
            }
            if (!empty($fields) && count($fields) > 0) {
                foreach ($fields as $key => $field) {
                    if (empty($_POST[$field['fid']])) {
                        $errors[] = $field['name'] . ' ' . lang('is required');
                    }
                    if (mb_strlen($_POST[$field['fid']]) > $field['length']) {
                        $errors[] = $field['name'] . ' ' . lang('field max characters is') . ' ' . $field['length'];
                    }
                }
            }
            $field_data = array();
	        $active = ($music->config->validation == 'on') ? 0 : 1;
	        if (empty($errors)) {

                if (!empty($fields) && count($fields) > 0) {
                    foreach ($fields as $key => $field) {
                        if (!empty($_POST[$field['fid']])) {
                            $_name = $field['fid'];
                            if (!empty($_POST[$_name])) {
                                $field_data[] = array(
                                    $_name => $_POST[$_name]
                                );
                            }
                        }
                    }
                }

	            $email_code = sha1(time() + rand(111,999));
	            $insert_data = array(
	                'username' => $username,
	                'password' => $password_hashed,
	                'email' => $email,
	                'name' => $name,
	                'ip_address' => get_ip_address(),
	                'active' => $active,
	                'email_code' => $email_code,
	                'last_active' => time(),
	                'registered' => date('Y') . '/' . intval(date('m')),
	                'time' => time()
	            );
	            $insert_data['language'] = $music->config->language;
	            if (!empty($_SESSION['lang'])) {
	                if (in_array($_SESSION['lang'], $langs)) {
	                    $insert_data['language'] = $_SESSION['lang'];
	                }
	            }

	            if (!empty($_SESSION['ref']) && $music->config->affiliate_type == 0) {
	            	$ref_user_id = $db->where('username', Secure($_SESSION['ref']))->getValue(T_USERS, 'id');
		            if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
		                $insert_data['referrer'] = Secure($ref_user_id);
		                $insert_data['src']      = Secure('Referrer');
		                $db->where('username', Secure($_SESSION['ref']))->update(T_USERS,array('balance' => $db->inc($music->config->amount_ref)));
		                unset($_SESSION['ref']);
		            }
		        }
		        elseif (!empty($_SESSION['ref']) && $music->config->affiliate_type == 1) {
		            $ref_user_id = $db->where('username', Secure($_SESSION['ref']))->getValue(T_USERS, 'id');
		            if (!empty($ref_user_id) && is_numeric($ref_user_id)) {
		                $insert_data['ref_user_id']      = Secure($ref_user_id);
		            }
		        }

	            $user_id             = $db->insert(T_USERS, $insert_data);
	            if (!empty($user_id)) {
                    if (!empty($field_data)) {
                        $insert  = UpdateUserCustomData($user_id, $field_data, false);
                    }
	                if ($music->config->validation == 'on') {
	                     $link = $email_code . '/' . $email; 
	                     $data['EMAIL_CODE'] = $link;
	                     $data['USERNAME']   = $username;
	                     $send_email_data = array(
	                        'from_email' => $music->config->email,
	                        'from_name' => $music->config->name,
	                        'to_email' => $email,
	                        'to_name' => $username,
	                        'subject' => lang("Confirm your account"),
	                        'charSet' => 'UTF-8',
	                        'message_body' => loadPage('emails/confirm-account', $data),
	                        'is_html' => true
	                    );
	                    $send_message = sendMessage($send_email_data);
	                    $data = array(
				            'status' => 403,
				            'message' => lang("Registration successful! We have sent you an email, Please check your inbox/spam to verify your account.")
				        );
	                } else {
	                	createUserSession($user_id);
	                	$music->loggedin = true;
	                    $music->user = userData($user_id);

                        $autoFollow = false;
                        if (!empty($music->config->auto_friend_users)) {
                            $autoFollow = AutoFollow($user_id);
                        }

                        if (isset($_GET['invite']) && IsAdminInvitationExists($_GET['invite'])){
                            $db->where('code', secure($_GET['invite']))->update(T_INVITATIONS, array('status' => 'Active'));
                        }

	                    $data = array(
				            'status' => 200,
				            'autoFollow' => $autoFollow,
				            'header' => loadPage('header/logged_head', [
				            	'site_search_bar' => loadPage('header/search-bar'), 
				            ])
				        );
	                }
	            }
	        }
	    }
	}
}
?>