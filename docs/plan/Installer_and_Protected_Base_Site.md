# Installer, protected base site/host, and path-based admin

> **Horizon:** phases 9–13 in [`CURRENT.md`](./CURRENT.md). This file holds design detail.

## Intent

Ship a **first-run installer** (WordPress-style `setup.php` / wizard) so a fresh WebHemi.PHP zip can become a runnable CMS without CLI seed:

1. Operator opens the installer URL (only when not yet installed).
2. Collects **language**, **DB host + credentials**, **primary domain**, and other bootstrap settings.
3. Writes config / runs migrations / creates the **base tenant**.
4. Leaves a usable **admin** plus a minimal **“Hello world”** public site.

After install, the product default is one **main site** and one **main domain**, with admin and frontend sharing that host via a **path prefix**.

## Target end state (example)

| Concept | Example |
|---------|---------|
| Base site | Name `Main site`, slug `main` |
| Base host | `www.gaborivan.de` (operator-chosen domain) |
| Public site | `https://www.gaborivan.de/` |
| Admin | `https://www.gaborivan.de/admin` (and `/admin/api`, …) |

Later the operator may add more sites/hosts (ownership verify → assign → active). The installer-created pair remains the **system base**.

## Protected base site and host

The Site and Host created by the installer must be **deletion- and deactivation-protected**:

- **Cannot delete** the base site or base host (API + UI: action disabled / `409` or equivalent).
- **Cannot deactivate** them (`is_active=false` / site `enabled=false` blocked for the base pair).
- Optional later: also block **unassign** of the base host from the base site, and block **slug/host rename** if that would break the install identity.
- Marking mechanism (decide at implementation): e.g. `is_system` / `is_protected` flags, or fixed slug `main` + env `PRIMARY_HOST` — prefer an explicit DB flag so rename rules stay clear.

Dev `app:seed` can keep creating local hosts for convenience, but production install path should create the protected pair the same way the installer does.

## Path-based admin (heritage)

### Old monolith (supported)

The pre-hub app treated **`/admin` on the site host as the canonical admin surface**:

- On a host with `surface=site`, a request whose path matched the admin prefix (default `/admin`) **overrode** the resolved surface to `admin`.
- A host with `surface=admin` (e.g. `admin.mysite.local`) **redirected** to the site’s canonical host + `/admin…` (alias → path).
- Prefix was injectable (`adminPathPrefix`, default `/admin`); similar paths like `/administrator` did not match.
- Docs/UI copy stated that the admin surface is always available via the canonical `/admin` path.

Reference (local archive): `.old/src/Routing/Request/HostContextSubscriber.php` and its unit tests (`resolvesAdminSurfaceForAdminPathOnSiteHost`, admin-alias redirect cases).

### Current hub (`webhemi-php`) — **not carried over**

Today’s [`HostContextSubscriber`](../../webhemi-php/src/Routing/HostContextSubscriber.php) only resolves by **hostname** → `SiteHost` → `HostContext`. There is:

- no path → admin surface override,
- no admin-host → canonical site `/admin` redirect,
- no configurable admin path prefix in routing.

Symfony routes and the firewall still use the **`/admin` URL prefix**, and local seed still creates a separate **`admin.*.local`** host with `surface=admin`. That is **host-based** admin entry, not the old path-canonical model.

**Implication for the installer vision:** restore (or re-spec) path-based admin as a **prerequisite or first slice** of this feature family — otherwise “one domain, `/` = site, `/admin` = admin” is incomplete relative to product intent and old behavior.

## Suggested work packages (when we get there)

Order is indicative; refine when scheduling.

| # | Slice | Notes |
|---|--------|--------|
| P0 | **Path-based admin restore** | Port old subscriber rules (or equivalent) into current `HostContext` / holder; tests; decide fate of dedicated `admin.*` hosts (alias redirect vs optional second entry). |
| P1 | **Protected base flags** | Schema + API guards + Sites/Hosts UI (no Delete / no deactivate for protected rows). |
| P2 | **Installer wizard** | Web UI when `APP_INSTALLED` (or similar) is false: locale, DB DSN, primary domain, admin user; migrations; create protected main site + host; seed Hello-world page; lock installer afterward. |
| P3 | **Hello world site** | Minimal public theme page on `/` for the base site so install feels complete. |
| P4 | **Packaging** | Zip/release story from Architecture Phase 5; document that Node is not required in production. |

## Out of scope for this note

- Implementing the installer now.
- Changing current Admin98 Phase 6 Sites/Hosts CRUD beyond documenting future constraints.
- Multi-engine (`webhemi-js`) install — PHP engine first.

## Open decisions

- Exact protection flag name and whether slug `main` is reserved forever.
- Whether a separate `admin.` hostname remains supported as redirect-only alias after restore.
- Installer language: PHP Twig wizard vs small React surface under a public `/install` route.
- How install interacts with existing host-ownership probe (base host may be trusted/pre-verified at install time).
