#! /usr/bin/env python

import psycopg2

conn = psycopg2.connect ("dbname='tracking' user='atw'")

cur = conn.cursor ()

cur.execute ("select * from track where name='piano'")

rows = cur.fetchall ()

for row in rows:
    print row
