<?php if($user): ?>

    Hello, <?=$user->first_name;?>.
    <br><br>
    <h3>Updates</h3>
    <ul>
        <li>Users can now view profiles from the <a href='/posts/users'>users</a> page.</li>
    </ul>

<?php else: ?>

    Welcome to the <em><?=APP_NAME?></em>.<br>
    Please <a href='/users/signup'>sign up</a> or <a href='/users/login'>log in</a>.

<?php endif; ?>


