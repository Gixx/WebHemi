# Sites & Hosts full CRUD (PATCH + DELETE)

> **Status:** done — see [`CURRENT.md`](./CURRENT.md). This note is the API/UI contract reference.

## API

| Resource | GET list | GET one | POST | PATCH | DELETE |
|----------|----------|---------|------|-------|--------|
| Sites | yes | yes | yes | yes | yes |
| Hosts | yes | yes | yes | yes | yes |

**PATCH only** (no PUT). Writes: `site.edit` / `host.edit` + CSRF.

### Sites

- `GET/PATCH/DELETE /admin/api/sites/{id}`
- DELETE with assigned hosts → `409 hosts_assigned`
- `site_host.site_id` ON DELETE **SET NULL** (migration `Version20260806200000`)

### Hosts

- `GET/DELETE /admin/api/hosts/{id}` → DELETE returns **204**
- PATCH rename uniqueness → `409 host_taken`

## UI

- Sites Edit → `updateSite`; Delete → confirm **Yes/No** MessageDialog → `deleteSite`
- Hosts Delete → same confirm pattern → `deleteHost`
- Slice F status-bar success: `Site updated.`, `Site deleted.`, `Host deleted.`, …

## Out of scope

- Installer protected base site/host flags
- Soft-delete / bulk delete
