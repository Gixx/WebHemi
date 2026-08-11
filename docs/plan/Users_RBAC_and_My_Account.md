# Users RBAC + My Account

> **Status:** done.  
> **Parent:** [CURRENT.md](./CURRENT.md) · [Users_Window.md](./Users_Window.md) · [RBAC_Reset.md](./RBAC_Reset.md).  
> **Language:** English only.

## Decision (Start menu)

**Separate Start → My Account** (not the Users management window).

- Users stays under Control Panel (management UI; every admin session may open it, list filtered).
- Start → **My Account** opens a slim self-only surface: Set Password (3 fields) now; later public profile fields under `user.view` / self.
- Avoids Site Admins / limited operators seeing a one-row “Users” manager from Start.

## Permission model

Codes (singular): `user.list` | `user.view` | `user.create` | `user.edit` | `user.delete`.

| Action | Who |
|--------|-----|
| Open Users (CP) | Any authenticated admin session (`ROLE_USER`) |
| See self in list (first) | Always |
| See other users | `ROLE_ADMIN` or `user.list` |
| View other (Change Settings read-only) | `ROLE_ADMIN` or `user.view` |
| Create | `ROLE_ADMIN` or `user.create` |
| Edit other | `ROLE_ADMIN` or `user.edit` |
| Delete other | `ROLE_ADMIN` or `user.delete` (keep self-delete + last-admin locks) |
| Set password **self** | Always (3 fields: old + new + confirm) |
| Set password **other** | `ROLE_ADMIN` or `user.edit` (2 fields: new + confirm) |

Edit **self** (roles / site assignments): needs `user.edit` or Admin. Self may change password via My Account / Set Password without editing global roles. v1: Change Settings on self requires `user.edit`|Admin; password always allowed on self.

## Implementation notes

1. **`user.` stays in `ADMIN_ONLY_PREFIXES`** — Site Admin must not auto-gain CP user actions via `isSiteInterior`. Custom roles already grant via `Role::hasPermission`.
2. **PermissionVoter** also checks **global** custom roles (`user_role`) for catalog codes (not only site assignments).
3. **`UserAccess`** + filtered `GET /users`, grant matrix on mutate/show/password, extended `GET /me` (`id`, `email`, `capabilities`).
4. **UI:** capabilities gate UsersWindow; Set Password `self` vs `other`; Start → My Account.

## Out of scope

- Defining “public” profile fields for `user.view`
- Full My Account profile editor
- Seeding default `user.*` catalog rows (operators create as today)
