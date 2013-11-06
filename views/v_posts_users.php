<h2>all users</h2>

<div id='userlist'>
<table cellpadding=10>

<?php foreach($users as $user): ?>

    <tr>
        <td>
            <h3>
                <a class='username' href='/users/profile/<?=$user['username']?>'><?=$user['username']?></a>
            </h3>
        </td>
        <td>
            <?php if(isset($follows[$user['user_id']])): ?>
                <a class='button' href='/posts/unfollow/<?=$user['user_id']?>'>Unfollow</a>
            <?php else: ?>
                <a class='button' href='/posts/follow/<?=$user['user_id']?>'>Follow</a>
            <?php endif; ?>
        </td>
    </tr>

<?php endforeach; ?>

</table>
</div>