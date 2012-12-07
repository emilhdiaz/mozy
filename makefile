MOZY=$(HOME)/$(C9_PID)
BIN=$(HOME)/bin
LIB=$(HOME)/lib/php
PHP=$(LIB)/php-5.4.9
EXT=$(PHP)/ext


download:
	rm -rf $(LIB)
	mkdir $(LIB)
	wget -P $(LIB) http://us.php.net/get/php-5.4.9.tar.gz/from/us1.php.net/mirror -O "php-5.4.9.tar.gz"
	tar xzf $(LIB)/php-5.4.9.tar.gz -C $(LIB)
	cd $(EXT); git clone https://github.com/krakjoe/pthreads.git

configure:
	cd $(PHP); ./buildconf --force
	cd $(PHP); ./configure \
		--prefix=$(PHP) \
		--without-pear \
		--enable-pcntl \
		--enable-sysvsem \
		--enable-sysvshm \
		--enable-sysvmsg \
		--with-tsrm-pthreads \
		--enable-maintainer-zts \
		--enable-pthreads \
		--enable-calendar

build: 
	make -C $(PHP) > $(PHP)/make.log 2>&1
	make -C $(PHP) install

install:
	rm -f $(BIN)/pear
	rm -f $(BIN)/peardev
	rm -f $(BIN)/pecl
	rm -f $(BIN)/phar
	rm -f $(BIN)/phar.phar
	rm -f $(BIN)/php
	rm -f $(BIN)/php-cgi
	rm -f $(BIN)/php-config
	rm -f $(BIN)/phpize
	ln -s $(PHP)/bin/phar $(BIN)/phar
	ln -s $(PHP)/bin/phar.phar $(BIN)/phar.phar
	ln -s $(PHP)/bin/php $(BIN)/php
	ln -s $(PHP)/bin/php-cgi $(BIN)/php-cgi
	ln -s $(PHP)/bin/php-config $(BIN)/php-config
	ln -s $(PHP)/bin/phpize $(BIN)/phpize
	cp $(MOZY)/php.ini $(PHP)/lib/
