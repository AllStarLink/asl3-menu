prefix ?= /usr
supportdir ?= $(prefix)/lib/asterisk/php-backend

SUPPORT_FILES = $(filter-out Makefile %.md setup-debug.sh commands.txt, $(wildcard *))
SUPPORT_INSTALLABLES = $(patsubst %, $(DESTDIR)$(supportdir)/%, $(SUPPORT_FILES))

INSTALLABLES = $(SUPPORT_INSTALLABLES)

.PHONY:	install
install:	$(INSTALLABLES)

$(DESTDIR)$(supportdir)/asl-configuration.php:	asl-configuration.php
	install -D -m 0755 $< $@

$(DESTDIR)$(supportdir)/%: %
	install -D -m 0644  $< $@
