<?php if($user): ?>

    Hello, <?=$user->first_name;?>.
    <br><br>
    <h3>Updates</h3>
    <ul>
        <li>Logging out and hitting the browser's 'Back' button no longer produces error pages.</li>
        <li>Users can now view all posts by another user on the other user's profile page.</li>
        <li>Users are now logged in automatically upon signup rather than redirected to the login page.</li>
        <li>Users can now delete their posts from their profile view.</li>
        <li>Users can now view profiles from the <a href='/posts/users'>users</a> page.</li>
	<li>Users now automatically follow themselves upon signup.</li>
	<li>Users no longer see option to follow themselves on users page.</li>
	<li>Timestamps added to all posts.</li>
	<li>All posts now sorted with newest displayed at top.</li>
    </ul>

<?php else: ?>

    Welcome to the <em><?=APP_NAME?></em>.<br>
    Please <a href='/users/signup'>sign up</a> or <a href='/users/login'>log in</a>.

<?php endif; ?>


