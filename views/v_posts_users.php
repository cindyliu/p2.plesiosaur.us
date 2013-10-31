<?php foreach($users as $user): ?>

    <p><h2>
        <a href='/users/profile/<?=$user['username']?>'><?=$user['username']?></a>
        </h2>

            <?php if(isset($follows[$user['user_id']])): ?>
                <a class='button' href='/posts/unfollow/<?=$user['user_id']?>'>Unfollow</a>
            <?php else: ?>
                <a class='button' href='/posts/follow/<?=$user['user_id']?>'>Follow</a>
            <?php endif; ?>
	<br>
    </p>

<?php endforeach; ?>
