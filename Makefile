CFLAGS=-g -Wall -I/usr/include/postgresql
LIBS=-lpq

install:
	install-site

uninstall:
	uninstall-site

clean:
	rm -f *~
