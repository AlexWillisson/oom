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

function add_sources ($song) {
	global $song_sources;

	$idx = $song->match_idx;
	$sources = $song->sources;

	if (isset ($song_sources[$idx])) {
		$src = $song_sources[$idx];

		if (is_array ($sources)) {
			$song_sources[$idx] = array_merge ($src, $sources);
		} else {
			$song_sources[$idx][] = $sources;
		}
	} else {
		if (is_array ($sources)) {
			$song_sources[$idx] = $sources;
		} else {
			$song_sources[$idx] = array ($sources);
		}
	}
}

function fix_id ($id) {
	$cleared = preg_replace ("/[^a-zA-Z0-9\s]/", "", $id);

	return (h(preg_replace ("/[\s]/", "-", $cleared)));
}

require ("common.php");

$pstart_args->js[] = "/oom/jquery-2.1.4.js";
$pstart_args->js[] = "/oom/jcanvas.js";

$pstart_args->css[] = "/oom/matcher.css";

$body = "";

pstart ();

require ("header.php");

$picked_sources = json_decode ("" . @$_REQUEST['sources']);
$match_map = json_decode ("" . @$_REQUEST['song_map'], true);

if ($match_map == NULL) {
	redirect ("list.php");
}

$source_map = fetch_sources ();
$sources = array_map (function ($s) { return ($s); }, $source_map);

$new_songs = array ();
$existing_songs = array ();
$song_sources = array ();

$keys = array_keys ($match_map);
for ($idx = 0; $idx < count ($keys); $idx++) {
	$columns = array ("name", "album", "artist", "sources", "song_id");
	$stmt = sprintf ("select %s from songs where song_id = ?", 
			 implode (", ", $columns));
	$q = query ($stmt, $keys[$idx]);

	if (($r = fetch ($q)) == NULL) {
		var_dump ("error, invalid song_id passed");
		pfinish ();
	}

	$res = parse_results ($columns, $r);

	$song = (object) NULL;
	$song->name = $res['name'];
	$song->album = $res['album'];
	$song->artist = $res['artist'];
	$song->sources = json_decode ("" . $res['sources']);
	$song->match_idx = $idx;
	$song->song_id = $res['song_id'];
	add_sources ($song);

	$existing_songs[] = $song;

	for ($jdx = 0; $jdx < count ($match_map[$keys[$idx]]); $jdx++) {
		$raw_song = $match_map[$keys[$idx]][$jdx];

		$song = (object) NULL;
		$song->name = $raw_song[0];
		$song->album = $raw_song[1];
		$song->artist = $raw_song[2];
		$song->sources = $picked_sources;
		$song->match_idx = $idx;
		add_sources ($song);

		$new_songs[] = $song;
	}
}

$body .= "<form action='add.php' method='post' id='songs-form'>\n";
$body .= "<input type='hidden' name='add' value='2' />\n";

$body .= "<div class='main-interface'>\n";

$body .= "<div style='float: left; width: 35%;' id='new-songs-body'>\n";

usort ($new_songs,
       function ($a, $b) {
	       return ($a->match_idx > $b->match_idx);
       });

for ($idx = 0; $idx < count ($new_songs); $idx++) {
	$song = $new_songs[$idx];

	$body .= sprintf ("<div class='new-song new-song-%d'"
			  ." data-song-idx='%d' data-id='%d'>\n",
			  h($song->match_idx), h($song->match_idx),
			  h($song->match_idx));
	$body .= "<table>\n";
	$body .= "<tr>\n";
	$body .= "<th class='song'>Song</th>\n";
	$body .= "<th class='album'>Album</th>\n";
	$body .= "<th class='artist'>Artist</th>\n";
	foreach ($sources as $s) {
		$body .= "<th class='source'>\n";
		$body .= $s;
		$body .= "</th>\n";
	}
	$body .= "</tr>\n";
	$body .= "<tr>\n";
	$body .= sprintf ("<td class='song-fields'"
			  ." data-field='name'>%s</td>\n",
			  h($song->name));
	$body .= sprintf ("<td class='song-fields'"
			  ." data-field='album'>%s</td>\n",
			  h($song->album));
	$body .= sprintf ("<td class='song-fields'"
			  ." data-field='artist'>%s</td>\n",
			  h($song->artist));

	foreach (array_keys ($source_map) as $source) {
		if (in_array ($source, $song->sources)) {
			$body .= sprintf ("<td class='source'>y</td>\n");
		} else {
			$body .= sprintf ("<td class='source'>n</td>\n");
		}
	}

	$body .= "</tr>\n";
	$body .= "</table>\n";
	$body .= "</div>\n";
}

$body .= "</div>\n";

$body .= "<div style='float: left; width: 29%;'>\n";
$body .= "<canvas id='lines' width='10' height='100'>\n";
$body .= "</canvas>\n";
$body .= "</div>\n";

$body .= "<div style='float: left; width: 35%;'>\n";
$body .= "<div class='end-song-padding'>\n";
$body .= "</div>\n";

usort ($existing_songs,
       function ($a, $b) {
	       return ($a->match_idx > $b->match_idx);
       });

$song_ids = array ();

for ($idx = 0; $idx < count ($existing_songs); $idx++) {
	$song = $existing_songs[$idx];

	$song_ids[] = $song->match_idx;

	$body .= sprintf ("<div class='end-song' id='song-%d'"
			  ." data-song-idx='%d'>\n",
			  h($song->match_idx), h($song->match_idx));
	$body .= "<table>\n";
	$body .= "<tr>\n";
	$body .= "<th class='song'>Song</th>\n";
	$body .= "<th class='album'>Album</th>\n";
	$body .= "<th class='artist'>Artist</th>\n";
	foreach ($sources as $s) {
		$body .= "<th class='source'>\n";
		$body .= $s;
		$body .= "</th>\n";
	}
	$body .= "</tr>\n";
	$body .= "<tr>\n";
	$body .= sprintf ("<input type='hidden' name='song-id-%d'"
			  ." value='%d' />\n",
			  $song->match_idx, $song->song_id);
	$body .= sprintf ("<td class='song-fields' id='name-%d'"
			  ." data-startval='%s'>%s</td>\n",
			  $song->match_idx, h($song->name), h($song->name));
	$body .= sprintf ("<input type='hidden' name='name-%d'"
			  ." value='%s' id='input-name-%d' />\n",
			  h($song->match_idx), h($song->name),
			  h($song->match_idx));
	$body .= sprintf ("<td class='song-fields' id='album-%d'"
			  ." data-startval='%s'>%s</td>\n",
			  $song->match_idx, h($song->album), h($song->album));
	$body .= sprintf ("<input type='hidden' name='album-%d'"
			  ." value='%s' id='input-album-%d' />\n",
			  h($song->match_idx), h($song->album),
			  h($song->match_idx));
	$body .= sprintf ("<td class='song-fields' id='artist-%d'"
			  ." data-startval='%s'>%s</td>\n",
			  $song->match_idx, h($song->artist), h($song->artist));
	$body .= sprintf ("<input type='hidden' name='artist-%d'"
			  ." value='%s' id='input-artist-%d' />\n",
			  h($song->match_idx), h($song->artist),
			  h($song->match_idx));

	$all_sources = $song_sources[$song->match_idx];

	foreach (array_keys ($source_map) as $source) {
		if (in_array ($source, $song->sources)) {
			$body .= sprintf ("<td class='source'"
					  ." data-source='%s'>y</td>\n",
					  fix_id ($source_map[$source]));
			$body .= sprintf ("<input type='hidden'"
					  ." name='%s-%d' value='1'"
					  ." id='%s-%d'/>\n",
					  fix_id ($source_map[$source]),
					  h($song->match_idx),
					  fix_id ($source_map[$source]),
					  h($song->match_idx));
		} else if (in_array ($source, $all_sources)) {
			$body .= sprintf ("<td class='source changed-source'"
					  ." data-source='%s'>y</td>\n",
					  fix_id ($source_map[$source]));
			$body .= sprintf ("<input type='hidden'"
					  ." name='%s-%d' value='1'"
					  ." id='%s-%d'/>\n",
					  fix_id ($source_map[$source]),
					  h($song->match_idx),
					  fix_id ($source_map[$source]),
					  h($song->match_idx));
		} else {
			$body .= sprintf ("<td class='source'"
					  ." data-source='%s'>n</td>\n",
					  fix_id ($source_map[$source]));
			$body .= sprintf ("<input type='hidden'"
					  ." name='%s-%d' value='0'"
					  ." id='%s-%d'/>\n",
					  fix_id ($source_map[$source]),
					  h($song->match_idx),
					  fix_id ($source_map[$source]),
					  h($song->match_idx));
		}
	}

	$body .= "</tr>\n";
	$body .= "</table>\n";
	$body .= "</div>\n";
}

$body .= "</div>\n";

$body .= "</div>\n";

$body .= "</div>\n";

$body .= "<div class='submit-area'>\n";
$body .= "<input type='submit' value='submit' />\n";
$body .= "</div>\n";

$body .= sprintf ("<input type='hidden' name='song_ids' value='%s'"
		  ." id='song_ids' />\n",
		  json_encode ($song_ids));

$body .= "</form>\n";

$body .= "<script src='matcher.js'></script>\n";

echo ($body);

require ("footer.php");

pfinish ();
