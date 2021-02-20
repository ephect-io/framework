#!/bin/sh
if [ -d /Sites/ephect ];
then
    cd /Sites/ephect;
fi
if [ -d /Users/david/Sites/CodePhoenixOrg/SDK/php/ephect ];
then
    cd /Users/david/Sites/CodePhoenixOrg/SDK/php/ephect;
fi
sudo find ./cache -type d -exec chmod 775 {} \;
sudo find ./cache -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode ./cache;
