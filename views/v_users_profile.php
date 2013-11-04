<?php if(!isset($profile['username'])): ?>

    <p class='error'>User profile not found</p>

<?php else: ?>

    <h2><?=$profile['username']?></h2>

    <div class='avatar'>
        <img src='<?=$profile['profile_photo']?>'>
    </div>

    <p><?=$profile['first_name']?> <?=$profile['last_name']?></p>
    <p class='email'><?=$profile['email']?></p>

    <?php if($user_posts): ?>

        <div class='feed'>

            <h3>my posts</h3>

            <?php foreach($user_posts as $user_post): ?>
                <p>
    		        <span class='timestamp'>
	                   <?=date('m/d/y | g:ia', $user_post['created'])?>:<br>
		            </span>

	                <?=$user_post['content']?><br>

                    <?php if($logged_in_user_id == $profile['user_id']): ?>
    		            <a href='/posts/delete/<?=$user_post['post_id']?>'>delete this post</a>
                    <?php endif; ?>
	            </p>
	        <?php endforeach; ?>

        </div>

    <?php endif; ?>

<?php endif; ?>
