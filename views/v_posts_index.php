<?php foreach($posts as $post): ?>

    <p>
        <?=$post['username']?> wrote:<br>
        <?=$post['content']?><br>
    </p>

<?php endforeach; ?>
