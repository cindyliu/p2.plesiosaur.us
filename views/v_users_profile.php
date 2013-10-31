<?php if(!isset($profile['username'])): ?>
    <p class='error'>User profile not found</p>
<?php else: ?>
    <h1><?=$profile['username']?></h1>
    <div class='avatar'>
        <img src='<?=$profile['profile_photo']?>'>
    </div>
    <p><?=$profile['first_name']?> <?=$profile['last_name']?></p>
    <p class='email'><?=$profile['email']?></p>
    <?php if($display_posts): ?>
        <div class='feed'>
            <h3>your posts</h3>
            <?php foreach($user_posts as $user_post): ?>
	        <p>
	        <?=date('m/d/y | g:ia', $user_post['created'])?>:<br>
	        <?=$user_post['content']?>
	        </p>
	    <?php endforeach; ?>
        </div>
    <?php endif; ?>
<? endif; ?>
