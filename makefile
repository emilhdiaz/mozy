BIN=~/bin
LIB=~/lib/php
HOME=~/$(C9_PID)
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

install: 
	make -C $(PHP) > $(PHP)/make.log 2>&1
	make -C $(PHP) install
	rm $(BIN)/pear
	rm $(BIN)/peardev
	rm $(BIN)/pecl
	rm $(BIN)/phar
	rm $(BIN)/phar.phar
	rm $(BIN)/php
	rm $(BIN)/php-cgi
	rm $(BIN)/php-config
	rm $(BIN)/phpize
	ln -s $(PHP)/bin/phar $(BIN)/phar
	ln -s $(PHP)/bin/phar.phar $(BIN)/phar.phar
	ln -s $(PHP)/bin/php $(BIN)/php
	ln -s $(PHP)/bin/php-cgi $(BIN)/php-cgi
	ln -s $(PHP)/bin/php-config $(BIN)/php-config
	ln -s $(PHP)/bin/phpize $(BIN)/phpize
	cp $(HOME)/php.ini $(PHP)/lib/
