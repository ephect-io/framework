#!/bin/sh
if [ ! -d ./cache ] || [ ! -d ./runtime ];
then
    echo "Be sure you are at the root of your Ephect project."
    exit 1;
fi


#sudo chown -R vscode:vscode .
sudo chown -R www-data:www-data .
