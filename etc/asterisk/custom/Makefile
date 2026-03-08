sysconfdir ?= /etc
conf_dir   ?= $(sysconfdir)/asterisk/custom

CONF_FILES := $(wildcard *.md)
CONF_INSTALLABLES := $(patsubst %, $(DESTDIR)$(conf_dir)/%, $(CONF_FILES))

CONF_SUBDIRS = $(filter-out %/., $(wildcard */))

.PHONY:	install samples all

install: $(CONF_INSTALLABLES)
	$(foreach dir, $(CONF_SUBDIRS), $(MAKE) -C $(dir) $@;)

samples:
	$(foreach dir, $(CONF_SUBDIRS), $(MAKE) -C $(dir) $@;)

all: install samples

$(DESTDIR)$(conf_dir)/%: %
	install -D -m 0644 -- $< $@

