# Admin access mode, reserved paths, and dual auth

> **Status:** accepted design (2026-08-08) — implementation = [`CURRENT.md`](./CURRENT.md) phase (bring forward ahead of deep links / installer).  
> **Supersedes:** path-only notes in [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) that treated dedicated `admin.*` as always-on or left `api` surface undecided; **no `api` host surface**; **no public-API domain mode**.  
> **Language:** English only.

---

## Intent

Single-domain installs must work without subdomain DNS. Admin may optionally move to a dedicated Main Site host. Public frontend API is always path-based on every site host. Admin UI and frontend auth stay separate (sessions included).

---

## Locked decisions

### Surfaces (hosts)

| Surface | Allowed on | Meaning |
|---------|------------|---------|
| `site` | Any site | Public site + reserved frontend paths |
| `admin` | **Main site only** | Dedicated admin host (used when `adminAccess=domain`) |

- **`api` surface is removed** from product and from Host create/edit UI/API. Existing enum value is deleted in the implementation slice (migrate/reject rows if any).
- Non–main sites: surface fixed to `site` (control read-only in UI; API rejects `admin`).

### Access modes

| Key | Values | Applies to |
|-----|--------|------------|
| `webhemi.access.admin` | `path` \| `domain` | Admin UI + **protected** admin API only |

- **`path` (default):** admin on the Main site’s primary site-host at `/admin`; protected API at `/admin/api`.
- **`domain`:** selectable in Settings **only if** a Main site host exists with `surface=admin`, verification `verified`, and enabled. Admin UI on that host; protected API on the same host under `/api` relative to that host’s admin root — canonical paths: `https://admin…/…` for UI and `https://admin…/api/…` for protected JSON (see below).
- If the admin surface host is deleted or loses verified/enabled: **`adminAccess` resets to `path`** automatically.

**No `apiAccess` setting.** Public API is always path.

### Protected admin API vs public API

| API | Where | Auth |
|-----|--------|------|
| Protected admin API | Always tied to the **admin entry** | Admin session (+ CSRF as today) |
| Public (frontend) API | **Every** `surface=site` host at `/api` | Frontend session / public tokens as designed later; **not** admin session |

Path admin mode:

- Admin UI: `{mainSiteHost}/admin`
- Protected API: `{mainSiteHost}/admin/api` (today’s Symfony prefix stays coherent)

Domain admin mode:

- Admin UI: `{adminHost}/` (and `{adminHost}/login`, …)
- Protected API: `{adminHost}/api` (admin-host-local `/api`, **not** the public site `/api`)
- `{mainSiteHost}/admin…` → **redirect** to `{adminHost}…`

Public API (all modes):

- `{anySiteHost}/api…` → that **site’s** public API (Host → Site scope)
- Never offered as a selectable host surface; never domain-only entry

### Redirects (canonical entry)

| Condition | Behavior |
|-----------|----------|
| `adminAccess=domain` and request hits Main site-host `/admin…` | Redirect to admin host (same path suffix after `/admin` → admin host path) |
| `adminAccess=path` and request hits a live admin host | Redirect to Main site-host `/admin…` |
| Non–main site host `/admin…` | Redirect to **canonical** admin entry (Main path or admin host), or 404 if Main cannot be resolved — prefer redirect |
| `admin.othersite…` | Must not exist (Main-only surface); if present in bad data → 404 |

### Reserved paths (never in site explorer / CMS tree)

On every **site** host:

- `/api` — public API
- `/login` — frontend login
- `/register` — frontend registration

On Main site host when `adminAccess=path`:

- `/admin` — admin UI (+ `/admin/login`, `/admin/api`, …)

These prefixes are blocked for virtual directory / content paths.

### Dual authentication

| Entry | Example | Session |
|-------|---------|---------|
| Frontend | `{siteHost}/login` | Frontend firewall / cookie (site-scoped) |
| Admin | path: `{mainHost}/admin/login` · domain: `{adminHost}/login` | **Separate** admin firewall / cookie |

Same User entity may log into both; **sessions are never shared**. Frontend styling for `/login` / `/register`; Retro OS for admin login.

### Config storage

- Install-global file: **`var/config/webhemi.yaml`** (not under git; `var/` already local).
- Symfony reads it via a small config layer / parameter bag (defaults if file missing).
- Control Panel → Settings (later phase) edits this file (atomic write). Not a Site row.

Example shape:

```yaml
webhemi:
  access:
    admin: domain   # path | domain — matches current seed with admin.webhemi.local
  paths:
    admin: /admin
    admin_api: /admin/api   # used when access.admin = path
    public_api: /api
    login: /login           # frontend
    register: /register
```

When `access.admin = domain`, protected admin API is served on the admin host at `/api` (not `/admin/api` on www). The `paths.admin_api` value applies to **path mode** only; document that in Settings copy.

### Protected Main site / host

- Main site + its primary **site** host: not deletable / not disableable (existing installer plan).
- Admin surface host: **not** permanently protected; optional; delete → `adminAccess=path`.

---

## Out of scope here

- Implementing public `/api` controllers (contract only).
- Full Settings UI (consumes this config later).
- Deep-link query lifecycle (`?window=`) — separate phase; runs on whatever canonical admin URL exists after this lands.
- Removing Symfony `/admin/api` routes before routing slice rewrites domain-mode admin host `/api`.

---

## Implementation slices (when scheduled)

1. **Config reader + default `var/config/webhemi.yaml`** — **done** (`WebhemiConfigLoader`, `config/webhemi.yaml.dist`, seed ensures domain file for local admin host).
2. **Drop `api` surface** — **done** (enum, Create/UpdateHostInput, Hosts UI, migration `api`→`site`).
3. **Main-only `admin` surface** — API + Hosts form rules.
4. **HostContext / redirect matrix** — path vs domain; reserved-path awareness hooks.
5. **Auth split** — frontend vs admin login routes + separate session cookies.
6. **Settings window** — edit `access.admin` with domain option gated on admin host health; delete-host listener resets to path.

---

## Relationship to other plans

| Doc | Change |
|-----|--------|
| [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) | Default install = path admin; optional admin host; no required `api.*` host; open decisions closed as above |
| [CURRENT.md](./CURRENT.md) | New phase replacing vague “path-based admin restore” |
| Deep links | After canonical admin entry is stable (or in parallel only for client open/raise; URL redirects follow this ADR) |
