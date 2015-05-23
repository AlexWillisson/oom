<?php

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
