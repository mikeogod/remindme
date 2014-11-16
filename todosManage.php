<?php
ob_start();
session_start();
require("dbconnect.php");

$ERROR="";

if(!isset($_SESSION["user_remindme"]))
{
	header("Location: index.php");
}
else 
{
	if(!empty($_POST) && $_POST["action"]=="reorder")
	{
		if(!update_priority($_POST["current"], $_POST["dest"], $_SESSION["user_remindme"]["id"], $db, $ERROR))
		{
			echo $ERROR;
		}
	}
	else if(!empty($_POST) && $_POST["action"]=="add")
	{
		if(!add_todo($_POST["title"], $_POST["description"], $_SESSION["user_remindme"]["id"], $db, $ERROR))
		{
			echo $ERROR;
		}
		header("Location: index.php");
	}
	else if(!empty($_POST) && $_POST["action"]=="change")
	{
		if(!update_todo($_POST["priority"], $_POST["title"], $_POST["description"], $_SESSION["user_remindme"]["id"], $db, $ERROR))
		{
			echo $ERROR;
		}
	}
	else if(!empty($_POST) && $_POST["action"]=="delete")
	{
		if(!delete_todo($_POST["priority"], $_SESSION["user_remindme"]["id"], $db, $ERROR))
		{
			echo $ERROR;
		}
	}
}


function update_priority($current, $dest, $uid, $db, &$ERROR)
{
	try{
		$db->beginTransaction();
		
		$query="SELECT * FROM `todos` WHERE `userId`='{$uid}' AND `status`='Active' ORDER BY `priority`";
		$stmt=$db->query($query);
		$all_todos=$stmt->fetchAll();

		$query="UPDATE `todos` SET `priority`={$dest} WHERE `userId`={$uid} AND `priority`={$current} AND `status`='Active'";
		$stmt=$db->query($query);
		
		//Moving up
		if($current>$dest)
		{
			$to_be_changed=array_slice($all_todos, $dest, $current-$dest);
			$ERROR=count($to_be_changed);
			foreach($to_be_changed as $todo)
			{
				$p=$todo["priority"];
				$p++;
				$query="UPDATE `todos` SET `priority`={$p} WHERE `id`={$todo['id']}";
				$db->exec($query);
			}
		}
		//Moving down
		else if($current<$dest)
		{
			$to_be_changed=array_slice($all_todos, $current+1, $dest-$current);
	
			foreach($to_be_changed as $todo)
			{
				$p=$todo["priority"];
				$p--;
				$query="UPDATE `todos` SET `priority`={$p} WHERE `id`={$todo['id']}";
				$db->exec($query);
			}
		}
		 
		$db->commit();

		return true;

	}catch(PDOException $ex)
	{
		$ERROR=$ex->getMessage();
		$db->rollBack();
		return false;
	}
}

function update_todo($priority, $title, $desc, $uid, $db, &$ERROR)
{
	try{
		$query="UPDATE `todos` SET `title`='{$title}', `description`='{$desc}' WHERE `userId`={$uid} AND `priority`={$priority} AND `status`='Active'";
		$db->exec($query);
		return true;
	}catch(PDOException $ex)
	{
		$ERROR=$ex->getMessage();
		return false;
	}
}

function delete_todo($priority, $uid, $db, &$ERROR)
{
	$db->beginTransaction();
	try{
		$query="UPDATE `todos` SET `priority`=-1, `status`='Not Active' WHERE `priority`={$priority} AND `userId`={$uid}";
		$stmt=$db->exec($query);
		
		$query="SELECT * FROM `todos` WHERE `userId`={$uid} AND `status`='Active' ORDER BY `priority`";
		$stmt=$db->query($query);
		$all_todos=$stmt->fetchAll();

		for($i=0; $i!=count($all_todos); $i++)
		{
			if($all_todos[$i]["priority"]>$priority)
			{
				$p=$all_todos[$i]["priority"]-1;
				$query="UPDATE `todos` SET `priority`={$p} WHERE `id`={$all_todos[$i]['id']}";
				$stmt=$db->exec($query);
			}
		}

		$db->commit();
		return true;
	}
	catch(PDOException $ex)
	{
		$db->rollBack();
		$ERROR=$ex->getMessage();
		return false;
	}
}

function add_todo($title, $desc, $uid, $db, &$ERROR)
{
	try{
		$query="SELECT * FROM `todos` WHERE `userId`='{$uid}' AND `status`='Active'";
		$stmt=$db->query($query);
		$largest_p=$stmt->rowCount();

		$query="INSERT INTO `todos`(`title`, `description`, `userId`, `priority`)VALUES( '{$title}', '{$desc}', {$uid}, {$largest_p})";
		$db->exec($query);
		return true;
	}catch(PDOException $ex)
	{
		$ERROR=$ex->getMessage();
		return false;
	}
}