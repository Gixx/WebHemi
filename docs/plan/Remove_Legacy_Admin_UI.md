# Remove legacy admin UI

> **Status:** **done** (2026-08-08) — was [`CURRENT.md`](./CURRENT.md) Phase 1 (formerly Product Integration Phase 7 UI half).  
> **Language:** English only (all hub plans and review docs).

---

## Context

The live admin surface is already only `AdminDesktop` + `LoginPage`. PHP `/admin/sites` and `/admin/hosts` redirected to the dashboard; orphan Twig + React legacy pages were dead code still exported into the bundle. Review findings P0-1 / P1-7 / P2-1 are resolved by this deletion (not by CSRF patches on legacy forms).

---

## What was removed

### PHP (`webhemi-php`)

- Deleted: `templates/admin/sites.html.twig`, `hosts.html.twig`.
- Kept redirect routes on `AdminController` (`admin_sites` / `admin_hosts` / `admin_hosts_verify`) for bookmarks.
- Live AssetMapper controllers: `AdminDesktop.js`, `LoginPage.js` only.

### UI package (`webhemi-ui`)

| Group | Removed |
|-------|---------|
| Pages | `SitesPage`, `HostsPage`, `AdminDashboard` |
| Layout chrome | `AdminLayout`, `Sidebar`, `TopBar`, `PageHeader` |
| Legacy widgets | `FlashList`, `DataTable`, `Pagination`, Twig-era `Modal` |
| List views | `SiteListView`, `SiteHostListView`, `UserListView`, `RoleListView` |

**Kept:** `AdminDesktop`, `LoginPage`/`LoginForm`, Control Panel, Sites/Hosts windows, chrome atoms, bricks, `/admin/api` client, shell, Retro `DesktopModal` / `FloatingModal`.

**Deferred:** full `shared/` → `themes/default` relocation (transitional leftovers remain for Default theme stories).

Changelog: [`webhemi-ui/CHANGELOG.md`](../../webhemi-ui/CHANGELOG.md).

---

## Acceptance criteria

- [x] `/admin` = AdminDesktop; `/admin/sites` and `/admin/hosts` redirect to dashboard.
- [x] No `SitesPage` / `HostsPage` / `AdminLayout` export from the package.
- [x] Typecheck + library build green; synced PHP bundle without legacy symbols.
- [x] Review annotated: follow-ups target desktop P1s, not legacy CSRF.
