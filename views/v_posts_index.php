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

    <p>
        <strong class='username'><?=$post['username']?>:<br></strong>
        <?=$post['content']?><br>
	<span class='timestamp'>
	    <?=date('m/d/y g:ia',$post['created'])?>
	</span>
    </p>

<?php endforeach; ?>
