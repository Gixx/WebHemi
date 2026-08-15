# Phase 10 Slice 3 — Site settings surface

> **Status:** implemented.  
> **Parent:** [Site_Content_Model.md](./Site_Content_Model.md) · [CURRENT.md](./CURRENT.md) Phase 10.  
> **Language:** English only.

## Goal

Activate the explorer **Settings** root and open a **site-interior** settings window (not CP install Settings).

## Locked decisions

| Topic | Choice |
|-------|--------|
| Surface | New shell window `site-settings` (per site); click Settings root opens it |
| API | `GET/PATCH /admin/api/sites/{siteId}/settings` |
| Permissions | `site_settings.list` / `site_settings.edit` (site-interior; subject = site id) |
| Editable | `name`, `description`; `faviconMediaId` (existing live media on site) |
| Read-only | Hosts list; assigned users (email + role); `themeId` (Themes CP later) |
| Protected Main | Name editable; no slug/enabled/delete here |
| Manage CTAs | “Manage in Hosts…” / “Manage in Users…” only when actor can `host.list` / `user.list` |
| Out of scope | Theme picker, favicon multipart upload UI (use media id), invite users |

## Schema

- `site.description` — nullable text  
- `site.favicon_media_id` — nullable FK → `media_asset` (ON DELETE SET NULL)

## Implementation order

1. Migration + entity  
2. PHP GET/PATCH settings + mapper/updater  
3. Enable Settings root in explorer forest  
4. UI `SiteSettingsWindow` + AdminDesktop shell kind + explorer open hook  
5. MSW + docs / CURRENT  
