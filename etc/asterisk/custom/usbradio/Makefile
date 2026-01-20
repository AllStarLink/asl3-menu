sysconfdir ?= /etc
conf_dir   ?= $(sysconfdir)/asterisk/custom/usbradio

DEMO_FILES := $(wildcard *-demo.conf)
DEMO_INSTALLABLES := $(patsubst %, $(DESTDIR)$(conf_dir)/%, $(DEMO_FILES))

CONF_FILES := $(filter-out $(DEMO_FILES),$(wildcard *.conf)) $(wildcard *.md)
CONF_INSTALLABLES := $(patsubst %, $(DESTDIR)$(conf_dir)/%, $(CONF_FILES))

.PHONY:	install samples all

install: $(CONF_INSTALLABLES)

samples: $(DEMO_INSTALLABLES)

all: install samples

$(DESTDIR)$(conf_dir)/%: %
	install -D -o asterisk -g asterisk -m 0644 $< $@

