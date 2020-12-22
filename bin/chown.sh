#!/bin/sh
cd /Sites/funcom
sudo find ./cache -type d -exec chmod 775 {} \;
sudo find ./cache -type f -exec chmod 664 {} \;
sudo chown -R vscode:vscode ./cache
