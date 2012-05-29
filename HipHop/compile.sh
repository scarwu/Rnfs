git clone git://github.com/scarwu/Reborn.git Source
git clone git://github.com/scarwu/CLx.git Source/Core

REBORN_BIN=`pwd`/Bin
REBORN_SRC=`pwd`/Source
rm -rf "$REBORN_BIN/*"
cd "$REBORN_SRC"

echo "----- Compiling PHP Code ------------------------------"
find . -name "*.php" > files.list
hphp \
	--input-list=files.list \
	--keep-tempdir=1 \
	--log=3 \
	--force=1 \
	--cluster-count=50 \
	--output-dir="$REBORN_BIN" \
	--include-path="." \
	-v "AllDynamic=true" \
	-v "AllVolatile=true" 
