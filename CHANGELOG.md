# Changelog

## v1.4 — Navigation and interface refresh

- Made the desktop left navigation Scouts Red and added active-page highlighting plus logical navigation groups.
- Restored Scout Purple to the Open tickets dashboard metric.
- Removed the emergency/immediate-danger notice from the public report form.
- Refreshed cards, forms, tables, ticket tabs, buttons, metric cards and the fixed header for a more modern, easier-to-use interface.
- Kept the mobile navigation responsive and horizontally scrollable.

## v1.3

- Redesigned the public/internal issue-reporting form with Hut/Equipment choice cards.
- Conditional category lists: Hut categories are separate from Equipment categories.
- Conditional fields: Hut reports show location details; Equipment reports show the equipment selector.
- Replaced the purple interface treatment with Scouts Red.
- Fixed the header at the top of the page.
- Rebuilt Ticket page tabs as visual Hut / Equipment switcher cards.
- Renamed Bookings to Hut bookings across the user interface.
- Added the bulk equipment booking builder with selected-item checkboxes, quantities and optional linked hut booking.
- Restricted External User accounts to hut availability, their own hut booking requests and issue reporting.
- Added an `deploy-update.sh` helper for updating an existing non-Git installation while retaining `.env`, uploads and backups.

## v1.5
- Tickets moved directly below Dashboard in the internal navigation.
- Hut-area add/edit configuration moved into System settings.
- Ticket reports and ticket updates now accept multiple attachments.
- Equipment add/update forms accept multiple supporting files.
- Added role-restricted Equipment Update action for Admin, GSL, Chairperson and QM.
- Equipment status options simplified to Available, Booked, Damaged, In repair and Disposed of.
- Equipment updates can record a maintenance entry and costs in the same save action.
