#!/bin/sh
if [ ! -d ./cache ] || [ ! -d ./runtime ] || [ ! -d ./static ];
then
    echo "Be sure you are at the root of your Ephect site."
    exit 1;
fi

sudo find . -type d -exec chmod 775 {} \;
sudo find . -type f -exec chmod 664 {} \;
sudo chown -R www-data:www-data .
