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

    <?php if($user): ?>
        <nav>
        <ul class='in'>
	        <li><a href='/'>home</a></li>
            <li><a href='/posts/add'>post</a></li>
            <li><a href='/posts'>feed</a></li>
            <li><a href='/posts/users'>users</a></li>
            <li><a href='/users/logout'>log out</a></li>
            <li><a id='profile' href='/users/profile'><?=$user->username?></a></li>
	    </ul>
        </nav>
        <a href='/'><h1 class='in'><?=APP_NAME?></h1></a>
    <?php else: ?>
        <nav>
        <ul class='out'>
	        <li><a href='/users/signup'>sign up</a></li>
            <li><a href='/users/login'>log in</a></li>
	        <li><a href='/'>home</a></li>
	    </ul>
        </nav>
        <h1><?=APP_NAME?></h1>
    <?php endif; ?>

    <?php if(isset($message)): ?>
        <div class='confirmation'>
            <?=$message?>
        </div>
    <?php endif; ?>

    <div class='content'>
        <?php if(isset($content)) echo $content; ?>
    </div>

    <?php if(isset($client_files_body)) echo $client_files_body; ?>

</body>
</html>
