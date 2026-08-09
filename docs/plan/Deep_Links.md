# Admin deep links (`?window=…`)

> **Status:** done (CURRENT Phase 2).  
> **Parent:** [CURRENT.md](./CURRENT.md) · was Phase 6 Slice D in [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md).  
> **Language:** English only.

## Intent

Shareable URLs on the canonical admin entry open (and focus) shell windows, with optional entity selection via `?id=`.

## Contract

| Query | Behavior |
|-------|----------|
| `?window=sites` | Open/raise Sites |
| `?window=sites&id={n}` | Sites + select site `n` when listed |
| `?window=hosts` | Open/raise Hosts |
| `?window=hosts&id={n}` | Hosts + select host `n` when listed |
| `?window=site&id={n}` | Open/raise site explorer for site `n` (must exist in desktop site list) |
| `?window=site-{n}` | Alias for `site` + id `n` |
| `?window=control-panel` | Open/raise Control Panel |
| `?window=settings` | Open/raise Settings |
| Unknown `window` / invalid `id` | Ignored (no toast) |

Rules:

- Single `window` param; first value wins if duplicated.
- Case-insensitive `window`; positive integer `id` only.
- Additive with localStorage window restore: deep link opens/raises and becomes active; other restored windows stay.
- Query stays in the URL (shareable); applied once per mount.
- No SPA router — client reads `location.search` (Storybook: `locationSearch` prop).
- Opening is not permission-gated (same as Control Panel); list/API errors surface in-window.

## Legacy redirects

| Old path | Target |
|----------|--------|
| `/admin/sites` | `/admin?window=sites` |
| `/admin/hosts` | `/admin?window=hosts` |
| `POST /admin/hosts/{id}/verify` | `/admin?window=hosts&id={id}` |

Access-mode host redirects already preserve the query string.

## Out of scope

- Nested form dialogs via URL (`?edit=1`)
- Promote/demote or mutate via deep link
- Future CP kinds (users/roles/…) until those shell windows exist
