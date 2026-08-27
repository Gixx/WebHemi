# Phase 10 Slice 2 — Explorer PHP tree + API wiring

> **Status:** implemented (read + write). Copy-paste done; media reparent still deferred.  
> **Parent:** [Site_Content_Model.md](./Site_Content_Model.md) · [Site_Content_Slice1.md](./Site_Content_Slice1.md) · [CURRENT.md](./CURRENT.md).  
> **Language:** English only.

## Goal

Replace empty/fixture explorer forests with live site content, and persist explorer mutations that Slice 1 already exposes. No document WYSIWYG, no Settings surface, no public routing.

## Locked decisions

| Topic | Choice |
|-------|--------|
| Forest load | Aggregate `GET /admin/api/sites/{siteId}/explorer` (not N+1 client walks) |
| Forest read perms | Require both `content.list` and `media.list` |
| UI ids | `node-{id}`, `media-{id}`; roots `site-{siteId}`, `site-{siteId}-media`, `-trash`, `-settings` |
| After mutation | Reload forest from PHP (server is source of truth) |
| Copy-paste | `POST /nodes/{id}/copy` `{ parentId }` — deep copy folders; unique sibling slug (`-copy`) |
| Cut / DnD move | `PATCH /nodes/{id}` `{ parentId }` for **nodes only** |
| Media move | Defer (no media folder PATCH in this slice) — CURRENT Phase 1 |
| Trash list | Soft-deleted **nodes and media** (flat) |
| Settings root | Still `disabled: true` (Slice 3) |
| New Page | `POST` document with empty `body`; editor = Slice 4 |
| Undo | Soft-delete → `POST …/restore` (single slot); purge not undoable |

## In scope

### PHP

- Extend trash listing to include soft-deleted media assets
- `GET …/explorer` — four roots (Site, Media library, Recycle Bin, Settings stub) via `ExplorerForestMapper`
- Reuse live children / trash repos; nest folders for tree pane; leaves + child folders in content pane

### UI

- Admin API client: explorer + node/media CRUD helpers (CSRF as Sites/Hosts)
- Map DTO → `ExplorerItem[]`
- `SiteFileExplorer`: fetch on open, remount/sync after load/mutation, MessageDialog on errors
- Wire: New Folder, New Page, Rename, Delete (soft), Delete in trash (purge), Undo restore, Cut+Paste / DnD node move, Copy+Paste (server deep copy)
- Media-asset move still deferred
- MSW handlers for Storybook product demos; keep fixture forests for pure chrome stories

## Out of scope

- Settings site-interior (Slice 3)
- Document WYSIWYG (Slice 4)
- Public URL resolve (Slice 5)
- Deep media reparent / media-asset move
- Context menu / keyboard shortcuts
- Installer seed tree

## Wiring matrix

| UI | API |
|----|-----|
| Open site window | `GET …/explorer` |
| New Folder / New Page | `POST …/nodes` |
| Rename | `PATCH …/nodes/{id}` |
| Delete (live) | `DELETE …/nodes/{id}` or `DELETE …/media/{id}` |
| Delete (in trash) | `…/purge` |
| Undo soft-delete | `POST …/restore` |
| Cut + Paste / DnD | `PATCH` node `parentId` |
| Copy + Paste | `POST …/nodes/{id}/copy` `{ parentId }` |
| Media upload | `POST …/media` multipart (minimal entry if menubar stub allows) |

## Implementation order

1. PHP trash media + explorer endpoint + tests
2. UI types / client / MSW
3. Forest load + sync in `SiteFileExplorer`
4. Delete / restore / purge
5. Create folder/page + rename
6. Node move; disable copy / media move
7. Update [CURRENT.md](./CURRENT.md) when complete
