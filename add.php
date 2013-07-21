<?php

require ("common.php");

pstart ();

$addrow = 0 + @$_REQUEST['addrow'];
$name = @$_REQUEST['name'];
$value = 0 + @$_REQUEST['value'];
$loggedin = 0 + @$_SESSION['loggedin'];

if ($loggedin == 0) {
	echo ("you are not logged in\n");
	echo ("<form action='login.php'>\n");
	echo ("<input name='username' size='40' />\n");
	echo ("<br />");
	echo ("<input type='password' name='password' size='40' />\n");
	echo ("<br />");
	echo ("<input type='hidden' name='login' value='1' />\n");
	echo ("<input type='submit' value='Login' name='button_login' />\n");
	echo ("</form>\n");

	pfinish ();
}

if ($addrow == 0) {
	echo ("<form action='add.php'>\n");
	echo ("<input name='name' size='40' />\n");
	echo ("<br />");
	echo ("<input name='value' size='40' />\n");
	echo ("<br />");
	echo ("<input type='hidden' name='addrow' value='1' />\n");
	echo ("<input type='submit' value='Add' />\n");
	echo ("</form>\n");	
} else if ($addrow == 1) {
	$stmt = sprintf ("insert into track (name, value, timestamp)"
			 ." values ('%s', '%s', current_timestamp)", $name , $value);

	query ($stmt);

	$t = "index.php";
	redirect ($t);
}

pfinish ();

?>
