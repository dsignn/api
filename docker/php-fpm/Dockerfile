# @version 5.6.10
FROM centos:centos7

# prerequisites
RUN yum install -y  libcurl4-openssl-dev ldconfig curl cmake libtool  git gcc gcc-c++ cpp kernel-headers.x86_64 kernel-devel libxml2-devel libxslt-devel openssl-devel libcurl-devel readline-devel bzip2-devel tar wget make autoconf libpng-devel libssl-dev libsasl2-dev libjpeg-devel freetype-devel openldap-devel php-pear openssl

# cmake # @see https://cmake.org/
RUN wget http://files1.directadmin.com/services/custombuild/cmake-3.9.2.tar.gz
RUN tar xzf cmake-3.9.2.tar.gz
WORKDIR cmake-3.9.2
RUN ./configure
RUN make
RUN make install
RUN cmake  --version

# libzip # @see https://libzip.org/
RUN wget https://nih.at/libzip/libzip-1.5.2.tar.gz
RUN tar zxvf libzip-1.5.2.tar.gz
WORKDIR  libzip-1.5.2
RUN mkdir build
WORKDIR  build
RUN cmake ..
RUN make
RUN make install
#RUN export LD_LIBRARY_PATH="$LD_LIBRARY_PATH:$PHP_DIR/libzip-1.4.0/lib"
#RUN ln -s ../lib/zipconf.h $PHP_DIR//build-src/srclib/libzip-1.2.0/include/zipconf.h

# locale
RUN localedef -c -i en_GB -f UTF-8 en_GB.UTF-8
ENV LANG en_GB.UTF-8
ENV LANGUAGE en_GB:en
ENV LC_ALL en_GB.UTF-8

WORKDIR /tmp

# icu4c # @see http://www.linuxfromscratch.org/blfs/view/svn/general/icu.html
RUN wget -nv -O - https://github.com/unicode-org/icu/releases/download/release-66-1/icu4c-66_1-src.tgz | tar zx
WORKDIR icu/source
RUN ./configure --prefix=/usr
RUN make
RUN make install

WORKDIR /tmp

# php
RUN ls
RUN wget -nv -O - http://php.net/distributions/php-7.2.32.tar.gz | tar zx
WORKDIR php-7.2.32
RUN rm configure
RUN ./buildconf --force
RUN ./configure --prefix=/usr/local/php \
	 --with-config-file-path=/usr/local/php/etc/ \
	 --disable-cgi \
	 --with-gd \
	 --enable-sockets \
	 --without-pear \
	 --without-sqlite3 \
	 --with-libzip \
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
#	 --enable-intl \
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
RUN php -r "if (hash_file('SHA384', 'composer-setup.php') === '756890a4488ce9024fc62c56153228907f1545c228516cbf63f885e036d37e9a59d27d63f46af1d4d07ee0f76181c7d3') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
RUN php composer-setup.php
RUN php -r "unlink('composer-setup.php');"
RUN mv composer.phar /usr/local/bin/composer

# FFMGEG
WORKDIR /tmp/ffmpeg_sources

RUN curl -O -L https://www.nasm.us/pub/nasm/releasebuilds/2.14.02/nasm-2.14.02.tar.gz
RUN tar zxvf nasm-2.14.02.tar.gz
WORKDIR nasm-2.14.02
RUN ls -l
RUN ./autogen.sh
RUN ./configure --prefix="$HOME/ffmpeg_build" --bindir="$HOME/bin"
RUN make
RUN make install
RUN ln -s /tmp/ffmpeg_sources/nasm-2.14.02/nasm /usr/bin/nasm

WORKDIR /tmp/ffmpeg_sources
# Yasm

RUN curl -O -L https://www.tortall.net/projects/yasm/releases/yasm-1.3.0.tar.gz
RUN tar xzvf yasm-1.3.0.tar.gz
WORKDIR yasm-1.3.0
RUN ./configure --prefix="$HOME/ffmpeg_build" --bindir="$HOME/bin"
RUN make
RUN make install
RUN ln -s /tmp/ffmpeg_sources/yasm-1.3.0/yasm /usr/bin/yasm

WORKDIR /tmp/ffmpeg_sources
# libx264

# Found no assembler Minimum version is nasm-2.13
RUN git clone --depth 1 https://code.videolan.org/videolan/x264.git
WORKDIR x264
RUN PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" ./configure --prefix="$HOME/ffmpeg_build" --bindir="$HOME/bin" --enable-static --disable-asm
RUN make
RUN make install

WORKDIR /tmp/ffmpeg_sources
# libx265

RUN yum install -y hg
RUN git clone https://bitbucket.org/multicoreware/x265_git.git
WORKDIR x265_git/build/linux/
RUN cmake -G "Unix Makefiles" -DCMAKE_INSTALL_PREFIX="$HOME/ffmpeg_build" -DENABLE_SHARED:bool=off ../../source
RUN make
RUN make install
RUN ln -s /tmp/ffmpeg_sources/x265_git/build/linux/x265 /usr/bin/x265

WORKDIR /tmp/ffmpeg_sources
# libfdk_aac

RUN git clone --depth 1 https://github.com/mstorsjo/fdk-aac
WORKDIR fdk-aac
RUN autoreconf -fiv
RUN ./configure --prefix="$HOME/ffmpeg_build" --disable-shared
RUN make
RUN make install

WORKDIR /tmp/ffmpeg_sources
# libmp3lame

RUN curl -O -L https://downloads.sourceforge.net/project/lame/lame/3.100/lame-3.100.tar.gz
RUN tar xzvf lame-3.100.tar.gz
WORKDIR lame-3.100
RUN ./configure --prefix="$HOME/ffmpeg_build" --bindir="$HOME/bin" --disable-shared --enable-nasm
RUN make
RUN make install

WORKDIR /tmp/ffmpeg_sources
# libopus

RUN curl -O -L https://archive.mozilla.org/pub/opus/opus-1.3.1.tar.gz
RUN tar xzvf opus-1.3.1.tar.gz
WORKDIR opus-1.3.1
RUN ./configure --prefix="$HOME/ffmpeg_build" --disable-shared
RUN make
RUN make install

WORKDIR /tmp/ffmpeg_sources
# libvpx

RUN git clone --depth 1 https://chromium.googlesource.com/webm/libvpx.git
WORKDIR libvpx
RUN ./configure --prefix="$HOME/ffmpeg_build" --disable-examples --disable-unit-tests --enable-vp9-highbitdepth --as=yasm
RUN make
RUN make install

WORKDIR /tmp/ffmpeg_sources
# FFmpeg

RUN curl -O -L https://ffmpeg.org/releases/ffmpeg-4.2.tar.gz
RUN tar -xzvf ffmpeg-4.2.tar.gz
WORKDIR ffmpeg-4.2
RUN PATH="$HOME/bin:$PATH" PKG_CONFIG_PATH="$HOME/ffmpeg_build/lib/pkgconfig" ./configure \
      --prefix="$HOME/ffmpeg_build" \
      --pkg-config-flags="--static" \
      --extra-cflags="-I$HOME/ffmpeg_build/include" \
      --extra-ldflags="-L$HOME/ffmpeg_build/lib" \
      --extra-libs=-lpthread \
      --extra-libs=-lm \
      --bindir="$HOME/bin" \
      --enable-gpl \
      --enable-libfdk_aac \
      --enable-libfreetype \
      --enable-libmp3lame \
      --enable-libopus \
      --enable-libvpx \
      --enable-libx264 \
      --enable-libx265 \
      --enable-nonfree
RUN make
RUN ln -s /tmp/ffmpeg_sources/ffmpeg-4.2/ffmpeg /usr/bin/ffmpeg
RUN ln -s /tmp/ffmpeg_sources/ffmpeg-4.2/ffprobe /usr/bin/ffprobe
RUN hash -d ffmpeg

WORKDIR /tmp/imageMagick
# ImageMagick

RUN yum install -y libtool-ltdl-devel fftw3-devel djvulibre-devel perl-devel

RUN curl -sfLO https://www.imagemagick.org/download/ImageMagick.tar.gz
#RUN echo 'cf51a1c6ebf627c627a8e6ac20aecce5f1425907c2cdb98c5a60f329c5c6caf2  ImageMagick-7.0.10-23.tar.gz' | sha256sum -c
RUN mkdir -p ImageMagick && tar -xzf ImageMagick.tar.gz -C ImageMagick --strip-components=1
WORKDIR ImageMagick
RUN ./configure --prefix=/usr/local --sysconfdir=/etc --enable-hdri --with-modules --with-perl --disable-static
RUN make install

WORKDIR /tmp/imageMagickModule
# imageMagickModule

RUN wget https://pecl.php.net/get/imagick
RUN mkdir -p imagick-module && tar zxvf imagick -C imagick-module --strip-components=1
WORKDIR imagick-module
RUN phpize
RUN ./configure
RUN make
RUN make install

WORKDIR /var/www/application

VOLUME ["/usr/local/php/etc"]
ENTRYPOINT ["/usr/local/php/sbin/php-fpm"]
CMD ["-F"]

