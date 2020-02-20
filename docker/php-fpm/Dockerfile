# @version 5.6.10
FROM centos:centos7

# prerequisites
RUN yum install -y git gcc gcc-c++ cpp kernel-headers.x86_64 kernel-devel libxml2-devel libxslt-devel openssl-devel libcurl-devel readline-devel bzip2-devel tar wget make autoconf libpng-devel libssl-dev libsasl2-dev libjpeg-devel freetype-devel openldap-devel php-pear openssl

# cmake # @see https://cmake.org/
RUN wget http://files1.directadmin.com/services/custombuild/cmake-3.9.2.tar.gz
RUN tar xzf cmake-3.9.2.tar.gz
WORKDIR cmake-3.9.2
RUN ./configure
RUN make
RUN make install
RUN cmake  --version

# libzip # @see https://libzip.org/
RUN wget https://libzip.org/download/libzip-1.5.2.tar.gz
RUN tar zxvf libzip-1.5.2.tar.gz
WORKDIR  libzip-1.5.2
RUN mkdir build
WORKDIR  build
RUN ls -l ..
RUN cmake ..
RUN make
RUN make test
RUN make install

# locale
RUN localedef -c -i en_GB -f UTF-8 en_GB.UTF-8
ENV LANG en_GB.UTF-8
ENV LANGUAGE en_GB:en
ENV LC_ALL en_GB.UTF-8

WORKDIR /tmp

# icu4c # @see http://www.linuxfromscratch.org/blfs/view/svn/general/icu.html
RUN wget -nv -O - http://download.icu-project.org/files/icu4c/55.1/icu4c-55_1-src.tgz | tar zx
WORKDIR icu/source
RUN ./configure
RUN make -j
RUN make install

WORKDIR /tmp

# php
RUN ls
RUN wget -nv -O - http://php.net/distributions/php-7.3.12.tar.gz | tar zx
WORKDIR php-7.3.12
RUN rm configure
RUN ./buildconf --force
RUN ./configure --prefix=/usr/local/php \
	 --with-config-file-path=/usr/local/php/etc/ \
	 --disable-cgi \
	 --with-gd \
	 --enable-sockets \
	 --without-pear \
	 --without-sqlite3 \
	 --with-zlib \
	 --without-pdo-sqlite \
	 --with-curl=/usr/bin/curl \
	 --with-openssl \
	 --libdir=/usr/lib64 \
	 --with-libdir=lib64 \
	 --enable-opcache \
	 --enable-fileinfo \
	 --enable-mbstring \
	 --with-readline \
	 --enable-fpm \
	 --enable-zip \
	 --enable-intl \
	 --with-pdo-mysql \
	 --with-mysqli \
	 --with-gettext \
	 --enable-bcmath
RUN make
RUN make install
RUN useradd fpm
RUN ln -s /usr/local/php/bin/* /usr/local/bin

WORKDIR /tmp

# mongo driver
RUN git clone --branch 1.6.1 https://github.com/mongodb/mongo-php-driver.git
WORKDIR mongo-php-driver/
RUN git submodule update --init
RUN phpize
RUN ./configure
RUN make -j
RUN make install

WORKDIR /tmp

# libjpeg
RUN wget -nv -O - http://www.ijg.org/files/jpegsrc.v8c.tar.gz | tar zx
WORKDIR jpeg-8c/
RUN ./configure
RUN make -j
RUN make install

WORKDIR /tmp

# xdebug
RUN git clone https://github.com/xdebug/xdebug.git
WORKDIR xdebug
RUN phpize
RUN ./configure
RUN make -j
RUN make install

WORKDIR /tmp

# Composer
# Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === 'e0012edf3e80b6978849f5eff0d4b4e4c79ff1609dd1e613307e16318854d24ae64f26d17af3ef0bf7cfb710ca74755a') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/application

VOLUME ["/usr/local/php/etc"]
ENTRYPOINT ["/usr/local/php/sbin/php-fpm"]
CMD ["-F"]

