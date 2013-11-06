<?php

class posts_controller extends base_controller {

    public function __construct() {
        parent::__construct();

        if(!$this->user) {
        	Router::redirect('/users/restricted');
        }
    }

    public function add() {

        $this->template->content = View::instance('v_posts_add');
		echo $this->template;
    }

    public function p_add() {

        $_POST['user_id'] = $this->user->user_id;
		$_POST['created'] = Time::now();
		$_POST['modified'] = $_POST['created'];

		DB::instance(DB_NAME)->insert('posts', $_POST);

        Router::redirect('/posts/index/posts.created/post_added');
    }

    public function p_delete($post_id = NULL) {

        if(!$post_id) {
		    Router::redirect('posts/error/post_not_found');
		}

		$q = 'SELECT user_id FROM posts WHERE post_id = '.$post_id;

		$post_user_id = DB::instance(DB_NAME)->select_field($q);

		if($this->user->user_id == $post_user_id) {
	    	DB::instance(DB_NAME)->delete('posts', 'WHERE post_id = '.$post_id);
        }

		Router::redirect('/posts/index/posts.created/post_deleted');
    }

    public function index($sort_order = 'posts.created',
    					  $confirmation = NULL) {

        $this->template->content = View::instance('v_posts_index');

		if($sort_order == 'posts.created') {
    	    $asc_desc = ' DESC';
		}
        else {
            $asc_desc = ' ASC';
        }

        $q = 'SELECT
        	  posts.post_id,
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

        switch($confirmation) {
        	case 'post_added':
        		$this->template->message = 'post successfully added';
        		break;
        	case 'post_deleted':
        		$this->template->message = 'post was deleted';
        		break;
        	default:
        }

		echo $this->template;
    }

    public function users() {

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

        $data = Array(
		    'created'          => Time::now(),
		    'user_id'          => $this->user->user_id,
		    'user_id_followed' => $user_id_followed
		);

		DB::instance(DB_NAME)->insert('users_users', $data);

        Router::redirect('/posts/users');

    }

    public function unfollow($user_id_followed) {

        $where_condition = 'WHERE user_id = '.$this->user->user_id.'
						    AND user_id_followed = '.$user_id_followed;

		DB::instance(DB_NAME)->delete('users_users', $where_condition);

		Router::redirect('/posts/users');
    }

    public function comments($post_id = NULL,
    						 $confirmation = NULL) {

    	if(!$post_id) {
    		Router::redirect('/posts/error/post_not_found');
    	}

    	$q = 'SELECT *
    		  FROM posts
    		  WHERE post_id = '.$post_id;

    	$post = DB::instance(DB_NAME)->select_row($q);
    	if(!$post) {
    		Router::redirect('/posts/error/post_not_found');
    	}

    	$q = 'SELECT
    		      comments.*,
    		      users.username
    		  FROM comments
    		  INNER JOIN users
    		  		  ON comments.user_id = users.user_id
    		  WHERE post_id = '.$post_id;

    	$comments = DB::instance(DB_NAME)->select_rows($q);

// CHECK TO MAKE SURE USER IS FOLLOWING THIS POST BEFORE DISPLAYING
        $q = 'SELECT
        	  posts.user_id AS user_id,
			  users.username AS username
		      FROM users_users
		      INNER JOIN posts
	 	              ON users_users.user_id_followed = posts.user_id
		      INNER JOIN users
		              ON posts.user_id = users.user_id
	    	  WHERE users_users.user_id = '.$this->user->user_id.'
	    	  AND posts.post_id = '.$post_id;

	    $post_user_followed = DB::instance(DB_NAME)->select_row($q);

    	if(!$post_user_followed) {
    		Router::redirect('/posts/error/not_followed');
    	}

    	$this->template->content = View::instance('v_posts_comments');
    	$this->template->content->post = $post;
    	$this->template->content->comments = $comments;
    	$this->template->content->post_user = $post_user_followed;
    	$this->template->content->logged_in_user_id = $this->user->user_id;

        switch($confirmation) {
        	case 'comment_added':
        		$this->template->message = 'comment successfully posted';
        		break;
        	case 'comment_deleted':
        		$this->template->message = 'comment was deleted';
        		break;
        	default:
        }

    	echo $this->template;
    }

    public function p_comment($post_id = NULL) {
    	if(!$post_id) {
    		Router::redirect('posts/error/post_not_found');
    	}

		$_POST['created'] = Time::now();
        $_POST['user_id'] = $this->user->user_id;
        $_POST['post_id'] = $post_id;

		DB::instance(DB_NAME)->insert('comments', $_POST);

        Router::redirect('/posts/comments/'.$post_id.'/comment_added');
    }

    public function p_delete_comment($comment_id = NULL) {
    	if(!$comment_id) {
    		Router::redirect('posts/error/comment_not_found');
    	}

		$q = 'SELECT * FROM comments WHERE comment_id = '.$comment_id;

		$comment = DB::instance(DB_NAME)->select_row($q);

		if($this->user->user_id == $comment['user_id']) {
	    	DB::instance(DB_NAME)->delete('comments', 'WHERE comment_id = '.$comment_id);
        }

		Router::redirect('/posts/comments/'.$comment['post_id'].'/comment_deleted');
    }

    public function error($error_type = NULL) {

    	$this->template->content = View::instance('v_posts_error');

    	switch($error_type) {
    		case 'post_not_found':
    			$this->template->content->error = 'Error: Post not found<br>';
    			break;
    		case 'comment_not_found':
    			$this->template->content->error = 'Error: Comment not found<br>';
    			break;
    		case 'not_followed':
    			$this->template->content->error = 'You must be following this user to view their posts.<br>';
    			break;
    		case 'impossible':
    			$this->template->content->error = 'Congratulations! You have encountered an impossible error.<br>';
    			break;
    		default:
    			$this->template->content->error = 'You have encountered an unexpected error.<br>';
    	}

    	echo $this->template;
    }

} #eoc
