<?php
ob_start();
?>
<?php if(!isset($_SESSION["user_remindme"])): ?>
  <div class="navbar navbar-inverse">
  
    <form class="navbar-form navbar-left" action="index.php" method="post">
      <div class="form-group">
      <input class="form-control" type="text" name="username" placeholder="user name:" /><br />
      </div>
      <input class="form-control" type="password" name="password" placeholder="password" /><br />
      <input class="form-control" type="password" name="password_again" placeholder="password again" /><br />
      <input class="form-control" type="email" name="email" placeholder="email" /><br />
      <input type="submit" name="register" value="Register!" /><br />
    </form>
    <form class="navbar-form" action="index.php" method="post">
      <label>user name: </label><input type="text" name="username"/><br />
      <label>password: </label><input type="password" name="password" /><br />
      <input type="submit" name="login" value="Login!" /><br />
    </form>
  </div>
<?php else: ?>
<!--   <div id="user_info"> -->
<!--     <p>Want to change your email address to send timely notifications?</p> -->
<!--     <form action="index.php" method="post"> -->
<!--       <input type="email" name="email" size="40" placeholder="Type your new email address here..." required/><br /> -->
<!--       <input type="time" name="time" size="40" placeholder="Set your new notification time here.. " required/><br /> -->
<!--       <input type="password" name="password" size="40" placeholder="Type your password here..." required/><br /> -->
<!--       <input type="submit" name="change_email" value="submit" /><br /> -->
<!--     </form> -->
<!--   </div> -->
  <div id="logout">
    <form action="index.php" method="post">
      <input type="submit" name="logout" value="Logout!" />
    </form>
  </div>
<?php endif; ?>