# Changelog

## v1.11
- Added a real server-generated **Download PDF** export for equipment booking summaries.
- The export is an A4 portrait PDF with the Group logo, booking reference, event, status, booking user, printed date/time, collection and return details, equipment holder, item check boxes, Asset IDs, quantities, condition-out fields, notes and signatures.
- The existing browser-based print screen is now labelled **Print preview** to make the difference clear.

## v1.9
- Redesigned the printable equipment booking summary as a logo-branded handover sheet.
- Added Event, Status, Booking user, Printed on date/time, booking reference and collection/return dates.
- Added Asset ID, equipment item, quantity, status/condition out and a check-off box for every item.
- Added issue/return note areas and signature lines for a complete paper handover record.

v1.8

- Added downloadable CSV and print/PDF-ready summaries for every equipment booking.
- Added a dedicated equipment booking detail page with requested, approved, issued, returned and outstanding quantities.
- Added controlled **book out** and **book back in** actions for Admin, GSL, Chairperson and QM.
- Every issue and return now records a permanent custody-history entry: item, booking, quantity, responsible person, condition, timestamp and the user who completed the handover.
- Equipment item pages now show **Who has had this item** and link directly to the relevant booking history.
- Approving a booking now records the approved quantity for every requested item.
- Removed the **Booking type** field from Hut booking requests. All new records retain the internal default `Hut booking` value for backward compatibility.

## v1.10

- Changed the Equipment Booking Summary print layout to **A4 portrait**.
- Tightened table widths, typography and metadata layout so the booking handover checklist remains readable on a portrait page.
- Preserved the logo, booking information, tick boxes, issue/return notes and signature fields.
