<?php

require ("common.php");


ob_start ();
echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'"
	  ." 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n"
	  ."<html xmlns='http://www.w3.org/1999/xhtml'>\n");

$addrow = 0 + @$_REQUEST['addrow'];
$name = @$_REQUEST['name'];
$value = 0 + @$_REQUEST['value'];
$tracked = @$_REQUEST['tracked'];
$loggedin = 0 + @$_SESSION['loggedin'];
$username = @$_SESSION['username'];

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

if ($tracked == "") {
	$tracked = "piano";
}

echo ("<head>\n"
	  ."<meta http-equiv='Content-Type' content='text/html;"
	  ." charset=utf-8' />\n");
echo ("<title>tracking</title>\n");

echo ("	<link href='plot.css' rel='stylesheet' type='text/css'>\n");
echo ("	<script language='javascript' type='text/javascript' src='jquery.js'></script>\n");
echo ("	<script language='javascript' type='text/javascript' src='jquery.flot.js'></script>\n");
echo ("	<script language='javascript' type='text/javascript' src='jquery.flot.time.js'></script>\n");
echo ("	<script type='text/javascript'>\n");
echo ("\n");
echo ("	$(function() {\n");
echo ("\n");
echo ("			var d1 = [];\n");
$stmt = sprintf ("select value, extract('epoch' from timestamp) as timestamp from track where name='".$tracked."' and owner='".$username."' order by timestamp");
$q = query ($stmt);

while (($r = fetch ($q)) != NULL) {
	echo ("d1.push(['".$r->timestamp."', ".$r->value."]);\n");
}

echo ("\n");
echo ("$.plot('#placeholder', [d1], { xaxis: { mode: 'time' } });\n");
echo ("		});\n");
echo ("\n");
echo ("</script>\n");
echo ("</head>\n");
echo ("<body>\n");

echo ("	<div id='content'>\n");
echo ("	<div class='demo-container'>\n");
echo ("	<div id='placeholder' class='demo-placeholder'></div>\n");
echo ("	</div>\n");
echo ("	</div>\n");

pfinish ();

?>
