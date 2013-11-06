<?php

// HANDLES POST-RELATED METHODS
class posts_controller extends base_controller {

    // CONSTRUCTOR
    public function __construct() {
        parent::__construct();

        if(!$this->user) {
        	Router::redirect('/users/restricted');
        }
    }

    // ALLOWS USER TO ADD POST TO DATABASE WITH SOME VALIDATION
    public function add() {

       // Generate view
        $this->template->content = View::instance('v_posts_add');

        // First time to page, not processing any data, just display
        if(!$_POST) {
            echo $this->template;
            return;
        }

        // Make sure post is less than 256 characters long (client-side validation too)
        if(strlen($_POST['content']) > 255) {
            $this->template->content->error = 'Post must not be longer than 255 characters.';
        }

        // Make sure post isn't empty
        if(trim($_POST['content']) == '') {
            $this->template->content->error = 'Post cannot be blank.';
        }

        // If either of the above errors, stop and make them do it again
        if(isset($this->template->content->error)) {
            echo $this->template;
            return;
        }

        // Add post to database. Relying on insert_row()'s internal sanitization here
        $_POST['user_id'] = $this->user->user_id;
		$_POST['created'] = Time::now();
		$_POST['modified'] = $_POST['created'];

		DB::instance(DB_NAME)->insert_row('posts', $_POST);

        // Confirmation message
        Router::redirect('/posts/index/posts.created/post_added');
    }

    // LETS USER DELETE A POST WITH SOME VALIDATION
    public function p_delete($post_id = NULL) {

        // Can't delete a post you haven't specified
        if(!$post_id) {
		    Router::redirect('posts/error/post_not_found');
		}

        // Just in case???
        $post_id = DB::instance(DB_NAME)->sanitize($post_id);

        // Just in case, make sure the post belongs to the logged-in user
        //   so people can't delete other people's posts
		$q = 'SELECT user_id FROM posts WHERE post_id = '.$post_id;

		$post_user_id = DB::instance(DB_NAME)->select_field($q);

        // Delete if logged-in user owns post
		if($this->user->user_id == $post_user_id) {
	    	DB::instance(DB_NAME)->delete('posts', 'WHERE post_id = '.$post_id);
        }

        // Send confirmation message
		Router::redirect('/posts/index/posts.created/post_deleted');
    }

    // DISPLAYS FEED OF FOLLOWED USERS' POSTS
    public function index($sort_order = 'posts.created',
    					  $confirmation = NULL) {

        // Generate view
        $this->template->content = View::instance('v_posts_index');

        // Implements post sorting by timestamp or username
        //   (just a silly little feature I liked)
        //   also "sanitizes" by default I think
		if($sort_order == 'users.username') {
    	    $asc_desc = ' ASC';
		}
        else {
            $sort_order = 'posts.created';
            $asc_desc = ' DESC';
        }

        // Get only posts this user is following
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

        // Send it all off to the view
		$this->template->content->sort_order = $sort_order;
        $this->template->content->posts = $posts;

        // This is the landing page after a post is added or deleted,
        //   so this processes the confirmation message, if any
        //   (also sanitizes by default I think?)
        switch($confirmation) {
        	case 'post_added':
        		$this->template->message = 'post successfully added';
        		break;
        	case 'post_deleted':
        		$this->template->message = 'post was deleted';
        		break;
        	default:
        }

        // Render view
		echo $this->template;
    }

    // DISPLAYS LIST OF ALL USERS IN THE DATABASE
    public function users() {

        // Generate view
        $this->template->content = View::instance('v_posts_users');

        // Get users in alphabetical order by username
        $q = 'SELECT *
		      FROM users
		      WHERE user_id != '.$this->user->user_id.'
		      ORDER BY username ASC';

		$users = DB::instance(DB_NAME)->select_rows($q);

        // Get which ones are being followed by logged-in user
		$q = 'SELECT *
		      FROM users_users
		      WHERE user_id = '.$this->user->user_id;

		$follows = DB::instance(DB_NAME)->select_array($q, 'user_id_followed');

        // Send off data to page
		$this->template->content->users = $users;
		$this->template->content->follows = $follows;

        // Render view
        echo $this->template;
    }

    // ALLOWS USER TO FOLLOW ANOTHER USER, I.E., SEE THEIR POSTS
    public function follow($user_id_followed) {

        // Sanitize???? Just in case??
        $user_id_followed = DB::instance(DB_NAME)->sanitize($user_id_followed);

        // Get the rest of the table data and insert to add follower relationship
        $data = Array(
		    'created'          => Time::now(),
		    'user_id'          => $this->user->user_id,
		    'user_id_followed' => $user_id_followed
		);

		DB::instance(DB_NAME)->insert_row('users_users', $data);

        // Doesn't need a confirmation message since the button changes
        Router::redirect('/posts/users');

    }

    // ALLOWS USER TO STOP FOLLOWING ANOTHER USER, I.E., NO LONGER SEE THEIR POSTS
    public function unfollow($user_id_followed) {

        // Sanitize???? Just in case??
        $user_id_followed = DB::instance(DB_NAME)->sanitize($user_id_followed);

        // Find this particular follower relationship in the table and delete it
        $where_condition = 'WHERE user_id = '.$this->user->user_id.'
						    AND user_id_followed = '.$user_id_followed;

		DB::instance(DB_NAME)->delete('users_users', $where_condition);

        // Doesn't need a confirmation message since the button changes
		Router::redirect('/posts/users');
    }

    // A PAGE FOR EACH POST WHERE USERS CAN SEE COMMENTS ON THAT POST AND ADD
    //   COMMENTS OF THEIR OWN (ASSUMING THEY'RE FOLLOWING THE POST OWNER)
    public function comments($post_id = NULL,
    						 $confirmation = NULL) {

        // Can't show a post that hasn't been specified
    	if(!$post_id) {
    		Router::redirect('/posts/error/post_not_found');
    	}

        // JUST IN CASE BECAUSE THAT PIAZZA POST MADE ME SO PARANOID
        $post_id = DB::instance(DB_NAME)->sanitize($post_id);

        // Get post info for display, or show error message if invalid post_id
    	$q = 'SELECT *
    		  FROM posts
    		  WHERE post_id = '.$post_id;

    	$post = DB::instance(DB_NAME)->select_row($q);
    	
        if(!$post) {
    		Router::redirect('/posts/error/post_not_found');
    	}

        // Get comments for post, if any
    	$q = 'SELECT
    		      comments.*,
    		      users.username
    		  FROM comments
    		  INNER JOIN users
    		  		  ON comments.user_id = users.user_id
    		  WHERE post_id = '.$post_id.'
    		  ORDER BY created ASC';

    	$comments = DB::instance(DB_NAME)->select_rows($q);

        // Check to make sure the logged-in user is following the user who
        //   posted this post, otherwise display appropriate error message
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

        // Generate page
    	$this->template->content = View::instance('v_posts_comments');
    	$this->template->content->post = $post;
    	$this->template->content->comments = $comments;
    	$this->template->content->post_user = $post_user_followed;
    	$this->template->content->logged_in_user_id = $this->user->user_id;

        // This is the landing page for comment addition/deletion, so
        //   handle any confirmation messages
        switch($confirmation) {
        	case 'comment_added':
        		$this->template->message = 'comment successfully posted';
        		break;
        	case 'comment_deleted':
        		$this->template->message = 'comment was deleted';
        		break;
        	default:
        }

        // Render view
    	echo $this->template;
    }

    // ALLOWS USER TO COMMENT ON A POST
    public function p_comment($post_id = NULL) {
    	
        // Can't comment on a non-post
        if(!$post_id) {
    		Router::redirect('posts/error/comment_not_found');
    	}

        // Seriously paranoid now
        $post_id = DB::instance(DB_NAME)->sanitize($post_id);

        // If post is empty or too long, send them to error page
        //   I know this is inconsistent, but I forgot about comment content
        //   validation till the end and didn't have time to do more than this
        if((strlen($_POST['content']) > 100) || (trim($_POST['content']) == '')) {
            Router::redirect('/posts/error/bad_comment');
        }

        // If either of the above errors, stop and make them do it again
        if(isset($this->template->content->error)) {
            echo $this->template;
            return;
        }

        // If all goes well, generate table row then add to comments table
		$_POST['created'] = Time::now();
        $_POST['user_id'] = $this->user->user_id;
        $_POST['post_id'] = $post_id;

		DB::instance(DB_NAME)->insert_row('comments', $_POST);

        // Send confirmation message
        Router::redirect('/posts/comments/'.$post_id.'/comment_added');
    }

    // ALLOWS USER TO DELETE A COMMENT THEY HAVE POSTED
    public function p_delete_comment($comment_id = NULL) {

        // Can't delete a comment that isn't specified        
    	if(!$comment_id) {
    		Router::redirect('posts/error/comment_not_found');
    	}

        // Again with the paranoid sanitization
        $comment_id = DB::instance(DB_NAME)->sanitize($comment_id);

        // Get comment from table
		$q = 'SELECT * FROM comments WHERE comment_id = '.$comment_id;

		$comment = DB::instance(DB_NAME)->select_row($q);

        // Make sure the logged-in user owns this comment before allowing
        //   them to delete it
		if($this->user->user_id == $comment['user_id']) {
	    	DB::instance(DB_NAME)->delete('comments', 'WHERE comment_id = '.$comment_id);
        }

        // Send a confirmation message
		Router::redirect('/posts/comments/'.$comment['post_id'].'/comment_deleted');
    }

    // DISPLAYS A NUMBER OF DEAD-END ERROR MESSAGE PAGES
    public function error($error_type = NULL) {

        $error_type = DB::instance(DB_NAME)->sanitize($error_type);
    	$this->template->content = View::instance('v_posts_error');

    	switch($error_type) {
    		case 'post_not_found':
    			$this->template->content->error = 'Error: Post not found<br>';
    			break;
    		case 'comment_not_found':
    			$this->template->content->error = 'Error: Comment not found<br>';
    			break;
            case 'bad_comment':
                $this->template->content->error = 'Error: Invalid comment<br>';
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
