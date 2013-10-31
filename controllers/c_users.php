<?php
class users_controller extends base_controller {

    public function __construct() {
        parent::__construct();
//	echo "users_controller construct called<br><br>";
    }

    public function index() {
        Router::redirect('/posts/users');
    }

    public function signup() {

	# Set up the view
	$this->template->content = View::instance('v_users_signup');

	# Render the view
	echo $this->template;
    }


    public function p_signup() {

	$_POST['created'] = Time::now();
	$_POST['modified'] = $_POST['created'];
	$_POST['password'] = sha1(PASSWORD_SALT.$_POST['password']);
	$_POST['token'] = sha1(TOKEN_SALT.$_POST['email'].Utils::generate_random_string());


//	echo "<pre>";
//	print_r($_POST);
//	echo "</pre>";

	$q = 'SELECT user_id
	      FROM users
	      WHERE email = "'.$_POST['email'].'"
	      LIMIT 1';

        if($q) {
	    $this->template->content = View::instance('v_users_signup');
	    $this->template->content = 'That email address is already in use.<br>'.$this->template->content;
	    echo $this->template;
	    die();
	}

        DB::instance(DB_NAME)->insert_row('users', $_POST);

	$new_user_id = DB::instance(DB_NAME)->select_field($q);

	$auto_follow = Array(
	    'created' => Time::now(),
	    'user_id' => $new_user_id,
	    'user_id_followed' => $new_user_id
	    );

	DB::instance(DB_NAME)->insert_row('users_users', $auto_follow);

	Router::redirect('/users/login/');

    }

    public function login() {

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

        $new_token = sha1(TOKEN_SALT.$this->user->email.Utils::generate_random_string());

	$data = Array('token' => $new_token);

	DB::instance(DB_NAME)->update('users',$data, 'WHERE user_id = '.$this->user->user_id);

	setcookie('token', '', strtotime('-1 year'), '/');

	Router::redirect('/');

    }

    public function profile($user_name = NULL) {

    	if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
	}
        else {
            if(!$user_name) {
	        $user_name = $this->user->username;
            }
            $q = 'SELECT
	              users.username,
	              users.first_name,
		      users.last_name,
		      users.email,
		      users.profile_photo
	          FROM users
	          WHERE users.username = "'.$user_name.'"
	          LIMIT 1';

	    $profile = DB::instance(DB_NAME)->select_row($q);

	    $q = 'SELECT *
	          FROM posts
		  WHERE user_id = '.$this->user->user_id.'
		  ORDER BY created DESC';

	    $user_posts = DB::instance(DB_NAME)->select_rows($q);

            $this->template->content = View::instance('v_users_profile');
	    $this->template->title = 'User Profile: '.$user_name;

	    $client_files_head = Array('/css/profile.css','/css/master.css');
	    $this->template->client_files_head = Utils::load_client_files($client_files_head);

	    $client_files_body = Array('/js/master.js');
	    $this->template->client_files_body = Utils::load_client_files($client_files_body);

	    $this->template->content->profile = $profile;
	    $this->template->content->user_posts = $user_posts;
	    $this->template->content->display_posts = ($user_name == $this->user->username);

            echo $this->template;
        }
    }

} #eoc
