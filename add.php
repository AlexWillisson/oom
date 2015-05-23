<?php

function fetch_sources () {
	$cols = array ("source_id", "name");
	$stmt = sprintf ("select %s from sources", implode (", ", $cols));
	$q = query ($stmt);

	$sources = array ();

	while (($r = fetch ($q)) != NULL) {
		$res = parse_results ($cols, $r);

		$sources[$res['source_id']] = $res['name'];
	}

	return ($sources);
}

require ("common.php");

pstart ();

$add = 0 + @$_REQUEST['add'];
$raw_songs = @$_REQUEST['songs'];
$loggedin = 0 + @$_SESSION['loggedin'];
$username = @$_SESSION['username'];

require ("header.php");

$source_map = fetch_sources ();
$sources = array_map (function ($s) { return ($s); }, $source_map);

$body = "";

if ($add == 0) {
	$body .= "<form action='add.php' method='post'>\n";
	$body .= "<br />\n";

	foreach ($sources as $s) {
		$body .= "<div class='source-area'>\n";
		$body .= "<div class='source-name'>\n";
		$body .= sprintf ("%s", $s);
		$body .= "</div>\n";
		$body .= "<div class='source-checkbox'>\n";
		$body .= sprintf ("<input type='checkbox' class='source'"
				  ." name='%s' />\n", h($s));
		$body .= "</div>\n";
		$body .= "</div>\n";
	}

	$body .= "<br />\n";
	$body .= "<div class='songs-area'>\n";
	$body .= "<textarea name='songs' rows='40' cols='80'>\n";
	$body .= "</textarea>\n";
	$body .= "<br />";
	$body .= "<input type='hidden' name='add' value='1' />\n";
	$body .= "<input type='submit' value='Add' />\n";
	$body .= "</form>\n";
	$body .= "</div>\n";
} else if ($add == 1) {
	$lines = explode ("\n", trim ($raw_songs));

	$sources_checked = array ();

	foreach (array_keys ($source_map) as $source) {
		if (strcmp ('' . @$_REQUEST[$source_map[$source]], 'on') == 0) {
			$sources_checked[] = $source;
		}
	}

	$js_sources = json_encode ($sources_checked);

	$songs = array ();
	for ($idx = 0; $idx < count ($lines); $idx++) {
		if (strlen ($lines[$idx]) <= 0) {
			continue;
		}

		$tokens = explode (",", $lines[$idx]);

		$song = trim ($tokens[0]);
		$album = trim ($tokens[1]);
		$artist = trim ($tokens[2]);

		$stmt = "insert into songs (name, album, artist, sources)"
			." values (?, ?, ?, ?)";
		$args = array ($song, $album, $artist, $js_sources);

		query ($stmt, $args);
	}

	$t = "list.php";
	redirect ($t);
}

echo ($body);

require ("footer.php");

pfinish ();
