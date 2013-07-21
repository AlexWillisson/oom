<?php

$dbname = "tracking";

require ("DB.php");

session_start ();

function h2($s) {
  $s = htmlentities ($s);
  $len = strlen ($s);
  $ret = "";

  for ($i = 0; $i < $len; $i++) {
    $ret .= $s[$i];
    $ret .= " ";
  }
  return ($ret);
}

function ckerr ($str, $obj, $aux = "")
{
  global $dbname;

  $ret = "";
  if (DB::isError ($obj)) {
    $ret = sprintf ("<p>DBERR %s %s: %s<br />\n",
		    h($dbname),
		    h($str),
		    h($obj->getMessage ()),
		    "");

    /* these fields might have db connect passwords */
    $ret .= h($obj->userinfo);
    if ($aux != "")
      $ret .= sprintf ("<p>aux info: %s</p>\n",
		       h($aux));
    $ret .= "</p>";
    echo ($ret);
    echo ("domain_name " . htmlentities ($_SERVER['HTTP_HOST']) . "<br/>");
    echo ("request " . htmlentities ($_SERVER['REQUEST_URI']) . "<br/>");
    var_dump ($_SERVER);
    error ();
    exit ();
  }
}

function query ($stmt, $arr = NULL) {
  global $login_id, $_SERVER;
  global $db;

  if (is_string ($stmt) == 0) {
    echo ("wrong type first arg to query");
    error ();
    exit ();
  }

  $q = $db->query ($stmt, $arr);
  ckerr ($stmt, $q);

  return ($q);
}

function fetch ($q) {
	return ($q->fetchRow (DB_FETCHMODE_OBJECT));
}

$db1 = new DB;
$db = $db1->connect ("pgsql://apache@/tracking");
ckerr ("connect/local can't connect to database", $db);

query ("begin transaction");


function h($val) {
	return (htmlentities ($val, ENT_QUOTES, 'UTF-8'));
}

function do_commit () {
	query ("end transaction");
}

function redirect ($t) {
	ob_clean ();
	do_commit ();
	header ("Location: $t");
	exit ();
}

function pstart () {
	ob_start ();
	echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'"
	      ." 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n"
	      ."<html xmlns='http://www.w3.org/1999/xhtml'>\n"
	      ."<head>\n"
	      ."<meta http-equiv='Content-Type' content='text/html;"
	      ." charset=utf-8' />\n");
	echo ("<title>tracking</title>\n");
//	echo ("<link rel='stylesheet' type='text/css' href='style.css' />\n");

	echo ("</head>\n");
	echo ("<body>\n");
}


function pfinish () {
	echo ("</body>\n");
	echo ("</html>\n");
	do_commit ();
	exit ();
}

function mklink ($text, $target) {
	if (trim ($text) == "")
		return ("&nbsp;");
	if (trim ($target) == "")
		return (h($text));
	return (sprintf ("<a href='%s'>%s</a>",
			 h($target), h($text)));
}

function odd_even ($x) {
	if ($x & 1)
		return ("class='odd'");
	return ("class='even'");
}

$urandom_chars = "0123456789abcdefghijklmnopqrstuvwxyz";
$urandom_chars_len = strlen ($urandom_chars);

function generate_urandom_string ($len) {
	global $urandom_chars, $urandom_chars_len;
	$ret = "";

	$f = fopen ("/dev/urandom", "r");

	for ($i = 0; $i < $len; $i++) {
		$c = ord (fread ($f, 1)) % $urandom_chars_len;
		$ret .= $urandom_chars[$c];
	}
	return ($ret);
}

function generate_urandom_digits ($len) {
	global $urandom_chars, $urandom_chars_len;
	$ret = "";

	$f = fopen ("/dev/urandom", "r");

	for ($i = 0; $i < $len; $i++) {
		$c = ord (fread ($f, 1)) % 10;
		$ret .= $urandom_chars[$c];
	}
	return ($ret);
}

?>
