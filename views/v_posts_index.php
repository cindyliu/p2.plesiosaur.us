<h2>my followed posts</h2>

sort by:
<?php if($sort_order != 'posts.created'): ?>
    <a href='/posts/index'>time</a>
<?php else: ?>
    time
<?php endif; ?>
<?php if($sort_order != 'users.username'): ?>
    <a href='/posts/index/users.username'>username</a>
<?php else: ?>
    username
<?php endif; ?>

<?php foreach($posts as $post): ?>

    <p class='post'>
        <a class='username' href='/users/profile/<?=$post['username']?>'><?=$post['username']?></a><br>
        <?=$post['content']?><br>
	<span class='timestamp'>
	    <a href='/posts/comments/<?=$post['post_id']?>'>
            <?=date('m/d/y g:ia',$post['created'])?>
        </a>
	</span>
    </p>

<?php endforeach; ?>
