<?php

$dbname = "oom";

require ("DB.php");

session_start ();

function h($val) {
	return (htmlentities ($val, ENT_QUOTES, 'UTF-8'));
}
function redirect ($t) {
	ob_clean ();
	do_commit ();
	header ("Location: $t");
	exit ();
}

$pstart_args = (object) null;
$pstart_args->css = array ();
$pstart_args->js = array ();

function pstart () {
	global $pstart_args;

	ob_start ();
	echo ("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'"
	      ." 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>\n"
	      ."<html xmlns='http://www.w3.org/1999/xhtml'>\n"
	      ."<head>\n"
	      ."<meta http-equiv='Content-Type' content='text/html;"
	      ." charset=utf-8' />\n");
	echo ("<title>oom</title>\n");
	echo ("<link rel='stylesheet' type='text/css' href='colors.css' />\n");
	echo ("<link rel='stylesheet' type='text/css' href='style.css' />\n");
	for ($idx = 0; $idx < count ($pstart_args->css); $idx++) {
		$s = sprintf ("<link rel='stylesheet'"
                      ." type='text/css' href='%s' />\n",
                      $pstart_args->css[$idx]);
		echo ($s);
	}
	for ($idx = 0; $idx < count ($pstart_args->js); $idx++) {
		$s = sprintf ("<script src='%s'></script>\n",
			      $pstart_args->js[$idx]);
		echo ($s);
	}
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

function query_db ($db, $stmt, $arr = NULL) {
	if ($arr == NULL) {
		$q = $db->query ($stmt);
	} else {
		if (! is_array ($arr)) {
			$arr = array ($arr);
		}

		foreach ($arr as $key => $val) {
			if (is_string ($val) && $val == "") {
				$arr[$key] = NULL;
			}
		}

		$q = $db->prepare ($stmt);
		$q->execute ($arr);
	}

	return ($q);
}

function query ($stmt, $arr = NULL) {
	global $default_db;

	return (query_db ($default_db, $stmt, $arr));
}

function fetch ($q) {
	return ($q->fetch (DB_FETCHMODE_OBJECT));
}

function fetchall ($q) {
	return ($q->fetchAll (DB_FETCHMODE_OBJECT));
}

$db = new PDO ("pgsql:dbname=oom; user=apache");
$default_db = $db;

function do_commit () {
	query ("end transaction");
}

function parse_results ($cols, $results) {
	$res = array ();

    if ( ! $results) {
        return (null);
    }

	for ($idx = 0; $idx < count ($results); $idx++) {
		$res[$cols[$idx]] =  $results[$idx];
	}

	return ($res);
}
