# Migration safety policy

Golden compatibility baseline: ab233f8e83150a1ea7f4c3f09de187f782edfa70

Rules for this repository:

- Never rewrite Git history or force-push migration work.
- Never drop or rename database columns/tables without an additive migration and rollback path.
- Preserve existing public URLs and form field names until compatibility is proven.
- Preserve authentication/session behavior until dedicated migration tests exist.
- Prefer adapters and compatibility layers over destructive replacement.
- Keep each migration small enough to roll back with a normal revert commit.
- Performance improvements must not change visible business behavior.
- Old code is removed only after its replacement is tested and verified in production.
