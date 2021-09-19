#!/bin/sh
if [ ! -d ./cache ] || [ ! -d ./runtime ];
then
    echo "Be sure you are at the root of your Ephect project."
    exit 1;
fi

sudo find . -type d -exec chmod 775 {} \;
sudo find . -type f -exec chmod 664 {} \;

exit 0