#!/usr/bin/env sh
MODULES_PATH=vendor/ephect-io/fullmoon/Ephect/Modules
LIST=$(ls -A $MODULES_PATH)

#find $MODULES_PATH -maxdepth 1 -type d -exec php use install:module "{}" $1 $2 \;
for i in $LIST;
do
   php use install:module "$MODULES_PATH/$i" $1 $2
done;
