#!/usr/bin/env bash
set -Eeuo pipefail

echo "GitHub update is disabled for this installation."
echo "The current GitHub main branch contains an invalid composer.json and does not contain the latest release code."
echo "Use the deploy-update.sh script from a downloaded release package instead."
exit 2
