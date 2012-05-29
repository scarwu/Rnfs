#!/bin/sh

REBORN_BIN=`pwd`/Bin

cd $REBORN_BIN

echo "----- Run Server --------------------------------------"
./program \
	-m server \
	-p 3000 \
	-v "Server.DefaultDocument=index.php"
