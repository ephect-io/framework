#!/bin/sh
if [ -d /Sites/funcom ];
then
    cd /Site/funcom
fi
if [ -d /Users/david/Sites/CodePhoenixOrg/SDK/php/funcom ];
then
    cd /Users/david/Sites/CodePhoenixOrg/SDK/php/funcom
fi

sudo rm -rf cache
echo Cache cleared
echo
