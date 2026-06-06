#!/bin/bash
# deploy_update_xml.sh
# Wordt via SCP naar de server gekopieerd en daar uitgevoerd.
# Vereiste omgevingsvariabelen (meegegeven via appleboy/ssh-action envs):
#   VERSION           bijv. 0.0.1
#   ZIP_NAME          bijv. mod_amu_ipinfo_v0.0.1.zip
#   UPDATES_PATH      bijv. /var/www/update.amultis.dev

set -e

if [ -z "$VERSION" ] || [ -z "$ZIP_NAME" ] || [ -z "$UPDATES_PATH" ]; then
    echo "ERROR: VERSION, ZIP_NAME en UPDATES_PATH moeten ingesteld zijn."
    exit 1
fi

DOWNLOAD_URL="https://download.amultis.dev/joomla/modules/amu_ipinfo/${ZIP_NAME}"
export VERSION
export DOWNLOAD_URL
export OUTPUT_PATH="${UPDATES_PATH}/joomla/modules/amu_ipinfo/update.xml"

mkdir -p "$(dirname "$OUTPUT_PATH")"

python3 /tmp/amu_deploy/generate_update_xml.py

echo "Klaar: update.xml geschreven naar ${OUTPUT_PATH}"
