<!-- HOME PAGE -->

<?php if($user): ?>

    Hello, <?=$user->first_name;?>.
    <br><br>
    <h3>Updates</h3>
    <ul>
        <li>Error messages and confirmation messages added/updated.</li>
        <li>NEW FEATURE: You can now add comments to posts!</li>
        <li>Signup now prohibits blank fields or usernames/emails that are already associated with an account.</li>
        <li>Signup now requires re-typing your password for confirmation.</li>
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
    Please <a href='/users/signup'>sign up</a> or <a href='/users/login'>log in</a>.<br>

    <p>
        <?=APP_NAME?> is a microblogging platform for all your microblogging needs.<br><br>
        or at least, the ones where you need to post and see other people's posts whom you've followed.<br><br>
        fun features:<br>
        you can delete posts you've written, and you can add comments to other people's posts (or your own) as well as delete them, if you've made bad choices.<br><br>
        enjoy!
    <p>
<?php endif; ?>


