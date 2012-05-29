#!/bin/sh

sudo apt-get install git-core cmake g++ libboost-dev libmysqlclient-dev libxml2-dev libmcrypt-dev libicu-dev openssl build-essential binutils-dev libcap-dev libgd2-xpm-dev zlib1g-dev libtbb-dev libonig-dev libpcre3-dev autoconf libtool libcurl4-openssl-dev libboost-system-dev libboost-program-options-dev libboost-filesystem-dev wget memcached libreadline-dev libncurses-dev libmemcached-dev libbz2-dev libc-client2007e-dev php5-mcrypt php5-imagick libgoogle-perftools-dev libcloog-ppl0

mkdir HphpDev
cd HphpDev

# Init Env
git clone git://github.com/facebook/hiphop-php.git
cd hiphop-php
export CMAKE_PREFIX_PATH=`/bin/pwd`/../
export HPHP_HOME=`/bin/pwd`
export HPHP_LIB=`/bin/pwd`/bin
cd ..

# Build libevent
wget http://www.monkey.org/~provos/libevent-1.4.14b-stable.tar.gz
tar -xzvf libevent-1.4.14b-stable.tar.gz
cd libevent-1.4.14b-stable
cp ../hiphop-php/src/third_party/libevent-1.4.14.fb-changes.diff .
patch -p1 < libevent-1.4.14.fb-changes.diff
./configure --prefix=$CMAKE_PREFIX_PATH
make
make install
cd ..

# Build curl
wget http://curl.haxx.se/download/curl-7.21.2.tar.gz
tar -xvzf curl-7.21.2.tar.gz
cd curl-7.21.2
cp ../hiphop-php/src/third_party/libcurl.fb-changes.diff .
patch -p1 < libcurl.fb-changes.diff
./configure --prefix=$CMAKE_PREFIX_PATH

rm $CMAKE_PREFIX_PATH/curl-7.21.2/lib/ssluse.c
wget https://raw.github.com/bagder/curl/26b487a5d6ed9da5bc8e4a134a88d3125884b852/lib/ssluse.c -O $CMAKE_PREFIX_PATH/curl-7.21.2/lib/ssluse.c
rm $HPHP_HOME/src/runtime/ext/extension.cpp
wget https://raw.github.com/h4ck3rm1k3/hiphop-php/0628599b4b03dff6b33bc2ea31de09f236ea6452/src/runtime/ext/extension.cpp -O $HPHP_HOME/src/runtime/ext/extension.cpp

make
make install
cd ..

# Build libmemcached
wget http://launchpad.net/libmemcached/1.0/0.49/+download/libmemcached-0.49.tar.gz
tar -xzvf libmemcached-0.49.tar.gz
cd libmemcached-0.49
./configure --prefix=$CMAKE_PREFIX_PATH
make
make install
cd ..

# Build HipHop-PHP
cd hiphop-php
git submodule init
git submodule update
cmake .
make
