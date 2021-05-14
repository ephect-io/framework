#!/bin/sh

pwd

if [ ! -d ./cache ] || [ ! -d ./runtime ];
then
    echo "Be sure you are at the root of your Ephect site."
    exit 1;
fi

sudo rm -rf cache
sudo rm -rf runtime
rm php_errors.log
rm src/public/php_errors.log
echo Cache cleared
echo
