#
# Build variables
#
SRCNAME = asl3-menu
PKGNAME = $(SRCNAME)
RELVER = 1.0
DEBVER = 1

BUILDABLES = bin # php-backend

ifdef ${DESTDIR}
DESTDIR=${DESTDIR}
endif

default:
	@echo This does nothing, use 'make install'

install:
	@echo DESTDIR=$(DESTDIR)
	$(foreach dir, $(BUILDABLES), $(MAKE) -C $(dir) install;)

deb:	debclean debprep
	debuild

debchange:
	debchange -v $(RELVER)-$(DEBVER)
	debchange -r

debprep: debclean
	(cd .. && \
		rm -f $(PKGNAME)-$(RELVER) && \
		rm -f $(PKGNAME)-$(RELVER).tar.gz && \
		rm -f $(PKGNAME)_$(RELVER).orig.tar.gz && \
		ln -s $(SRCNAME) $(PKGNAME)-$(RELVER) && \
		tar --exclude=".git" -h -zvcf $(PKGNAME)-$(RELVER).tar.gz $(PKGNAME)-$(RELVER) && \
		ln -s $(PKGNAME)-$(RELVER).tar.gz $(PKGNAME)_$(RELVER).orig.tar.gz )

debclean:
	rm -f ../$(PKGNAME)_$(RELVER)*
	rm -f ../$(PKGNAME)-$(RELVER)*
	rm -rf debian/$(PKGNAME)
	rm -f debian/files
	rm -rf debian/.debhelper/
	rm -f debian/debhelper-build-stamp
	rm -f debian/*.substvars


