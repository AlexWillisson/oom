<?php

require ("common.php");

pstart ();

require ("header.php");

$username = @$_SESSION['username'];

echo ("logged in as " . $username);
echo ("<br />\n");

require ("footer.php");

pfinish ();
