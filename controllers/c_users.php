<?php

// HANDLES USER-RELATED METHODS
class users_controller extends base_controller {

	// CONSTRUCTOR
    public function __construct() {
        parent::__construct();
    }

    // SINCE /USERS/INDEX IS A NATURAL PLACE TO LOOK FOR THE USER LIST
    public function index() {
        Router::redirect('/posts/users');
    }

    // ALLOWS USERS TO CREATE ACCOUNTS WITH VARIOUS VALIDATIONS
    public function signup() {

        // Generate view, initialize errors
        $this->template->content = View::instance('v_users_signup');
        $this->template->content->error = '<br>';
		$errors = Array();
		$error_flag = false;

		// First time to page, not processing any data, just display
		if(!$_POST) {
	    	echo $this->template;
	    	return;
		}

		// Sanitization???
		$_POST = DB::instance(DB_NAME)->sanitize($_POST);

		// Checking for existing emails and usernames
		$e = 'SELECT user_id
		      FROM users
		      WHERE email = "'.$_POST['email'].'"
		      LIMIT 1';

		$u = 'SELECT user_id
		      FROM users
		      WHERE username = "'.$_POST['username'].'"
		      LIMIT 1';

		$email_exists = DB::instance(DB_NAME)->select_field($e);
		$username_exists = DB::instance(DB_NAME)->select_field($u);

		// If errors, add them to the errors array
		if($email_exists || $username_exists) {
	    	$error_flag = true;
	    	if($email_exists) {
                array_push($errors,'That email address is already in use.');
            }
	    	if($username_exists) {
	        	array_push($errors,'That username is already in use.');
	    	}
		}

		// Prevent blank fields. This is also being done client-side, but in case
		//   user's browser is antiquated, done server-side anyway
		foreach($_POST as $prompt => $value) {
		    if(trim($value) == '') {
	    	    $error_flag = true;
				array_push($errors, $prompt.' cannot be blank.');
	    	}
		}

		// Implementation of password re-entry confirmation,
		//   which also helps people remember what password they used
		//   (apparently. This was a suggestion from a friend)
		if($_POST['password'] != $_POST['password_check']) {
	    	$error_flag = true;
	    	array_push($errors, 'Password entries did not match.');
		}

		// Limit usernames to 16 characters because otherwise the user list
		//   table gets ugly. Yes, this was my workaround
		if(strlen($_POST['username']) > 16) {
			$error_flag = true;
			array_push($errors, 'Username cannot be longer than 16 characters.');
		}

		// Send off the errors
		$this->template->content->errors = $errors;	

		// If there was an error, don't process any more, just make them do it again
		if($error_flag) {
	    	echo $this->template;
		}
		// Otherwise, put them into the database
		else {
	    	unset($_POST['password_check']);
	    	$_POST['created'] = Time::now();
	    	$_POST['modified'] = $_POST['created'];
	    	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
	    	$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());

            DB::instance(DB_NAME)->insert_row('users', $_POST);

            // This implements auto-self-following
	    	$new_user_id = DB::instance(DB_NAME)->select_field($e);

	    	$auto_follow = Array(
	        	'created'          => Time::now(),
	        	'user_id'          => $new_user_id,
	        	'user_id_followed' => $new_user_id
	    	);

	    	$new_user = DB::instance(DB_NAME)->insert_row('users_users', $auto_follow);

	    	// This implements auto-login upon signup
            if($new_user) {
	        	setcookie('token', $_POST['token'], strtotime('+1 month'), '/');
	    	}

	    	// Display signup confirmation
	    	Router::redirect('/index/index/signed_up');
		}
    }

    // DISPLAYS LOGIN PAGE WHERE USERS CAN LOG IN
    public function login($message = NULL) {

    	// Send them to the home page if they're already logged in
    	if($this->user) {
    		Router::redirect('/');
    	}

		$this->template->content = View::instance('v_users_login');

		// Handle error message for failed login attempts
		if($message == 'failed') {
			$this->template->message = 'Login failed. Please try again.';
		}

		echo $this->template;
    }

	// ALLOWS USERS TO LOG IN WITH VARIOUS VALIDATIONS
    public function p_login() {

    	// Do not allow blank fields
    	if((trim($_POST['email']) == '') || (trim($_POST['password']) == '')) {
    		Router::redirect('/users/login/failed');
    	}

    	// Sanitize???
    	$_POST = DB::instance(DB_NAME)->sanitize($_POST);

    	// Get encrypted password
		$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

		// Check password against database
		$q = 'SELECT token
		      FROM users
		      WHERE email = "'.$_POST['email'].'"
		      AND password = "'.$_POST['password'].'"';

		$token = DB::instance(DB_NAME)->select_field($q);

		// If their info is correct, log them in with confirmation message
		if($token) {
	    	setcookie('token', $token, strtotime('+1 month'), '/');
	    	Router::redirect('/index/index/logged_in');
		}
		else {
			// Fail login attempt with error message
			Router::redirect('/users/login/failed');	
		}
    }

    // LOGS USER OUT WITH CONFIRMATION MESSAGE
    public function logout() {

    	// Can't get to this option if not logged in
        if(!$this->user) {
        	Router::redirect('/users/restricted');
        }

        // Reset token for security
        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

		$data = Array('token' => $new_token);

		DB::instance(DB_NAME)->update('users',$data, 'WHERE user_id = '.$this->user->user_id);

		// Log out
		setcookie('token', '', strtotime('-1 year'), '/');

		// With confirmation
		Router::redirect('/index/index/logged_out');
    }

    // DISPLAYS THE GIVEN USER'S PROFILE
    public function profile($user_name = NULL) {

    	// Can't see any profiles if not logged in
    	if(!$this->user) {
            Router::redirect('/users/restricted');
		}

		// Sanitize????
		$user_name = DB::instance(DB_NAME)->sanitize($user_name);

		// Default if no username specified is the logged-in user's profile
        if(!$user_name) {
	       	$user_name = $this->user->username;
        }

        // Get profile info from DB
        $q = 'SELECT
              users.user_id,
		      users.username,
              users.first_name,
		      users.last_name,
		      users.email,
		      users.profile_photo
	          FROM users
	          WHERE users.username = "'.$user_name.'"
	          LIMIT 1';

	    $profile = DB::instance(DB_NAME)->select_row($q);

	    // Generate view and content
        $this->template->content = View::instance('v_users_profile');
	    $this->template->title = APP_NAME.' user profile: '.$user_name;
	    $this->template->content->profile = $profile;

	    // If valid username specified and the logged-in user is following
	    //   the specified user, display the specified user's posts
	    if($profile) {
	    	$q = 'SELECT user_user_id
	    	      FROM users_users
				  WHERE user_id = "'.$this->user->user_id.'"
				  AND user_id_followed = '.$profile['user_id'];

	        $display_posts = DB::instance(DB_NAME)->select_field($q);

		    if($display_posts) {
		        $q = 'SELECT *
		              FROM posts
				      WHERE user_id = '.$profile['user_id'].'
				      ORDER BY created DESC';

		        $user_posts = DB::instance(DB_NAME)->select_rows($q);
		        $this->template->content->user_posts = $user_posts;
	    	}
	    }

	    // I think I forgot you can just use $user on all views. Oh well,
	    //   send some useful data to the view anyway
	    $this->template->content->logged_in_user_id = $this->user->user_id;
	    $this->template->content->profile_username = $user_name;

	    // local stuff
	    $client_files_head = Array('/css/profile.css','/css/master.css');
	    $this->template->client_files_head = Utils::load_client_files($client_files_head);

	    $client_files_body = Array('/js/master.js');
	    $this->template->client_files_body = Utils::load_client_files($client_files_body);

	    // Render view
        echo $this->template;
    }

    // For pages that can only be viewed by a logged-in user, send them to a
    //   page with an "access restricted" message
    public function restricted() {

		$this->template->content = View::instance('v_users_restricted');
		echo $this->template;
    }

} #eoc
