# Scout Hut Management System

A self-hosted PHP/MariaDB management system for Scout hut bookings, maintenance tickets, equipment, inspections and asset history.

## v1.4 included changes

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


- **Redesigned public issue form**: choose Hut or Equipment first, then the Nature of Problem list changes to match. Hut location details only appear for Hut reports; equipment selection only appears for Equipment reports.
- The interface palette is now **Scouts Red** throughout; the purple navigation, tabs, focus states and secondary actions have been replaced.
- The header is fixed at the top of the viewport while content scrolls below it.
- **Ticket tabs redesigned** as two clear visual switcher cards for Hut tickets and Equipment tickets, each with a live count and plain-English context.
- **Bookings is now labelled Hut bookings** across the navigation and page headings.
- **Bulk equipment booking**: choose a list of equipment with checkboxes, set quantities, optionally link to a hut booking, then submit one event request for approval.
- External users are now restricted to a minimal account: hut availability, their own hut booking requests, and reporting issues. Equipment, ticket administration, maintenance, settings and all internal data are blocked.



### v1.4 visual and navigation update

- The left navigation is now **Scouts Red** (`#ED3F23`) with clearer active-page styling and grouped links.
- The **Open tickets** dashboard card is Scout Purple again, while urgent issues remain red.
- Removed the emergency/immediate-danger warning from the public issue-report page.
- Refreshed the overall interface with softer page backgrounds, larger cards, improved form controls, clearer status surfaces, more readable tables, stronger spacing and mobile-friendly navigation.
- The fixed header remains in place while content scrolls below it.

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

### Updating a current CT before GitHub is populated

This release also includes `deploy-update.sh`, which keeps the existing `.env`, uploaded files and backups. Extract the downloaded v1.4 ZIP, then run:

```bash
cd /path/to/scout-hut-mgmt-v1.4
sudo bash deploy-update.sh /path/to/scout-hut-mgmt-v1.4
```

Use this instead of re-running `install.sh`.

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
