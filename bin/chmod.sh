#!/bin/sh
cd /Sites/funcom/framework
sudo find . -type d -exec chmod 775 {} \;
sudo find . -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode .
