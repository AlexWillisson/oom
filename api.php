<?php

require ("common.php");

pstart ();

$type = @$_REQUEST['type'];
$name = @$_REQUEST['name'];
$value = 0 + @$_REQUEST['value'];
$username = @$_REQUEST['username'];
$password = @$_REQUEST['password'];
$loggedin = 0 + @$_SESSION['loggedin'];

$stmt = sprintf ("select hash, salt from users where username='%s'",
		 $username);
$q = query ($stmt);
if (($r = fetch ($q)) == NULL) {
	$t = "login.php?failedlogin=1";
	redirect ($t);
}

$password = $password . $r->salt;
$hash = md5 ($password);

if ($hash != $r->hash) {
	echo ("invalid authentication\n");

	pfinish ();
}

if ($loggedin <= 0) {
	echo ("not logged in\n");

	pfinish ();
}

if ($type == "add") {
	if ($name == "" || $value == "") {
		echo ("missing parameter\n");
		pfinish ();
	}

	$stmt = sprintf ("select * from track where date_trunc('day',"
			 ." timestamp) = date_trunc ('day', current_timestamp)"
			 ." and name='%s';", $name);

	$q = query ($stmt);

	if (($r = fetch ($q)) == NULL) {
		$stmt = sprintf ("insert into track (name, value, owner,"
				 ." timestamp) values ('%s', '%s', '%s',"
				 ." current_timestamp)",
				 $name , $value, $username);

		query ($stmt);
	} else {
		$stmt = sprintf ("update track set value = value + %s where"
				 ." date_trunc('day', timestamp)"
				 ." = date_trunc ('day', current_timestamp)"
				 ." and name='%s';", $value, $name);

		query ($stmt);
	}

	echo ("transaction successful\n");
}

pfinish ();

