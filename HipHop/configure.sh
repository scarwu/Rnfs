#!/bin/sh

REBORN_SOURCE=`pwd`/Source
REBORN_PATCH=`pwd`/Patch

rm -rf $REBORN_SOURCE

git clone git://github.com/scarwu/Reborn.git $REBORN_SOURCE
git clone git://github.com/scarwu/CLx.git $REBORN_SOURCE/Core

rm -rf $REBORN_SOURCE/.git
rm -rf $REBORN_SOURCE/Core/.git
rm -rf $REBORN_SOURCE/Boot
rm -rf $REBORN_SOURCE/HipHop
cp $REBORN_PATCH/index.php $REBORN_SOURCE

