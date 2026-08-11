# Roles window

> **Status:** done (Phase 5).  
> **Parent:** [CURRENT.md](./CURRENT.md) · [RBAC_Reset.md](./RBAC_Reset.md) R4.  
> **Language:** English only.

## Product rules (Roles v1)

| Rule | Detail |
|------|--------|
| Fields | `name` (unique `ROLE_*` code, uppercased), `label`, `description`, attached `permissionIds` |
| CRUD | Full create / edit / delete for **custom** roles |
| Locks | **Admin** (`ROLE_ADMIN`) and **Site Admin** (`ROLE_SITE_ADMIN`): no edit, no delete (`protected` / `is_read_only`) |
| Seed | Those two system roles only; operators add custom roles for testing |
| Auth | Admin-only: `role.list` / `role.edit` |
| Delete | Refuse (`409`) if role still assigned to users; refuse if protected |
| Permissions | Create/update may set full `permissionIds` list (replace sync) |

Name validation: non-empty, max 64, pattern `ROLE_[A-Z0-9_]+` (entity uppercases). Reserved system names cannot be created as custom rows.

## Architecture

Mirror [Permissions_Window.md](./Permissions_Window.md): PHP JSON API → client → `RolesWindow` → shell / CP / deep link / MSW.

## API

- `GET/POST /admin/api/roles`, `GET/PATCH/DELETE /admin/api/roles/{id}`
- Mapper: `{ id, name, label, description, protected, permissionIds, permissionCount }`
- 409: `name_taken`, `role_protected`, `users_assigned`
- CSRF on mutations

## UI

- Table: Name, Label, Permissions (count), Protected
- Form: General (name, label, description) + Permissions tab — assigned table, Assign dropdown, Remove / Add… (same pattern as Sites → Hosts)
- Protected rows: Edit/Delete disabled; double-click no-op
- Deep link: `?window=roles` / `?window=roles&id=`

## Follow-on

**Phase 6 — Users:** global roles + `site_assignment`. Detail: [Users_Window.md](./Users_Window.md).
