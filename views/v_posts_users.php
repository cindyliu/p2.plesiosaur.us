<h2>all users</h2>

<div id='user_list'>

<?php foreach($users as $user): ?>

    <p><h3>
        <a class='username' href='/users/profile/<?=$user['username']?>'><?=$user['username']?></a>
        </h3>

            <?php if(isset($follows[$user['user_id']])): ?>
                <a class='button' href='/posts/unfollow/<?=$user['user_id']?>'>Unfollow</a>
            <?php else: ?>
                <a class='button' href='/posts/follow/<?=$user['user_id']?>'>Follow</a>
            <?php endif; ?>
	<br>
    </p>

<?php endforeach; ?>

</div>
