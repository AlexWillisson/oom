<?php

require ("common.php");

pstart ();

require ("header.php");

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

$columns = array ("artist", "album", "name", "sources");
$stmt = sprintf ("select %s from songs", implode (", ", $columns));
$q = query ($stmt);

$source_map = fetch_sources ();
$sources = array_map (function ($s) { return ($s); }, $source_map);

$body = "";

$body .= "<table>\n";
$body .= "<tr>\n";
$body .= "<th class='artist'>Artist</th>\n";
$body .= "<th class='album'>Album</th>\n";
$body .= "<th class='song'>Song</th>\n";
foreach ($sources as $s) {
	$body .= "<th class='source'>\n";
	$body .= $s;
	$body .= "</th>\n";
}
$body .= "</tr>\n";

while (($r = fetch ($q)) != NULL) {
	$res = parse_results ($columns, $r);

	$body .= "<tr>\n";
	$body .= sprintf ("<td>%s</td>\n", $res['artist']);
	$body .= sprintf ("<td>%s</td>\n", $res['album']);
	$body .= sprintf ("<td>%s</td>\n", $res['name']);

	$js = json_decode ($res['sources']);
	for ($idx = 0; $idx < count ($source_map); $idx++) {
		if (isset ($js[$idx])) {
			$body .= sprintf ("<td class='source'>y</td>\n");
		} else {
			$body .= sprintf ("<td class='source'>n</td>\n");
		}
	}

	$body .= "</tr>\n";
}
$body .= "</table>\n";

echo ($body);

require ("footer.php");

pfinish ();
