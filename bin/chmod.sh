#!/bin/sh
if [ -d /Sites/funcom ];
then
    cd /Site/funcom
fi
if [ -d /Users/david/Sites/CodePhoenixOrg/SDK/php/funcom ];
then
    cd /Users/david/Sites/CodePhoenixOrg/SDK/php/funcom
fi
cd framework

sudo find . -type d -exec chmod 775 {} \;
sudo find . -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode .
