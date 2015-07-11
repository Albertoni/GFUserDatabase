<?php
require 'dir.php';
require $inc.'loader'.$php;

// check user group

if ($_SESSION['group_id'] < 40) {
	header('Location: ./');
	die;
}

$message = '';

// delete user?

if (@$_POST['action'] == 'Delete') {
	$id = (int) $_POST['delete'];
	$username = $_POST['deletename'];
	
	$queryUsernameFilter = '';
	
	if ($username != '') {
		$username = mysql_real_escape_string($username);
		$queryUsernameFilter = " AND name = '$username' ";
	}
	
	mysql_select_db('thengamer_userdb');
	query("DELETE FROM users WHERE id = $id $queryUsernameFilter LIMIT 2");
	mysql_select_db('thengamer_forum');
	$message = "<p>User $id deleted.</p>";
}

// update user?

if (@$_POST['action'] == 'Submit') {
	$id = (int) @$_POST['id'];
	$old_id = (int) @$_POST['old_id'];
	$name = @$_POST['username'];
	$name = mysql_real_escape_string($name);
	
	mysql_select_db('thengamer_userdb');
	query("DELETE FROM users WHERE id = $old_id LIMIT 1");
	$rs = query("SELECT COUNT(*) FROM users WHERE id = $id");
	list($num) = mysql_fetch_row($rs);
	if ($num) {
		query("UPDATE users SET id = $id, name = '$name' WHERE id = $old_id LIMIT 1");
	} else {
		query("INSERT INTO users (id, name) VALUES ($id,'$name')");
	}
	mysql_select_db('thengamer_forum');
	$message = "<p>User added/edited.</p>";
}

writeHead('GFuserDB Moderation');

if (@$_POST['action'] == 'Next...') {
	$id = (int) @$_POST['user'];
	mysql_select_db('thengamer_userdb');
	$rs = query("SELECT name FROM users WHERE id = $id");
	list($username) = @mysql_fetch_row($rs);
	mysql_select_db('thengamer_forum');
	$username = htmlspecialchars($username);

	echo <<<ADDEDIT
<h3>Add/edit a user</h3>

<form action="gfuserdb.php" method="post">
  <p>User ID: <input type="text" name="id" value="$id" /></p>
  <p>Username: <input type="hidden" name="old_id" value="$id" /><input type="text" name="username" value="$username" /> <input type="submit" name="action" value="Submit" /></p>  
</form>
ADDEDIT;
	writeFoot();
}

echo <<<STUFF
$message

<h3>Add/edit a user</h3>

<form action="gfuserdb.php" method="post">
  <p>Enter user ID to edit, or leave blank to add a user: <input type="text" name="user" /> <input type="submit" name="action" value="Next..." /></p>  
</form>

<h3>Delete a user</h3>

<form action="gfuserdb.php" method="post">
  <p>Enter user ID: <input type="text" name="delete" value="" /></p>
  <p>Enter user name: <input type="text" name="deletename" value="" /> <input type="submit" name="action" value="Delete" /> (no confirmation, deletion is immediate. Leave blank for deleting at most 2 usernames with this ID.)</p>
</form>
STUFF;

writeFoot();
