<?php
class users_controller extends base_controller {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        Router::redirect('/posts/users');
    }

    public function signup() {

        $this->template->content = View::instance('v_users_signup');
        $this->template->content->error = '<br>';
		$errors = Array();
		$error_flag = false;

		if(!$_POST) {
	    	echo $this->template;
	    	return;
		}

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

		if($email_exists || $username_exists) {
	    	$error_flag = true;
	    	if($email_exists) {
                array_push($errors,'That email address is already in use.');
            }
	    	if($username_exists) {
	        	array_push($errors,'That username is already in use.');
	    	}
		}

		foreach($_POST as $prompt => $value) {
		    if($value == '') {
	    	    $error_flag = true;
				array_push($errors, $prompt.' cannot be blank.');
	    	}
		}

		if($_POST['password'] != $_POST['password_check']) {
	    	$error_flag = true;
	    	array_push($errors, 'Password entries did not match.');
		}

		$this->template->content->errors = $errors;	

		if($error_flag) {
	    	echo $this->template;
		}
		else {
	    	unset($_POST['password_check']);
	    	$_POST['created'] = Time::now();
	    	$_POST['modified'] = $_POST['created'];
	    	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
	    	$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());

            DB::instance(DB_NAME)->insert_row('users', $_POST);

	    	$new_user_id = DB::instance(DB_NAME)->select_field($e);

	    	$auto_follow = Array(
	        	'created'          => Time::now(),
	        	'user_id'          => $new_user_id,
	        	'user_id_followed' => $new_user_id
	    	);

	    	$new_user = DB::instance(DB_NAME)->insert_row('users_users', $auto_follow);

            if($new_user) {
	        	setcookie('token', $_POST['token'], strtotime('+1 month'), '/');
	    	}

	    	Router::redirect('/');
		}
    }

    public function login() {

    	if($this->user) {
    		Router::redirect('/');
    	}

		$this->template->content = View::instance('v_users_login');

		echo $this->template;
    }

  
    public function p_login() {

		$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);

		$q = 'SELECT token
		      FROM users
		      WHERE email = "'.$_POST['email'].'"
		      AND password = "'.$_POST['password'].'"';

		$token = DB::instance(DB_NAME)->select_field($q);

		if($token) {
	    	setcookie('token', $token, strtotime('+1 month'), '/');
	    	Router::redirect('/');
		}
		else {
	    	$this->template->content = View::instance('v_users_login');
	    	$this->template->content = "<p>Login failed. Please try again.<p>".$this->template->content;
	    	echo $this->template;
		}
    }


    public function logout() {

        if(!$this->user) {
        	Router::redirect('/users/restricted');
        }

        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

		$data = Array('token' => $new_token);

		DB::instance(DB_NAME)->update('users',$data, 'WHERE user_id = '.$this->user->user_id);

		setcookie('token', '', strtotime('-1 year'), '/');

		Router::redirect('/');
    }

    public function profile($user_name = NULL) {

    	if(!$this->user) {
            Router::redirect('/users/restricted');
		}

        if(!$user_name) {
	       	$user_name = $this->user->username;
        }

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

        $this->template->content = View::instance('v_users_profile');
	    $this->template->title = APP_NAME.' user profile: '.$user_name;
	    $this->template->content->profile = $profile;

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

	    $this->template->content->logged_in_user_id = $this->user->user_id;

	    $client_files_head = Array('/css/profile.css','/css/master.css');
	    $this->template->client_files_head = Utils::load_client_files($client_files_head);

	    $client_files_body = Array('/js/master.js');
	    $this->template->client_files_body = Utils::load_client_files($client_files_body);

        echo $this->template;
    }


    public function restricted() {

		$this->template->content = View::instance('v_users_restricted');

		echo $this->template;
    }

} #eoc

?>
