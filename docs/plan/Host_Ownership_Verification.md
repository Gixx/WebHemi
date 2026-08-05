# Host ownership verification (pending → verified → active)

> **Parent:** [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md) (Hosts follow-up after Slice E)  
> **Domain service (exists):** [`HostOwnershipVerifier`](../../webhemi-php/src/SiteHost/Verification/HostOwnershipVerifier.php)  
> **Legacy note:** [`webhemi-php/docs/host-ownership-verification-flow.md`](../../webhemi-php/docs/host-ownership-verification-flow.md) — probe mechanics still valid; **lifecycle / assignment rules below supersede** that doc’s “create with site / admin→active skip” product rules.

## Product decision (locked)

Hostname ownership is proven with a **short-lived file + token** served from this WebHemi install and fetched **via the candidate hostname**. Status progression:

```text
(create host, no site)  →  pending
        ↓  successful ownership probe
     verified
        ↓  assign to a site
      active
```

Rules:

1. **Create** a host **without** binding it to a site. Status = `pending`. `isActive` may stay `true` as a row flag, but the host is **not** usable for tenant routing until `active` status.
2. **Verify** (`host.verify`): run `HostOwnershipVerifier` (temp file under `public/`, GET `http(s)://{host}/{token}.txt`, body must equal `webhemi-host-verification:{token}`, then delete file). Success → status `verified`. Failure → stay `pending` (operator can retry).
3. **Assign to a site** only when status is `verified` (Sites Hosts tab / Hosts edit / dedicated Assign). Assignment → status `active`.
4. **Only `verified` hosts** appear as assignable in Sites → Hosts (or equivalent). `pending` hosts are listed in Hosts window for verify; `active` hosts are already bound.
5. Surfaces (`admin` | `site` | `api`) describe **entry purpose**, not a verify bypass. **All surfaces** use the same ownership probe before they can become `active` via site assignment. (This replaces the older “admin/api skip probe → active” rule.)

Unassign / re-verify / delete: out of scope until the happy path ships; note as follow-ups.

```mermaid
stateDiagram-v2
  [*] --> pending: create (no site)
  pending --> verified: ownership probe OK
  pending --> pending: probe fail / retry
  verified --> active: assign to site
  active --> [*]
```

## Probe mechanics (unchanged service)

Canonical implementation: `App\SiteHost\Verification\HostOwnershipVerifier`.

1. Generate token; write `public/{token}.txt` with body `webhemi-host-verification:{token}`.
2. Request that path on the candidate host (http/https, optional current-request port).
3. Match trimmed body to expected content.
4. Always unlink the temp file (`finally`).

Do **not** reimplement the probe in React. UI only triggers API + shows result.

## Gap vs current Phase 6 Slice E

| Area | Today (Slice E) | Target |
|------|-----------------|--------|
| Create body | `siteId` **optional** (landed) | keep omit-on-create; assign later when verified |
| Create status | `pending`; site may be null | same; probe before assign |
| Verify API | `POST /admin/api/hosts/{id}/verify` (landed) | unchanged |
| Assign | `POST …/assign` + PATCH rules (landed) | unchanged |
| Sites Hosts tab | assigned table + Assign verified-unassigned + Remove (landed) | unchanged |
| Entity | `site` ManyToOne **nullable** (landed) | unchanged |

## Acceptance criteria

- [x] Operator can create a hostname from Hosts window without choosing a site; row shows `pending` (Site column `—`).
- [x] **Verify** action (Hosts window) calls API; on success status becomes `verified`; on failure stays `pending` with clear error (Error MessageDialog + chord).
- [x] Sites → Hosts (or Hosts assign) only lists **verified, unassigned** hosts plus hosts already on that site; cannot assign `pending`.
- [x] Assigning a verified host to a site sets status `active` and sets the site FK.
- [x] Probe uses existing `HostOwnershipVerifier`; no duplicate client-side HTTP check.
- [x] Permission: verify requires `host.verify`; create/assign require `host.edit` (assign may also need `site.edit` — decide in implement slice).
- [x] Storybook: Hosts Verify play; Sites Assign play for verified-unassigned (MSW optional / 6b).

## Suggested implementation slices

### H1 — Schema + create without site — **done** (partial vs original bullet)

- `SiteHost::$site` nullable + migration; create/update/unassign API; Hosts UI Site select **None**; Site column `—`.
- Remaining for full H1 intent: optionally hide Site on **New** (still available so operators can assign early until H3).

### H2 — Verify API + Hosts UI action — **done**

- `POST /admin/api/hosts/{id}/verify` (`host.verify`, CSRF).
- Load host; MVP: `pending` only; run `HostOwnershipVerifier`; set `verified` or `422` envelope.
- Hosts window: **Verify** enabled for selected `pending` row; Error MessageDialog on failure; refresh list on success.
- Storybook plays + PHP unit tests.

### H3 — Assign verified → active — **done**

- `POST /admin/api/hosts/{id}/assign` body `{ siteId }` (`host.edit`, CSRF); require `verified` + null site → set site + `active`.
- PATCH host `siteId` uses the same assign rules.
- Sites Hosts tab: Assign select (verified-unassigned) + Assign; Remove unassigns.
- Hosts Edit: Site select locked while `pending`.
- Storybook play + PHP unit tests.

### H4 — Docs + seed alignment

- Update `webhemi-php/docs/host-ownership-verification-flow.md` to point here for product rules.
- Seed hosts: either `active` with site (fixtures) or document that seed skips probe (dev only).
- Hub README capability row: pending → verified → active via assign.

## Explicitly out of scope (until criteria expand)

- DNS TXT alternative to file probe.
- Automatic verify on create (always operator-triggered for MVP).
- Demote `active` → `verified` on unassign (landed with unassign).
- Deep links to Hosts / Verify.
- Changing how request host resolves tenants beyond requiring `active` + site (routing follow-up).

## Status

**H1–H3 done** for happy path (create → verify → assign → active). H4 docs/seed alignment still open.
