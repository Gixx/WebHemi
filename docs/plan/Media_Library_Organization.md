# Media library organization + site links

> **Status:** done (organization + site links). Delete/embed usage = CURRENT Phase 1.  
> **Parent:** [CURRENT.md](./CURRENT.md) · [Site_Content_Model.md](./Site_Content_Model.md) · [Site_Content_Slice2.md](./Site_Content_Slice2.md).  
> **Language:** English only.

## Locked product rules

| Topic | Choice |
|-------|--------|
| Media library role | Reference library of uploaded blobs (images, video, PDF, …) |
| Organize inside library | **DnD reparent** assets between media folders / library root (`PATCH` `folderNodeId`). **No Cut/Paste** for media assets |
| Media → site tree | **Copy** (or DnD onto a site folder) creates a **`media_ref`** node (link). Blob stays in the library |
| Site tree delete of `media_ref` | Deletes the link only |
| Mixed selection | Allowed (Win98): each item uses its own API |
| Media asset Copy | Only meaningful as “link into site tree”, not duplicate blob rows (`(site_id, content_hash)` unique) |
| Media-tree **folders** | Still normal content nodes: Cut/move/`PATCH parentId` as today |

## Out of scope (later phase)

Media library **delete** that audits link vs Lexical embed usage, cascade soft-delete of `media_ref`s, and an extra confirm when embeds would point at null — park as its own CURRENT phase after this one.

## API

| Method | Path | Body | Purpose |
|--------|------|------|---------|
| `PATCH` | `/admin/api/sites/{siteId}/media/{id}` | `{ folderNodeId: number \| null }` | Reparent asset inside media tree |
| `POST` | `/admin/api/sites/{siteId}/nodes` | existing `kind: media_ref` + `mediaAssetId` + site-tree `parentId` | Link from library into site |

## UI

- Enable Copy when selection includes media assets (and/or site nodes as today).
- Paste / DnD of media assets into **site** folder → `POST` `media_ref`.
- DnD of media assets into **media** folder / library root → `PATCH` `folderNodeId`.
- Cut remains **nodes only** (site + media folders), not media assets.
- Media Library location: File → **Upload…** (replaces New Page); OS drag-and-drop into the content pane; pale yellow notice bar.
- Allowed uploads: images, video, audio, PDF, Office/ODF documents (`MediaAssetMimePolicy`).

## Upload API

| Method | Path | Body | Purpose |
|--------|------|------|---------|
| `POST` | `/admin/api/sites/{siteId}/media` | multipart `file` + optional `folderNodeId` | Create / dedupe blob in library |
