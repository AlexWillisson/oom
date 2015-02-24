<?php

require ("common.php");

pstart ();

$login = 0 + @$_REQUEST['login'];
$username = @$_REQUEST['username'];
$password = @$_REQUEST['password'];
$createuser = @$_REQUEST['createuser'];
$failedlogin = 0 + @$_REQUEST['failedlogin'];
$loggedin = @$_SESSION['loggedin'];

if ($login == 1) {
	$stmt = sprintf ("select hash, salt from users where username='%s'",
			 $username);
	$q = query ($stmt);
	if (($r = fetch ($q)) == NULL) {
		$t = "login.php?failedlogin=1";
		redirect ($t);
	}

	$password = $password . $r->salt;
	$hash = md5 ($password);

	if ($hash == $r->hash) {
		$_SESSION['loggedin'] = 1;
		$_SESSION['username'] = $username;
		$t = "index.php";
		redirect ($t);
	} else {
		$t = "index.php?failedlogin=2";
		redirect ($t);
	}
} else if ($login == 2) {
	$_SESSION['loggedin'] = 0;
	$_SESSION['username'] = "";
	$t = "index.php";
	redirect ($t);
}

if ($createuser == 1) {
	echo ("<form action='login.php' method='post'>\n");
	echo ("<input name='username' size='40' />\n");
	echo ("<br />");
	echo ("<input type='password' name='password' size='40' />\n");
	echo ("<br />");
	echo ("<input type='hidden' name='createuser' value='2' />\n");
	echo ("<input type='submit' value='Create' />\n");
	echo ("</form>\n");	
} else if ($createuser == 2) {
	$stmt = sprintf ("select username from users where username='%s'",
			 $username);
	$q = query ($stmt);
	if (($r = fetch ($q)) != NULL) {
		$t = "index.php?createduser=2";
		redirect ($t);
	}

	$q = query ("select nextval('seq') as seq");
	$r = fetch ($q);
	$id = 0 + $r->seq;
	$salt = generate_urandom_string (10);
	$hash = $password . $salt;
	$hash = md5 ($hash);
	$stmt = sprintf ("insert into users (username, hash, salt, id)"
			 ." values ('%s', '%s', '%s', '%d')",
			 $username, $hash, $salt, $id);
	query ($stmt);

	$t = "index.php?createduser=1";
	redirect ($t);
}

pfinish ();

?>
