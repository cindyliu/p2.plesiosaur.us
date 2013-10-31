<?php foreach($posts as $post): ?>

    <p>
        <?=$post['first_name']?> <?=$post['last_name']?> wrote:<br>
        <?=$post['content']?><br>
    </p>

<?php endforeach; ?>
