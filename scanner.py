#! /usr/bin/env python

import psycopg2

resolutions = ["hour", "day", "week"]

conn = psycopg2.connect ("dbname='tracking' user='atw'")

cur = conn.cursor ()

name = "pushups"

cur.execute ("select * from trackers where name='" + name + "'")

rows = cur.fetchall ()

if len (rows) < 1:
    print "can't find tracker '" + name + "'"
    exit (1)

if len (rows) > 1:
    print "multiple trackers with same name '" + name + "'"
    exit (1)

tracker = rows[0]

cur.execute ("select * from track where name='pushups' and date_trunc ('day', timestamp) = date_trunc ('day', current_timestamp)")

rows = cur.fetchall ()

total = 0
for row in rows:
    total += row[1]

threshold = tracker[3]
if total < threshold:
    print name + " under thershold! %s/%d" % (total, threshold)
else:
    print "made it to threshold! %s/%d" % (total, threshold) 
