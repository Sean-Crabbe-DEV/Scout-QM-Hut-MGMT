# v1.14 recovery update

The GitHub `main` branch is not currently safe to use for application updates because its `composer.json` was overwritten with PHP source code. The normal `update.sh` script performs `git reset --hard origin/main`, which restores that broken file and then stops at Composer.

Use `deploy-update.sh` from this downloaded package instead:

```bash
cd /root
rm -rf scout-hut-mgmt-v1.14
unzip -o Scout-QM-Hut-MGMT-v1.14.zip -d /root
sudo bash /root/scout-hut-mgmt-v1.14/deploy-update.sh /root/scout-hut-mgmt-v1.14
```

This keeps `.env`, the database, uploads, logs and backups. It also intentionally skips Composer, because the current release uses its built-in PDF renderer.

Do not run `/var/www/scout-hut-mgmt/update.sh` until the GitHub branch is repaired and contains the release source.
