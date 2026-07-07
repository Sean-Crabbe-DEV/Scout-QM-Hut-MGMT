# Scout Hut Management System

A self-hosted PHP/MariaDB management system for Scout hut bookings, maintenance tickets, equipment, inspections and asset history.

## v1.2 included changes

- **Hut and Equipment ticket tabs** with separate `HUT-` and `EQP-` references.
- A ticket begins by choosing **Hut** or **Equipment**. A free-text Hut location detail appears only for Hut tickets; Equipment tickets show only equipment selection.
- **Whole Site** hut-booking option. When approved or confirmed, it reserves every currently enabled bookable area, surfaces in each affected area’s booking history, and blocks conflicting approvals.
- Hut areas no longer have a manual **Current Condition** field. Equipment condition tracking is unchanged.
- **Group name** and Whole Site availability are configurable in **System settings**.
- System settings show and copy the public fault-report link: `/report-problem`.
- Footer simplified to the configured Group name and `Hut Management System`.
- A cleaned settings layout, ticket tabs and clearer mobile controls.
- The supplied Scouts logo SVG is included in a clean, red version without the copied margin/animation styles. `scripts/normalise-logo-svg.php` is also included for future approved logo replacements.
- Installer now preserves the existing database password if it is run again, avoiding the MariaDB / `.env` mismatch that caused the earlier login error.

## Core permission rules

- **Admins** can manage all users, groups/roles, system settings and all operational records.
- **Only Admin, GSL, Chairperson and QM** can approve or decline equipment bookings.
- **Only Admin, GSL, Chairperson, QM and users assigned to a ticket** can add ticket updates or maintenance cost records.
- Only Admin, GSL, Chairperson and QM can finalise ticket closure/cancellation or change ticket assignments.

## Fresh installation: Ubuntu CT

Use the intended public URL:

```text
https://hut.1stsedburytidenhamscouts.org.uk
```

Once the repository contains this release:

```bash
git clone https://github.com/Sean-Crabbe-DEV/Scout-QM-Hut-MGMT.git
cd Scout-QM-Hut-MGMT
sudo bash install.sh \
  --domain hut.1stsedburytidenhamscouts.org.uk \
  --app-url https://hut.1stsedburytidenhamscouts.org.uk
```

Do **not** add `--with-certbot` when the site is published through a Cloudflare Tunnel. Cloudflare provides the public certificate in that setup.

For direct public hosting with port 80 available to the CT, add `--with-certbot`.

The installer writes to `/var/www/scout-hut-mgmt`. The first account created at the website becomes the initial Admin and locks setup afterwards.

## Update

After the repository is published, use:

```bash
cd /var/www/scout-hut-mgmt
sudo bash update.sh
```

`update.sh` creates a dated MariaDB and `.env` backup before pulling `main`, installing PHP packages, and running the database migration.

## Logo

The fixed red logo supplied during this build is already included at:

```text
public/assets/brand/group-logo-red.svg
```

For a future approved replacement that carries the same broken copied page styles, run:

```bash
php scripts/normalise-logo-svg.php /path/to/original-logo.svg public/assets/brand/group-logo-red.svg
```

The normaliser keeps the vector artwork, removes copied page margins and changes the inherited outer fill to Scouts Wales red (`#ED3F23`).

## Operational safety

Keep `/var/www/scout-hut-mgmt/.env`, MariaDB and `storage/uploads/` in encrypted off-site backups. Use HTTPS, strong admin passwords and minimum necessary access. Do not store sensitive youth information unless there is an approved data-protection process for doing so.
