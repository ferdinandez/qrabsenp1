# Performance Fix - Email Disabled

Email notification temporarily disabled in UserController.php to fix slow user creation.

User creation now should be instant (< 2 seconds).

## Changes:
- Disabled email sending in `store()` method
- Added TODO comment for queue worker implementation

## Deployed: 2026-07-29
