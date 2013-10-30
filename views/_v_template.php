<!DOCTYPE html>
<html>
<head>
	<title><?php if(isset($title)) echo $title; ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
					
	<!-- Controller Specific JS/CSS -->
	<?php if(isset($client_files_head)) echo $client_files_head; ?>
	
</head>

<body>	

        <nav>
	        <menu>
		        <?php if($user): ?>
			        <li><a href='/posts/add'>Add Post</a></li>
				<li><a href='/posts/'>View Posts</a></li>
				<li><a href='/posts/users'>Follow users</a></li>
				<li><a href='/users/logout'>Logout</a></li>
				<br>You are logged in.<br>
			<?php else: ?>
			        <li><a href='/users/signup'>Sign up</a></li>
				<li><a href='/users/login'>Log in</a></li>
			<?php endif; ?>
		</menu>
	</nav>


	<?php if(isset($content)) echo $content; ?>

	<?php if(isset($client_files_body)) echo $client_files_body; ?>
</body>
</html>
