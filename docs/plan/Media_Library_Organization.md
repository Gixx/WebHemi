# Media library organization + site links

> **Status:** done (organization + site links + delete/embed usage).  
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

## Delete + embed usage

| Topic | Choice |
|-------|--------|
| Usage audit | `GET …/media/{id}/usage` → live `media_ref`s + Lexical body embeds (`mediaAssetId` walk) |
| Soft-delete library asset | Soft-deletes asset **and cascades** soft-delete of live `media_ref` links |
| Confirm UI | Only when usage is non-empty (refs and/or embeds); one aggregated Yes/No dialog |
| Embeds after delete | Document body keeps the id; public/render treats missing blob as empty (embed block still future) |
| Undo | Soft-delete response includes `cascadedMediaRefIds`; explorer undo restores asset + those nodes |
| Purge | Blocked while live `media_ref`s **or** live document embeds remain |

Lexical **media embed** UI block is still future; the scanner is forward-compatible (any JSON node with matching `mediaAssetId`).

## API

| Method | Path | Body | Purpose |
|--------|------|------|---------|
| `PATCH` | `/admin/api/sites/{siteId}/media/{id}` | `{ folderNodeId: number \| null }` | Reparent asset inside media tree |
| `GET` | `/admin/api/sites/{siteId}/media/{id}/usage` | — | `{ mediaRefs, embeds }` usage audit |
| `DELETE` | `/admin/api/sites/{siteId}/media/{id}` | — | Soft-delete + cascade refs; returns asset + `cascadedMediaRefIds` |
| `POST` | `/admin/api/sites/{siteId}/nodes` | existing `kind: media_ref` + `mediaAssetId` + site-tree `parentId` | Link from library into site |

## UI

- Enable Copy when selection includes media assets (and/or site nodes as today).
- Paste / DnD of media assets into **site** folder → `POST` `media_ref`.
- DnD of media assets into **media** folder / library root → `PATCH` `folderNodeId`.
- Cut remains **nodes only** (site + media folders), not media assets.
- Media Library location: File → **Upload…** (replaces New Page); OS drag-and-drop into the content pane; pale yellow hint.
- Allowed uploads: images, video, audio, PDF, Office/ODF documents (`MediaAssetMimePolicy`).
- Delete: fetch usage for selected library assets; confirm when in use; then soft-delete.

## Upload API

| Method | Path | Body | Purpose |
|--------|------|------|---------|
| `POST` | `/admin/api/sites/{siteId}/media` | multipart `file` + optional `folderNodeId` | Create / dedupe blob in library |
