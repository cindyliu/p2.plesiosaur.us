<!DOCTYPE html>
<html>
<head>
    <title><?php if(isset($title)) echo $title; ?></title>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />	
    <link rel='stylesheet' href='/css/main.css'>
    <!-- Controller Specific JS/CSS -->
    <?php if(isset($client_files_head)) echo $client_files_head; ?>
	
</head>

<body>	

    <nav>
        <?php if($user): ?>
            <ul class='in'>
		<li><a href='/posts/add'>add post</a></li>
		<li><a href='/posts/'>view posts</a></li>
		<li><a href='/posts/users'>follow users</a></li>
		<li><a href='/users/logout'>log out</a></li>
	    </ul>
	<?php else: ?>
	    <ul class='out'>
	        <li><a href='/users/signup'>sign up</a></li>
		<li><a href='/users/login'>log in</a></li><br>
	    </ul>
	<?php endif; ?>
    </nav>

    <h1><?=APP_NAME?></h1>

    <div class='content'>
        <?php if(isset($content)) echo $content; ?>
    </div>

    <?php if(isset($client_files_body)) echo $client_files_body; ?>

</body>
</html>
