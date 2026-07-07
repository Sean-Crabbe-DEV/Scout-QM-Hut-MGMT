# Scout Hut Management System

A self-hosted management system for Scout hut bookings, reported issues, maintenance, equipment, loans and inspections.

## Main features

- Public, mobile-friendly problem reporting form with photo uploads.
- Ticketing system for hut and equipment faults.
- Separate Hut database for rooms, fixed facilities and maintenance history.
- Separate Equipment database with photos, condition, purchase details, bookings and repair history.
- Equipment approvals restricted to **GSL, Chairperson, QM and Admins**.
- Ticket updates restricted to **GSL, Chairperson, QM, Admins and users assigned to the ticket**.
- Booking calendar and room request workflow.
- Email notifications and delivery history.
- First-run setup: the first account created becomes the sole initial Admin.
- User, group and role management for Admins.
- Scouts Wales-ready visual styling; add your approved red Group logo after install.

## Install on an Ubuntu CT

> Tested design target: Ubuntu 22.04 / 24.04 LTS, Nginx, PHP-FPM and MariaDB.

```bash
git clone https://github.com/Sean-Crabbe-DEV/Scout-QM-Hut-MGMT.git
cd Scout-QM-Hut-MGMT
sudo bash install.sh --domain hut.example.org
```

The installer creates the database, installs PHP/Nginx/MariaDB dependencies, writes `.env`, installs Composer packages, runs migrations, creates the virtual host and schedules daily reminders.

Open the configured URL and use **Set up first admin**. This option disappears as soon as the first account is created.

## Update safely

```bash
cd /var/www/scout-hut-mgmt
sudo bash update.sh
```

`update.sh` makes a dated database and configuration backup before pulling `main`, installing dependencies and running the migration script.

## Branding

Save the official, approved Wales red Group logo as one of the following:

```text
public/assets/brand/group-logo-red.svg
public/assets/brand/group-logo-red.png
```

Do not recreate or edit the Scout logo. Download the approved personalised artwork from the Scouts Brand Centre.

## Configuration

Admins can configure SMTP mail settings in **Admin → System settings**. The SMTP password is encrypted using `APP_KEY`; leave it blank when editing to keep the existing value.

## Backups

- Database backups created by `update.sh`: `storage/backups/`
- Daily reminder task: `/etc/cron.d/scout-hut-mgmt`
- You should additionally back up `/var/www/scout-hut-mgmt/.env`, `storage/uploads/` and the MariaDB database off-site.

## Important

This system stores operational and contact information. Keep it patched, use HTTPS, give people the minimum roles they need, and do not upload sensitive young-person data unless you have an approved data-protection process for doing so.
