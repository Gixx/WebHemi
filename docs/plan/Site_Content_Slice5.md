# Phase 10 Slice 5 — Public content routing + theme render

> **Status:** implemented.  
> **Parent:** [Site_Content_Model.md](./Site_Content_Model.md) · [CURRENT.md](./CURRENT.md) Phase 10 · [Frontend_Sites_and_Themes.md](./Frontend_Sites_and_Themes.md).  
> **Language:** English only.

## Goal

Replace the Phase 9 Hello-world-only `/` stub with **CMS-aware public routing**: resolve Host → Site → content path, then render (or redirect) through the active frontend theme. Documents use Lexical JSON from Slice 4; the first custom block (**accordion**) gets public markup.

## Locked decisions

| Topic | Choice |
|-------|--------|
| Engine | **PHP Twig** in the active theme (`ThemeResolver` / `ThemeRenderer`) — zero-Node production |
| Catch-all | `ContentController` — `/` + `/{path}` (low priority) after reserved + `/api` + `/login` |
| URL shapes | Folder = trailing-slash directory; document / redirect / media_ref = `{slug}.html` |
| `/` (site root) | Listable root children → folder index; else **Hello fallback** (`home.html.twig`) |
| Visibility | `draft` → 404; soft-deleted → 404; `scheduled` → live when `publish_at <= now`; `hidden` → unlisted (direct URL OK) |
| Reserved paths | `ReservedPaths` site reserved + admin path → not content |
| Lexical → HTML | `LexicalHtmlRenderer` walker (escape text; allowlisted links) |
| Accordion | `wh-accordion` → `<details>` markup (theme CSS + `blocks/accordion.html.twig` reference) |

## What shipped

| Area | Detail |
|------|--------|
| Visibility | `PublicationVisibility` |
| Path resolve | `PublicPathResolver` + `PublicContentHit` |
| Lexical HTML | `LexicalHtmlRenderer` |
| Controller | `Controller/Site/ContentController` (replaces `HomeController`) |
| Theme | `folder.html.twig`, `document.html.twig`, `blocks/accordion.html.twig`; default theme CSS |
| Media | `BinaryFileResponse` via `MediaBlobStore` |
| Redirect | `302` to safe `/…` or `http(s):` targets |
| Tests | Unit: visibility, path resolve, Lexical render |

## Out of scope (unchanged)

- Locale / gallery / filter_list folder types  
- Public media-library browser  
- Themes CP, caching, sitemap  
- Client-side Lexical on the public site  

## Related

- [Site_Content_Model.md](./Site_Content_Model.md) — URL shapes, publication + hidden  
- [Site_Content_Slice4.md](./Site_Content_Slice4.md) — Lexical JSON + `wh-accordion`  
- [Frontend_Sites_and_Themes.md](./Frontend_Sites_and_Themes.md) — Host → Site → theme seams  
- [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md) — reserved paths  
