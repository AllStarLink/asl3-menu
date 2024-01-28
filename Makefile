#
# Build variables
#
RELVER = 1.0.0
PKGNAME = asl3-menu

BUILDABLES =		\
	scripts		\
	php-backend

ifdef ${DESTDIR}
DESTDIR=${DESTDIR}
endif

ROOT_FILES = LICENSE README.md
ROOT_INSTALLABLES = $(patsubst %, $(DESTDIR)$(docdir)/%, $(CONF_FILES))

default:
	@echo This does nothing, use 'make install'

install: $(ROOT_INSTALLABLES)
	@echo DESTDIR=$(DESTDIR)
	$(foreach dir, $(BUILDABLES), $(MAKE) -C $(dir) install;)

$(DESTDIR)$(docdir)/%: %
	install -D -m 0644  $< $@

