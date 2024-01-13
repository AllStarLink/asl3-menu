prefix = /usr

install:
	install -m 755 scripts/astdn.sh		$(DESTDIR)$(prefix)/sbin/
	install -m 755 scripts/astres.sh	$(DESTDIR)$(prefix)/sbin/
	install -m 755 scripts/astup.sh		$(DESTDIR)$(prefix)/sbin/
	install -m 755 scripts/node-setup	$(DESTDIR)$(prefix)/sbin/
	install -m 755 scripts/restore-node	$(DESTDIR)$(prefix)/sbin/
	install -m 755 scripts/save-node	$(DESTDIR)$(prefix)/sbin/

uninstall: 
	-rm -f $(DESTDIR)$(prefix)/sbin/astdn.sh
	-rm -f $(DESTDIR)$(prefix)/sbin/astres.sh
	-rm -f $(DESTDIR)$(prefix)/sbin/astup.sh
	-rm -f $(DESTDIR)$(prefix)/sbin/node-setup
	-rm -f $(DESTDIR)$(prefix)/sbin/restore-node
	-rm -f $(DESTDIR)$(prefix)/sbin/save-node

.PHONY: install uninstall
