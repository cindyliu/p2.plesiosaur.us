<?php

class posts_controller extends base_controller {

    public function __construct() {
        parent::__construct();
    }

    public function add() {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $this->template->content = View::instance("v_posts_add");
	echo $this->template;

    }

    public function p_add() {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $_POST['user_id'] = $this->user->user_id;
	$_POST['created'] = Time::now();
	$_POST['modified'] = $_POST['created'];

	DB::instance(DB_NAME)->insert('posts', $_POST);

        Router::redirect('/posts/index');

    }

    public function delete($post_id = NULL) {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        if(!$post_id) {
	    die('You cannot delete a post you do not own.');
	}

	$q = 'SELECT user_id FROM posts WHERE post_id = '.$post_id;

	$post_user_id = DB::instance(DB_NAME)->select_field($q);

	if($this->user->user_id == $post_user_id) {
	    DB::instance(DB_NAME)->delete(posts, 'WHERE post_id = '.$post_id);
        }

	Router::redirect('/users/profile');	
    }

    public function index($sort_order = 'posts.created') {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $this->template->content = View::instance('v_posts_index');

	if($sort_order == 'posts.created') {
            $asc_desc = ' DESC';
	}
        else {
            $asc_desc = ' ASC';
        }

        $q = 'SELECT
	          posts.content,
		  posts.created,
        	  posts.user_id AS post_user_id,
		  users_users.user_id AS follower_id,
		  users.first_name,
        	  users.last_name,
		  users.username
	      FROM posts
	      INNER JOIN users_users
	              ON posts.user_id = users_users.user_id_followed
	      INNER JOIN users
	              ON posts.user_id = users.user_id
	      WHERE users_users.user_id = '.$this->user->user_id.'
	      ORDER BY '.$sort_order.$asc_desc;

        $posts = DB::instance(DB_NAME)->select_rows($q);

	$this->template->content->sort_order = $sort_order;
        $this->template->content->posts = $posts;
	echo $this->template;

    }

    public function users() {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $this->template->content = View::instance('v_posts_users');

        $q = 'SELECT *
	      FROM users
	      WHERE user_id != '.$this->user->user_id.'
	      ORDER BY username ASC';

	$users = DB::instance(DB_NAME)->select_rows($q);

	$q = 'SELECT *
	      FROM users_users
	      WHERE user_id = '.$this->user->user_id;

	$follows = DB::instance(DB_NAME)->select_array($q, 'user_id_followed');

	$this->template->content->users = $users;
	$this->template->content->follows = $follows;
        echo $this->template;

    }

    public function follow($user_id_followed) {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $data = Array(
	    'created' => Time::now(),
	    'user_id' => $this->user->user_id,
	    'user_id_followed' => $user_id_followed
	);

	DB::instance(DB_NAME)->insert('users_users', $data);

        Router::redirect('/posts/users');

    }

    public function unfollow($user_id_followed) {

        if(!$this->user) {
            $this->template->content = View::instance('v_users_restricted');
	    echo $this->template;
	    die();
        }

        $where_condition = 'WHERE user_id = '.$this->user->user_id.'
			    AND user_id_followed = '.$user_id_followed;
	DB::instance(DB_NAME)->delete('users_users', $where_condition);

	Router::redirect('/posts/users');

    }

}

?>
