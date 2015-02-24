<?php

require ("common.php");

pstart ();

$add = 0 + @$_REQUEST['add'];
$name = @$_REQUEST['name'];
$value = 0 + @$_REQUEST['value'];
$loggedin = 0 + @$_SESSION['loggedin'];
$username = @$_SESSION['username'];

if ($loggedin == 0) {
	echo ("you are not logged in\n");
	echo ("<form action='login.php' method='post'>\n");
	echo ("<input name='username' size='40' />\n");
	echo ("<br />");
	echo ("<input type='password' name='password' size='40' />\n");
	echo ("<br />");
	echo ("<input type='hidden' name='login' value='1' />\n");
	echo ("<input type='submit' value='Login' name='button_login' />\n");
	echo ("</form>\n");

	pfinish ();
}

if ($add == 0) {
	echo ("<form action='add.php'>\n");
	echo ("<input name='name' size='40' />\n");
	echo ("<br />");
	echo ("<input name='value' size='40' />\n");
	echo ("<br />");
	echo ("<input type='hidden' name='add' value='1' />\n");
	echo ("<input type='submit' value='Add' />\n");
	echo ("</form>\n");	
} else if ($add == 1) {
	$stmt = sprintf ("select * from track where date_trunc('day',"
			 ." timestamp) = date_trunc ('day', current_timestamp)"
			 ." and name='%s';", $name);

	$q = query ($stmt);

	if (($r = fetch ($q)) == NULL) {
		$stmt = sprintf ("select * from trackers where name='%s'", $name);
		$q = query ($stmt);
		if (($r = fetch ($q)) == NULL) {
			$stmt = sprintf ("insert into track (name, value, owner,"
							 ." timestamp) values ('%s', '%s', '%s',"
							 ." current_timestamp)",
							 $name , $value, $username);
		} else {
			$stmt = sprintf ("insert into track (name, value, owner,"
							 ." timestamp) values ('%s', '%s', '%s',"
							 ." date_trunc ('%s', current_timestamp))",
							 $name , $value, $username, $r->resolution);
		}
		
		query ($stmt);
	} else {
		$stmt = sprintf ("update track set value = value + %s where"
				 ." date_trunc('day', timestamp)"
				 ." = date_trunc ('day', current_timestamp)"
				 ." and name='%s';", $value, $name);

		query ($stmt);
	}

	$t = "index.php";
	redirect ($t);
}

pfinish ();

?>
