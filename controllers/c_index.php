<?php

class index_controller extends base_controller {
	
	/*-------------------------------------------------------------------------------------------------

	-------------------------------------------------------------------------------------------------*/
	public function __construct() {
		parent::__construct();
	} 
		
	/*-------------------------------------------------------------------------------------------------
	Accessed via http://localhost/index/index/
	-------------------------------------------------------------------------------------------------*/
	public function index($message = NULL) {
		
		# Any method that loads a view will commonly start with this
		# First, set the content of the template with a view file
			$this->template->content = View::instance('v_index_index');

			switch($message) {
				case 'signed_up':
					if($this->user) {
						$this->template->message = 'you have signed up';
					}
					break;
				case 'logged_in':
					if($this->user) {
						$this->template->message = 'you have logged in';
					}
					break;
				case 'logged_out':
					if(!$this->user) {
						$this->template->message = 'you have logged out';
					}
					break;
				default:
			}
			
		# Now set the <title> tag
			$this->template->title = APP_NAME;
	
		# CSS/JS includes
			/*
			$client_files_head = Array("");
	    	$this->template->client_files_head = Utils::load_client_files($client_files);
	    	
	    	$client_files_body = Array("");
	    	$this->template->client_files_body = Utils::load_client_files($client_files_body);   
	    	*/
	      					     		
		# Render the view
			echo $this->template;

	} # End of method
	
	
} # End of class
