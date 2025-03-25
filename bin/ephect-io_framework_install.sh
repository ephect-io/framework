#!/usr/bin/env sh

FILE_PATH=$(realpath -s $0);
CWD=$(dirname $FILE_PATH);
PARENT_DIR=$(dirname $CWD)
FRAMEWORK_DIR=$(head $PARENT_DIR/config/framework)

MODULES_PATH=$FRAMEWORK_DIR/Modules
LIST=$(ls -A $MODULES_PATH)

#find $MODULES_PATH -maxdepth 1 -type d -exec php use install:module "{}" $1 $2 \;
for i in $LIST;
do
   php use install:module "$MODULES_PATH/$i" $1 $2
done;
