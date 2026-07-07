# Changelog

## v1.8

- Added downloadable CSV and print/PDF-ready summaries for every equipment booking.
- Added a dedicated equipment booking detail page with requested, approved, issued, returned and outstanding quantities.
- Added controlled **book out** and **book back in** actions for Admin, GSL, Chairperson and QM.
- Every issue and return now records a permanent custody-history entry: item, booking, quantity, responsible person, condition, timestamp and the user who completed the handover.
- Equipment item pages now show **Who has had this item** and link directly to the relevant booking history.
- Approving a booking now records the approved quantity for every requested item.
- Removed the **Booking type** field from Hut booking requests. All new records retain the internal default `Hut booking` value for backward compatibility.
