#!/bin/sh

REBORN_BIN=`pwd`/Bin

cd $REBORN_BIN

echo "----- Run Server --------------------------------------"
./bin/program \
	-m server \
	-p 8080 \
	-v "Server.DefaultDocument=index.php"
