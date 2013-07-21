<?php

require ("common.php");

pstart ();

$createuser = 0 + @$_REQUEST['createduser'];
$failedlogin = 0 + @$_REQUEST['failedlogin'];
$loggedin = 0 + @$_SESSION['loggedin'];
$username = @$_SESSION['username'];

if ($failedlogin == 1) {
	echo ("can't find user data\n"
		  ."<br />");
} else if ($failedlogin == 2) {
	echo ("failed login\n"
		  ."<br />");
}

if ($createduser == 1) {
	echo ("successfully created user \n"
		  ."<br />\n");
}

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

echo ("logged in as " . $username);
echo ("<br />\n");
echo ("<a href='add.php'>[Add]</a>\n");
echo ("<br />\n");
echo ("<a href='plot.php'>[Plot]</a>\n");
echo ("<br />\n");
echo ("<a href='login.php?createuser=1'>[Create user]</a>\n");
echo ("<form action='login.php'>\n");
echo ("<input type='hidden' name='login' value='2' />\n");
echo ("<input type='submit' value='logout' />\n");
echo ("</form>\n");

pfinish ();

?>
