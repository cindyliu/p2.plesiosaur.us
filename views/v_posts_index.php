<?php foreach($posts as $post): ?>

    <p>
        <strong class='username'><?=$post['username']?>:<br></strong>
        <?=$post['content']?><br>
	<span class='timestamp'>
	    <?=date('m/d/y g:ia',$post['created'])?>
	</span>
    </p>

<?php endforeach; ?>
