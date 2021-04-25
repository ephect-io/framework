#!/bin/sh
if [ ! -d ./cache ] || [ ! -d ./runtime ] || [ ! -d ./static ];
then
    echo "Be sure you are at the root of your Ephect site."
    exit 1;
fi

sudo find ./cache -type d -exec chmod 775 {} \;
sudo find ./cache -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode ./cache;

sudo find ./runtime -type d -exec chmod 775 {} \;
sudo find ./runtime -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode ./runtime;
