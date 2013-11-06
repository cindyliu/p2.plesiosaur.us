<div class='err_msg'>
    <?php if(isset($error)) echo $error; ?>
</div>

<form method='POST' action='/posts/add'>

    <h2>Talk to the <?=APP_NAME?>.</h2>
    <div>
    <textarea rows='4' cols='30' maxlength='255'  name='content' required></textarea>
    </div>
    <input type='submit' value='add new post'>

</form>
