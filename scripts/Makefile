prefix ?= /usr
scriptdir ?= $(prefix)/sbin

SCRIPT_FILES = $(filter-out Makefile, $(wildcard *))
SCRIPT_INSTALLABLES = $(patsubst %, $(DESTDIR)$(scriptdir)/%, $(SCRIPT_FILES))

INSTALLABLES = $(SCRIPT_INSTALLABLES)

.PHONY:	install
install:	$(INSTALLABLES)

$(DESTDIR)$(scriptdir)/%: %
	install -D -m 0755  $< $@

