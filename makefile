MOZY=$(HOME)/$(C9_PID)/php
TMP=/tmp
BIN=$(HOME)/bin
LIB=$(HOME)/lib
PHP=$(LIB)/php
EXT=$(PHP)/ext
ICU=$(LIB)/icu
DOC=$(LIB)/phpDocumentor
NCURSES=$(LIB)/ncurses
PEAR=$(HOME)/pear
LD_LIBRARY_PATH=$(ICU)/usr/local/bin

clean-php:
	make -C $(PHP) clean
	rm -rf $(PHP)
	rm -f $(BIN)/phar
	rm -f $(BIN)/phar.phar
	rm -f $(BIN)/php
	rm -f $(BIN)/php-cgi
	rm -f $(BIN)/php-config
	rm -f $(BIN)/phpize

install-php:
	mkdir $(PHP)
	cd ($TMP); wget http://us.php.net/get/php-5.4.9.tar.gz/from/us1.php.net/mirror -O "php.tar.gz"
	cd ($LIB); tar xzf $(TMP)/php.tar.gz; mv php-5.4.9 $(PHP)
	cd ($TMP); wget http://pecl.php.net/get/proctitle -O "proctitle.tar.gz"
	cd ($EXT); tar xzf $(TMP)/proctitle.tar.gz; mv proctitle-0.1.2 proctitle
	cd $(EXT); git clone https://github.com/krakjoe/pthreads.git

	cd $(PHP); ./buildconf --force
	cd $(PHP); ./configure \
		--prefix=$(PHP) \
		--without-pear \
		--with-xsl \
		--with-tsrm-pthreads \
		--with-openssl \
		--enable-pcntl \
		--enable-sysvsem \
		--enable-sysvshm \
		--enable-sysvmsg \
		--enable-maintainer-zts \
		--enable-pthreads \
		--enable-calendar \
		--enable-zip \
		--enable-libxml \
		--enable-intl
	make -C $(PHP) clean
	make -C $(PHP) > $(PHP)/make.log 2>&1
	make -C $(PHP) install

	cd $(EXT)/proctitle; phpize; ./configure
	make -C $(EXT)/proctitle
	make -C $(EXT)/proctitle install

	ln -s $(PHP)/bin/phar $(BIN)/phar
	ln -s $(PHP)/bin/phar.phar $(BIN)/phar.phar
	ln -s $(PHP)/bin/php $(BIN)/php
	ln -s $(PHP)/bin/php-cgi $(BIN)/php-cgi
	ln -s $(PHP)/bin/php-config $(BIN)/php-config
	ln -s $(PHP)/bin/phpize $(BIN)/phpize
	cp $(MOZY)/php.ini $(PHP)/lib/

clean-pear:
	rm -rf $(PEAR)
	rm -f $(BIN)/pear
	rm -f $(BIN)/peardev
	rm -f $(BIN)/pecl

install-pear: clean-pear
	mkdir $(PEAR)
	cd $(PEAR); wget http://pear.php.net/go-pear.phar
	cd $(PEAR); php go-pear.phar $(PEAR)
	ln -s $(PEAR)/bin/pear $(BIN)/pear
	ln -s $(PEAR)/bin/peardev $(BIN)/peardev
	ln -s $(PEAR)/bin/pecl $(BIN)/pecl

clean-doc:
	rm -rf $(DOC)

install-doc: clean-doc
	mkdir $(DOC)
	cd $(DOC); wget https://raw.github.com/phpDocumentor/phpDocumentor2/develop/installer.php
	cd $(DOC); php installer.php

clean-icu:
	rm -rf $(ICU)
	rm -f $(BIN)/derb
	rm -f $(BIN)/genbrk
	rm -f $(BIN)/gencfu
	rm -f $(BIN)/gencnval
	rm -f $(BIN)/gendict
	rm -f $(BIN)/icu-config
	rm -f $(BIN)/icuinfo
	rm -f $(BIN)/makeconv
	rm -f $(BIN)/pkdata
	rm -f $(BIN)/uconv

install-icu: clean-icu
	mkdir $(ICU)
	cd $(ICU); wget http://download.icu-project.org/files/icu4c/50.1/icu4c-50_1-RHEL6-i386.tgz -O "icu.tar.gz"
	cd $(ICU); tar xzf icu.tar.gz
	ln -s $(ICU)/usr/local/bin/derb $(BIN)/derb
	ln -s $(ICU)/usr/local/bin/genbrk $(BIN)/genbrk
	ln -s $(ICU)/usr/local/bin/gencfu $(BIN)/gencfu
	ln -s $(ICU)/usr/local/bin/gencnval $(BIN)/gencnval
	ln -s $(ICU)/usr/local/bin/gendict $(BIN)/gendict
	ln -s $(ICU)/usr/local/bin/icu-config $(BIN)/icu-config
	ln -s $(ICU)/usr/local/bin/icuinfo $(BIN)/icuinfo
	ln -s $(ICU)/usr/local/bin/makeconv $(BIN)/makeconv
	ln -s $(ICU)/usr/local/bin/pkdata $(BIN)/pkdata
	ln -s $(ICU)/usr/local/bin/uconv $(BIN)/uconv

clean-ncurses:
	rm -rf $(NCURSES)
	rm -f $(BIN)/ncurses

install-ncurses: clean-ncurses
	cd $(TMP); wget ftp://ftp.gnu.org/pub/gnu/ncurses/ncurses-5.9.tar.gz -O "ncurses.tar.gz"
	cd $(LIB); tar xzf $(TMP)/ncurses.tar.gz; mv ncurses-5.9 $(NCURSES)
	cd $(NCURSES); ./configure --prefix=$(NCURSES)
	make -C $(NCURSES) clean
	make -C $(NCURSES)
	make -C $(NCURSES) install
	ln -s $(NCURSES)/ncurses $(BIN)/ncurses

install-mozy:
	ln -s $(MOZY)/mozy.php $(BIN)/mozy
