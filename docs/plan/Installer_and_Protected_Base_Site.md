# Installer, protected base site/host, and path-based admin

> **Horizon:** installer / packaging phases in [`CURRENT.md`](./CURRENT.md).  
> **Access routing:** canonical rules are in [`Admin_API_Access_Mode.md`](./Admin_API_Access_Mode.md) (supersedes older “always admin.* / undecided api host” notes below).

## Intent

Ship a **first-run installer** (WordPress-style wizard) so a fresh WebHemi.PHP zip can become a runnable CMS without CLI seed:

1. Operator opens the installer URL (only when not yet installed).
2. Collects **language**, **DB host + credentials**, **primary domain**, and other bootstrap settings.
3. Writes config / runs migrations / creates the **base tenant**.
4. Leaves a usable **admin** plus a minimal **“Hello world”** public site.

After install, the product default is one **main site** and one **main domain**, with admin on that host via **`/admin`** (`webhemi.access.admin=path`). A dedicated `admin.*` host is **optional** later (Settings → domain mode).

## Target end state (example)

| Concept | Example |
|---------|---------|
| Base site | Name `Main site`, slug `main` (protected) |
| Base host | `www.example.com` (operator-chosen; protected site surface) |
| Public site | `https://www.example.com/` |
| Admin (default) | `https://www.example.com/admin` (+ `/admin/api`) |
| Public API | `https://www.example.com/api` (and every other site host’s `/api`) |
| Optional later | `admin.example.com` + `access.admin=domain` |

There is **no** required `api.*` host and **no** `api` host surface.

## Protected base site and host

The Site and **primary site host** created by the installer must be **deletion- and deactivation-protected**:

- **Cannot delete** the base site or base site-host (API + UI: action disabled / `409` or equivalent).
- **Cannot deactivate** them.
- Optional later: also block **unassign** of the base host from the base site, and block **slug/host rename** if that would break the install identity.
- Prefer an explicit DB flag (`is_system` / `is_protected`) so rename rules stay clear.

An **admin** surface host (if created) is **not** permanently protected; deleting it forces `access.admin` back to `path` ([Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md)).

Dev `app:seed` may still create `admin.webhemi.local` for local domain-mode testing; production install defaults to path-only until the operator adds an admin host and switches Settings.

## Path / domain admin (summary)

Full rules: [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md).

Heritage: the pre-hub app already treated `/admin` on the site host as canonical and could redirect `admin.*` → site `/admin`. Current hub `HostContextSubscriber` is hostname-only until the access-mode implementation lands.

## Suggested work packages (when we get there)

Order follows [`CURRENT.md`](./CURRENT.md); refine when scheduling.

| Slice | Notes |
|-------|--------|
| Access mode | Config file + routing/redirects + drop `api` surface — [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md) |
| Protected base flags | **Done** (API/UI) — [Protected_Main_Base_Guards.md](./Protected_Main_Base_Guards.md) |
| Installer wizard | **Deferred** (CURRENT Phase 15) — locale, DB, primary domain, admin user; migrations; protected main pair; path admin; lock installer |
| Hello world site | Minimal public theme on `/` |
| Packaging | Zip/release; Node not required in production |

## Out of scope for this note

- Implementing the installer now.
- Public API controller design beyond reserved `/api` path.
- Multi-engine (`webhemi-js`) install — PHP engine first.

## Open decisions (remaining)

- Exact protection flag name and whether slug `main` is reserved forever.
- Installer UI: PHP Twig wizard vs small React surface under `/install`.
- How install interacts with host-ownership probe (base site host may be trusted/pre-verified at install time).
