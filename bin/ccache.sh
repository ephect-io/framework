#!/bin/sh

pwd

if [ ! -d ./cache ] || [ ! -d ./runtime ];
then
    echo "Be sure you are at the root of your Ephect project."
    exit 1;
fi

sudo find ./runtime -type d -exec chmod 775 {} \;
sudo find ./runtime -type f -exec chmod 664 {} \;

sudo find ./cache -type d -exec chmod 775 {} \;
sudo find ./cache -type f -exec chmod 664 {} \;

sudo rm -rf cache
sudo rm -rf runtime

rm php_errors.log
rm src/public/php_errors.log

echo Cache cleared
echo

exit 0