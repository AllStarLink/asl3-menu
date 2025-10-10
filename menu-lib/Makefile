prefix ?= /usr
share_prefix ?= $(prefix)/share
ast_prefix ?= $(share_prefix)/asterisk

menu_helper_dir ?= $(ast_prefix)/menu-lib

MENU_HELPER_FILES = $(filter-out Makefile, $(wildcard *))
MENU_HELPER_INSTALLABLES = $(patsubst %, $(DESTDIR)$(menu_helper_dir)/%, $(MENU_HELPER_FILES))

.PHONY:	install
install:	$(MENU_HELPER_INSTALLABLES)

$(DESTDIR)$(menu_helper_dir)/%: %
	install -D -m 0755  $< $@

