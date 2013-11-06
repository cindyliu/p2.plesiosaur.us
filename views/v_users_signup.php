<h2>Sign Up</h2>

<form method='POST' action='/users/signup'>

<div class='err_msg'>
    <?php if(isset($errors)): ?>
        <?php foreach($errors as $error) echo $error.'<br>'; ?>
    <?php endif; ?>
</div>

<table>
<tr>
    <td class='prompt'>First Name: </td>
    <td><input type='text' name='first_name' value='<?php if(isset($_POST['first_name'])) echo $_POST['first_name']?>' size='30' required></td>
</tr>
<tr>
    <td class='prompt'>Last Name: </td>
    <td><input type='text' name='last_name' value='<?php if(isset($_POST['last_name'])) echo $_POST['last_name']?>' size='30' required></td>
</tr>
<tr>
    <td class='prompt'>Email: </td>
    <td><input type='text' name='email' value='<?php if(isset($_POST['email'])) echo $_POST['email']?>' size='30' required></td>
</tr>
<tr>
    <td class='prompt'>Username: </td>
    <td><input type='text' name='username' value='<?php if(isset($_POST['username'])) echo $_POST['username']?>' size='30' required></td>
</tr>
<tr>
    <td class='prompt'>Password: </td>
    <td><input type='password' name='password' size='30' required></td>
</tr>
<tr>
    <td class='prompt'>Re-enter password: </td>
    <td><input type='password' name='password_check' size='30' required></td>
</tr>
</table>

<input type='submit' value='Sign up!'>

</form>