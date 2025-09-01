prefix ?= /usr
scriptdir ?= $(prefix)/bin
mandir ?= $(prefix)/share/man

SCRIPT_FILES = $(filter-out Makefile $(wildcard *.md), $(wildcard *))
SCRIPT_INSTALLABLES = $(patsubst %, $(DESTDIR)$(scriptdir)/%, $(SCRIPT_FILES))

MAN_FILES = $(wildcard *.1.md)
MAN_INSTALLABLES = $(patsubst %.md, $(DESTDIR)$(mandir)/man1/%, $(MAN_FILES))

INSTALLABLES = $(SCRIPT_INSTALLABLES) $(MAN_INSTALLABLES)

.PHONY:	install

install:	$(INSTALLABLES) $(MAN_INSTALLABLES)

mans:	$(MAN_INSTALLABLES)

$(DESTDIR)$(scriptdir)/%: %
	install -D -m 0755  $< $@

$(DESTDIR)$(mandir)/man1/%: %.md
	mkdir -p $(DESTDIR)$(mandir)/man1
	pandoc $< -s -t man > $@

