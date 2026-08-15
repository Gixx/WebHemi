# Phase 10 Slice 1 — Content schema + admin APIs

> **Status:** implemented (schema + admin APIs). Explorer wiring = [Site_Content_Slice2.md](./Site_Content_Slice2.md) (in progress).  
> **Parent:** [Site_Content_Model.md](./Site_Content_Model.md) · [CURRENT.md](./CURRENT.md) Phase 10.  
> **Language:** English only.

## Locked for this slice

| Decision | Choice |
|----------|--------|
| Folder `publication` + `hidden` | **Yes** (same as leaves; default may become `published` later) |
| Hidden + published public access | **Unlisted** — omit from nav/indexes; direct URL OK |
| Media `content_hash` | From real file I/O (`hash_file` / upload); store under `var/media/` |
| IDs | Int autoincrement (match Sites/Hosts) |
| Permissions | `content.list` / `content.edit` / `content.delete` (nodes + trash); `media.list` / `media.edit` / `media.delete` (assets). Site-interior (not Admin-only). Always pass **site id** as voter subject. |

## In scope

- Tables: `content_node`, `media_asset`
- Admin CRUD under `/admin/api/sites/{siteId}/…`
- Soft-delete, restore, purge; folder soft-delete cascades to descendants
- Media multipart upload + dedupe by `(site_id, content_hash)` when not deleted

## Out of scope (later slices)

- Explorer forest endpoint / UI wiring  
- Site settings surface  
- Document WYSIWYG  
- Public URL resolve  
- Installer seed tree  
