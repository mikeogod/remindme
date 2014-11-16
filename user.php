<?php
assert(isset($notification));
require('dbconnect.php');
if(!empty($_POST))
{
	if(isset($_POST["register"]))
	{
		if(empty($_POST["username"]))
		{
			$notification="Please enter a username";
			return;
		}
		if(empty($_POST["password"]))
		{
			$notification="Please enter a password";
			return;
		}
		if(empty($_POST["password_again"]))
		{
			$notification="Please enter the password again";
			return;
		}
		if(strlen($_POST["password"])<4)
		{
			$notification="The password needs to be at least 4 characters long";
			return;
		}
		if($_POST["password"]!=$_POST["password_again"])
		{
			$notification="The two passwords don't match";
			return;
		}
		if(empty($_POST["email"]))
		{
			$notification="Please enter an email address";
			return;
		}
		
		$query="SELECT * FROM `users` WHERE `username`='{$_POST['username']}'";
		try{
			$stmt=$db->query($query);
		}catch(PDOException $ex)
		{
			$notification="Database error:".$ex->getMessage();
			return;
		}
		if($stmt->fetch())
		{
			$notification="This username has been used";
			return;
		}
		
		$salt=dechex(mt_rand(0, 10000));
		$password=hash("sha256", $_POST["password"].$salt);
		$query="INSERT INTO `users`(`username`, `password`, `salt`, `email`) VALUES (:username, :password, :salt, :email)";
		$query_param=array(":username"=>$_POST["username"], ":password"=>$password, ":salt"=>$salt, ":email"=>$_POST["email"]);
		try{
			$stmt=$db->prepare($query);
			$stmt->execute($query_param);
		}catch(PDOException $ex)
		{
			$notification="Database error: ".$ex->getMessage();
			return;
		}
		
		$query="SELECT * FROM `users` WHERE `username`='{$_POST['username']}'";
		try{
			$stmt=$db->query($query);
		}catch(PDOException $ex)
		{
			$notification="Database error: ".$ex->getMessage();
			return;
		}
		$user=$stmt->fetch();
		$_SESSION["user_remindme"]=$user;
		$notification="You are registered! Enjoy!";
	}
	else if(isset($_POST["login"]))
	{
		if(empty($_POST["username"]))
		{
			$notification="Please enter a username";
			return;
		}
		if(empty($_POST["password"]))
		{
			$notification="Please enter a password";
			return;
		}
		
		$query="SELECT * FROM `users` WHERE `username`='{$_POST['username']}'";
		try{
			$stmt=$db->query($query);
		}catch(PDOException $ex)
		{
			$notification="Database error: ".$ex->getMessage();
			return;
		}
		$result=$stmt->fetch();
		if(empty($result))
		{
			$notification="Wrong username";
			return;
		}
		$salt=$result["salt"];
		$password=hash("sha256", $_POST["password"].$salt);
		if($password!==$result["password"])
		{
			$notification="Wrong password ".$password." ".$result["password"];
			
			return;
		}
		
		$user=$result;
		$_SESSION["user_remindme"]=$user;
		$notification="You have logged in! Enjoy!";	
	}
	else if(isset($_POST["logout"]))
	{
		unset($_SESSION["user_remindme"]);
		setcookie(session_name(), '', time() - 72000);
		session_destroy();
	}
	else if(isset($_POST["change_email"]))
	{
		$notification="Have not yet implemented";
		return;
	}
}