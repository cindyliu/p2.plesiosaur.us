<?php foreach($posts as $post): ?>

    <p>
        <em><?=$post['username']?>:<br></em>
        <?=$post['content']?><br>
	<span class='timestamp'>
	    <?=date('m/d/y g:ia',$post['created'])?>
	</span>
    </p>

<?php endforeach; ?>
