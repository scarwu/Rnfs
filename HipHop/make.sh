#!/bin/sh

REBORN_BIN=`pwd`/Bin
REBORN_SOURCE=`pwd`/Source
REBORN_HPHP=`pwd`/HphpDev

mkdir $REBORN_BIN
rm -rf $REBORN_BIN/*

export HPHP_HOME=$REBORN_HPHP/hiphop-php
export HPHP_LIB=$HPHP_HOME/bin
export CMAKE_PREFIX_PATH=$HPHP_HOME/../
export PATH=$PATH:$HPHP_HOME/src/hphp:$HPHP_HOME/src/hphpi

echo "----- Compiling PHP Code ------------------------------"
cd $REBORN_SOURCE
find . -name "*.php" > files.list
hphp \
	--input-list=files.list \
	--keep-tempdir=1 \
	--log=3 \
	--force=1 \
	--cluster-count=50 \
	--output-dir=$REBORN_BIN \
	--include-path="." \
	-v "AllDynamic=true" \
	-v "AllVolatile=true" 
