<?php foreach($users as $user): ?>

    <p><?=$user['username']?><br>

    <?php if(isset($follows[$user['user_id']])): ?>
        <a href='/posts/unfollow/<?=$user['user_id']?>'>Unfollow</a>
    <?php else: ?>
        <a href='/posts/follow/<?=$user['user_id']?>'>Follow</a>
    <?php endif; ?>
    </p>

<?php endforeach; ?>
