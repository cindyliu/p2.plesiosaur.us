<?php if(!isset($post)): ?>

    <p class='error'>Post not found</p>

<?php else: ?>

    <h2><?=$post_user['username']?>'s post</h2>

    <div class='post'>
        <span class='timestamp'>
            <?=date('m/d/y | g:ia', $post['created'])?>:<br>
        </span>

        <?=$post['content']?><br>

        <?php if($logged_in_user_id == $post_user['user_id']): ?>
            <a class='delete' href='/posts/p_delete/<?=$post['post_id']?>'>delete this post</a><br>
            <div class='warning'>Warning: deleting this post will permanently delete all associated comments.</div>
        <?php endif; ?>
    </div>

    <h3>comments</h3>

    <?php if($comments): ?>

        <div class='feed'>

            <?php foreach($comments as $comment): ?>
                <p>
                    <span class='username'>
                        <?=$comment['username']?>
                    </span>
    		        <span class='timestamp'>
	                   @ <?=date('m/d/y | g:ia', $comment['created'])?>:<br>
		            </span>

	                <?=$comment['content']?><br>

                    <?php if($logged_in_user_id == $comment['user_id']): ?>
                        <a class='delete' href='/posts/p_delete_comment/<?=$comment['comment_id']?>'>delete comment</a><br>
                    <?php endif; ?>
	            </p>
	        <?php endforeach; ?>

        </div>

    <?php else: ?>
        There are no comments to display.<br>

    <?php endif; ?><br>

    <p>
        <h3>add a comment</h3>

        <form method='POST' action='/posts/p_comment/<?=$post['post_id']?>'>
            <div>
                <textarea rows='4' cols='30' maxlength='100'  name='content'></textarea>
            </div>
            <input type='submit' value='post comment'>
        </form>
        <div class='warning'>Note: Your comment will be visible to anyone who can see this post.</div>
    </p>
<?php endif; ?>
