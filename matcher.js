(function($) {  
    $.fn.extend({  
        //Let the user resize the canvas to the size he/she wants  
        resizeCanvas:  function(w, h) {  
            var c = $(this)[0]  
            c.width = w;  
            c.height = h  
        }  
    })  
})(jQuery)

function rm_line (obj_id) {
    for (idx = 0; idx < canvas_objs.length; idx++) {
	if (canvas_objs[idx].obj_id == obj_id) {
	    canvas_objs.splice (idx, 1);
	    return;
	}
    }
}

function min (a, b) {
    if (a < b) {
	return (a);
    } else {
	return (b);
    }
}

function max (a, b) {
    if (a > b) {
	return (a);
    } else {
	return (b);
    }
}

function line_intersect (line0, line1) {
    if (line0 == null || line1 == null) {
	return (false);
    }

    x0 = line0["startx"];
    x1 = line0["endx"];
    y0 = line0["starty"];
    y1 = line0["endy"];
    x2 = line1["startx"];
    x3 = line1["endx"];
    y2 = line1["starty"];
    y3 = line1["endy"];

    lowx0 = min (x0, x1);
    highx0 = max (x0, x1);
    lowy0 = min (y0, y1);
    highy0 = max (y0, y1);

    lowx1 = min (x2, x3);
    highx1 = max (x2, x3);
    lowy1 = min (y2, y3);
    highy1 = max (y2, y3);

    if (x0 == x1 && x2 == x3) {
	return (false);
    } else if (x0 == x1) {
	x = x0;
	y = (x - x2) * ((y3 - y2) / (x3 - x2)) + y2;
    } else if (x2 == x3) {
	x = x2;
	y = (x - x0) * ((y1 - y0) / (x1 - x0)) + y0;
    } else if (y0 == y1 && y2 == y3) {
	return (false);
    } else {
	x = (((x3 * y2 - x2 * y3) / (x3 - x2))
	     - ((x1 * y0 - x0 * y1) / (x1 - x0)))
	    / (((y1 - y0) / (x1 - x0)) - ((y3 - y2) / (x3 - x2)));
	y = (x - x0) * ((y1 - y0) / (x1 - x0)) + y0;
    }

    if (x >= lowx0 && x <= highx0 && y >= lowy0 && y <= highy0
	&& x >= lowx1 && x <= highx1 && y >= lowy1 && y <= highy1) {
	return (true);
    } else {
	return (false);
    }
}

function draw () {
    setTimeout (draw, 30);

    ctx = canvas[0].getContext("2d");
    ctx.clearRect (-50, -50, canvas.width () + 50, canvas.height () + 50);

    dragged = null;
    for (idx = 0; idx < canvas_objs.length; idx++) {
	if (canvas_objs[idx]["obj_id"] == "dragged") {
	    dragged = canvas_objs[idx];
	}
    }

    for (idx = 0; idx < canvas_objs.length; idx++) {
	obj_id = canvas_objs[idx]["obj_id"];
	startx = canvas_objs[idx]["startx"];
	starty = canvas_objs[idx]["starty"];
	endx = canvas_objs[idx]["endx"];
	endy = canvas_objs[idx]["endy"];

	if (obj_id == "dragged") {
	    canvas.drawLine ({strokeStyle: "#000",
			      strokeWidth: 1,
			      x1: startx, y1: starty,
			      x2: endx, y2: endy});
	} else {
	    if (line_intersect (canvas_objs[idx], dragged)) {
		canvas.drawLine ({strokeStyle: "#f00",
				  strokeWidth: 1,
				  x1: startx, y1: starty,
				  x2: endx, y2: endy});
	    } else {
		canvas.drawLine ({strokeStyle: "#000",
				  strokeWidth: 1,
				  x1: startx, y1: starty,
				  x2: endx, y2: endy});
	    }
	}
    }
}

function makeid (len) {
    var text = "";
    var possible = "abcdefghijklmnopqrstuvwxyz0123456789";

    for (var idx = 0; idx < len; idx++ ) {
        text += possible.charAt (Math.floor (Math.random () * possible.length));
    }

    return text;
}

$("body").mousedown (function (event) {
    last_mousedown.x = event.pageX - canvas_pos.left;
    last_mousedown.y = event.pageY - canvas_pos.top;
    rm_line ("dragged");
    canvas_objs.push ({"obj_id": "dragged",
		       "startx": last_mousedown.x,
		       "starty": last_mousedown.y,
		       "endx": last_mousedown.x,
		       "endy": last_mousedown.y});
});

$("body").mousemove (function (event) {
    if (event.buttons != 0) {
	rm_line ("dragged");
	canvas_objs.push ({"obj_id": "dragged",
			   "startx": last_mousedown.x,
			   "starty": last_mousedown.y,
			   "endx": event.pageX - canvas_pos.left,
			   "endy": event.pageY - canvas_pos.top});
    }
});

$("body").mouseup (function (event) {
    rm_line ("dragged");

    last_mouseup.x = event.pageX - canvas_pos.left;
    last_mouseup.y = event.pageY - canvas_pos.top;
});

$("div.end-song td.source").mousedown (function (event) {
    trigger = $(event.currentTarget);

    node = $(trigger.parent ());

    while (node.prop ("tagName") != "DIV") {
	node = $(node.parent ());
    }

    song_idx = node.data ("song-idx");
    source = trigger.data ("source");

    if (trigger.html () == "n") {
	trigger.html ("y");
	$("#" + source + "-" + song_idx).val (1);
    } else {
	trigger.html ("n");
	$("#" + source + "-" + song_idx).val (0);
    }

    trigger.css ("color", "#2ecc40");
    trigger.css ("font-weight", "bold");
});

var canvas_objs, canvas, canvas_pos, last_mousedown, last_mouseup;
    
function init_page (event) {
    canvas_objs = []

    canvas = $("#lines");

    canvas.css ("height", $("#new-songs-body").css ("height"));

    canvas.resizeCanvas (parseInt (canvas.css ("width")),
			 parseInt (canvas.css ("height")));

    canvas_pos = canvas.offset ();

    last_mousedown = {};
    last_mouseup = {};

    setTimeout (draw, 30);

    end_songs = $(".end-song");
    for (idx = 0; idx < end_songs.length; idx++) {
	end_song_entry = $(end_songs[idx]);
	song_idx = end_song_entry.data ("song-idx");

	group_top = Infinity;
	group_bottom = -Infinity;

	matched_songs = $("div.new-song-" + song_idx);
	for (jdx = 0; jdx < matched_songs.length; jdx++) {
	    song = $(matched_songs[jdx]);

	    coords = song.offset ();

	    group_top = min (coords.top, group_top);
	    group_bottom = max (coords.top + parseInt (song.css ("height")),
				group_bottom);
	}

	midpoint = (group_top + group_bottom) / 2

	mid_top = midpoint - parseInt (end_song_entry.css ("height")) / 2;
	mid_left = end_song_entry.offset().left;

	end_song_entry.offset ({"top": mid_top, "left": mid_left});
    }

    new_songs = $(".new-song");
    for (idx = 0; idx < new_songs.length; idx++) {
	new_song_entry = $(new_songs[idx]);
	new_song_pos = new_song_entry.offset ();
	new_song_height = parseInt (new_song_entry.css ("height"));
	y_start = new_song_pos.top + (new_song_height / 2) - canvas_pos.top;

	jdx = new_song_entry.data ("song-idx");
	end_song_entry = $("#song-" + jdx);
	end_song_pos = end_song_entry.offset ();
	end_song_height = parseInt (end_song_entry.css ("height"));
	y_end = end_song_pos.top + (end_song_height / 2) - canvas_pos.top;

	canvas_objs.push ({"obj_id": makeid (5),
    			   "startx": 25,
    			   "starty": y_start,
    			   "endx": parseInt (canvas.css ("width")) - 25,
    			   "endy": y_end});
    }
}

init_page (null);

window.onresize = init_page;
