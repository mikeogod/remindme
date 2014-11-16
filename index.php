<?php
ob_start();
session_start();
$notification="";
require_once("user.php");
?>

<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="Author" content="Mike Xie" />
    
    <!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css">
	
	<!-- Optional theme -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css">
	
	<!-- Latest compiled and minified JavaScript -->
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
    
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" type="text/css" href="styles/general.css">
    <link rel="stylesheet" type="text/css" href="styles/jquery-ui-1.10.3.custom.min.css">
    <!--<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">-->
    
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
    <script src="scripts/jquery-ui-1.10.3.custom.min.js"></script>
    <style>
  	  /*#todos table tr td {padding-left: 3em; padding-right: 3em; padding-top: 0.5em; padding-bottom: 0.5em; }*/
  	 
  	</style>
  </head>
  <title>
    Remindme | 
	<?php 
		if (basename($_SERVER['PHP_SELF']) == "index.php") echo "Welcome";
	?>
  </title>
  <body>
    <div id="container">
	  <?php require("header.php"); ?>
	  <div id="body_container">
	  <?php if(isset($_SESSION["user_remindme"])): ?>
	  <div id='todos'>
	  <?php require("todos.php"); ?>
	  </div>
	  <?php else: ?>
	    <div class="panel panel-default">
		    <div class="panel-body">
		      <p>Welcome to RemindMe!</p>
		      <p>If you are like me, you would always forget things. Sometimes I just want to have something like a notebook that can hold some important things going on in my life, something simpler than Evernote, as well as more organizable than Sticky Notes. So I spent some free time before school's finals and made this website.</p>
		      <p>The site does two things basically. It stores your to-do items, and it orders the items according to your preference.</p>
		      <p>Sign up and try it! It's intuitive, simple, and free!</p>  
		    </div>
	    </div> 	
	    
	  <?php endif; ?>
	  </div>
	  <div id="footer">
	  <?php require_once("footer.php"); ?>
	  </div>
	  <?php if($notification!=""): ?>
	  <div id="notification"><?php echo $notification; ?></div>
	  <?php endif; ?>
	  
	  <script> 
	    $( "#notification" ).dialog({
		  modal: true,
		  width: 500,
		  buttons: [ { text: "Ok", click: function() { $( this ).dialog( "close" ); } } ],
	      show: {
	        effect: "blind",
		    duration: 1000
		  },
		  hide: {
		    effect: "explode",
		    duration: 1000
		  }
		});
		$("button, input[type='submit']").button();
	  </script>
	</div>
  </body>
</html>

