# Changelog

## v1.13
- Refined the server-generated **A4 portrait Equipment Booking Summary PDF** for clearer paper handovers.
- Reduced unused white space in the heading and booking-details area.
- Rebuilt the booking details panel into a compact Event/Status row with Booking User, Collection From and Return By underneath; the print date now sits beside the booking reference.
- Improved the item checklist table with better column proportions, larger check boxes, wider Asset ID and Condition Out cells, clearer headings, and more consistent item-row spacing.
- Increased the size of the issue and return note areas, tightened the gap below the final table row, and aligned signature lines consistently.

## v1.12
- Replaced the equipment-booking PDF export dependency with a built-in PDF renderer.
- **Download PDF** now generates a real A4 portrait PDF even when Composer or the former Dompdf component is not installed.
- Added a packaged, print-ready group logo JPEG used by the generated PDF.
- Updated deployment so a Composer failure no longer prevents the system update or PDF export; SMTP email still needs Composer packages when configured.

## v1.11
- Added a real server-generated **Download PDF** export for equipment booking summaries.
- The export is an A4 portrait PDF with the Group logo, booking reference, event, status, booking user, printed date/time, collection and return details, equipment holder, item check boxes, Asset IDs, quantities, condition-out fields, notes and signatures.
- The existing browser-based print screen is now labelled **Print preview** to make the difference clear.

## v1.10
- Changed the Equipment Booking Summary print layout to **A4 portrait**.

## v1.9
- Redesigned the printable equipment booking summary as a logo-branded handover sheet.

## v1.8
- Added downloadable CSV and print/PDF-ready summaries for every equipment booking.
- Added controlled equipment issue/return with custody history.
- Removed the Booking type field from Hut booking requests.
