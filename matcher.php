<?php

require ("common.php");

$pstart_args->js[] = "/oom/jquery-2.1.4.js";
$pstart_args->js[] = "/oom/jcanvas.js";

$pstart_args->css[] = "/oom/matcher.css";

$body = "";

pstart ();

require ("header.php");

$new_songs = array ();
$existing_songs = array ();

$song = (object) NULL;
$song->name = "Chrono Trigger Theme";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 0;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "chrono trigger theme";
$song->album = "gaming fantasy";
$song->artist = "taylor davis";
$song->match_idx = 0;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "Chrono Trigger Theme";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 0;

$existing_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 1;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "zelda medley";
$song->album = "gaming fantasy";
$song->artist = "taylor davis";
$song->match_idx = 1;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 1;

$existing_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 2;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "zelda medley";
$song->album = "gaming fantasy";
$song->artist = "taylor davis";
$song->match_idx = 2;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 2;

$existing_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 3;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "zelda medley";
$song->album = "gaming fantasy";
$song->artist = "taylor davis";
$song->match_idx = 3;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 3;

$existing_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 4;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "zelda medley";
$song->album = "gaming fantasy";
$song->artist = "taylor davis";
$song->match_idx = 4;

$new_songs[] = $song;

$song = (object) NULL;
$song->name = "Zelda Medley";
$song->album = "Gaming Fantasy";
$song->artist = "Taylor Davis";
$song->match_idx = 4;

$existing_songs[] = $song;

$body .= "<div style='float: left; width: 100%; padding-bottom: 5em;'>\n";

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
	$body .= sprintf ("<td>%s</td>\n", h($song->name));
	$body .= sprintf ("<td>%s</td>\n", h($song->artist));
	$body .= sprintf ("<td>%s</td>\n", h($song->album));
	$body .= "</tr>\n";
	$body .= "</table>\n";
	$body .= "</div>\n";
}

$body .= "</div>\n";

$body .= "<div style='float: left; width: 29%; height: 50em;'>\n";
$body .= "<canvas id='lines' width='500' height='800'>\n";
$body .= "</canvas>\n";
$body .= "</div>\n";

$body .= "<div style='float: left; width: 35%; height: 50em;'>\n";
$body .= "<div class='end-song-padding'>\n";
$body .= "</div>\n";

usort ($existing_songs,
       function ($a, $b) {
	       return ($a->match_idx > $b->match_idx);
       });

for ($idx = 0; $idx < count ($existing_songs); $idx++) {
	$song = $existing_songs[$idx];

	$body .= sprintf ("<div class='end-song' id='song-%d'"
			  ." data-song-idx='%d'>\n",
			  h($song->match_idx), h($song->match_idx));
	$body .= "<table>\n";
	$body .= "<tr>\n";
	$body .= sprintf ("<td>%s</td>\n", h($song->name));
	$body .= sprintf ("<td>%s</td>\n", h($song->artist));
	$body .= sprintf ("<td>%s</td>\n", h($song->album));
	$body .= "</tr>\n";
	$body .= "</table>\n";
	$body .= "</div>\n";
}

$body .= "</div>\n";

$body .= "</div>\n";

$body .= "<script src='matcher.js'></script>\n";

echo ($body);

require ("footer.php");

pfinish ();
