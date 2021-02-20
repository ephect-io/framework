#!/bin/sh
if [ -d /Sites/ephect ];
then
    cd /Sites/ephect;
fi
if [ -d /Users/david/Sites/CodePhoenixOrg/SDK/php/ephect ];
then
    cd /Users/david/Sites/CodePhoenixOrg/SDK/php/ephect;
fi

sudo rm -rf cache
echo Cache cleared
echo
