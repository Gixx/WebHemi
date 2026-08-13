# Settings: Symfony debug toolbar

> **Status:** done.  
> **Parent:** [Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md) · [CURRENT.md](./CURRENT.md).  
> **Language:** English only.

## Scope

Add a **Symfony** GroupBox to the Settings window with one checkbox: **Debug toolbar**.

| Rule | Detail |
|------|--------|
| Persist | `webhemi.symfony.debug_toolbar` in `var/config/webhemi.yaml` |
| Default | `true` when key missing (matches current Symfony `when@dev` toolbar) |
| Editable | Only when `kernel.environment` is `dev` or `stage` |
| Prod (and other envs) | Checkbox **unchecked + disabled**; PATCH of the flag rejected; toolbar never shown |
| Runtime | `SymfonyDebugToolbarSubscriber` disables Profiler when flag is `false` (dev/stage) or env is not editable |
| Stage | WebProfilerBundle + toolbar config enabled for `stage` (same as `dev`) |

## API

- GET `/admin/api/settings` adds:
  - `symfonyDebugToolbar` — effective UI value (`false` outside `dev`/`stage`)
  - `symfonyDebugToolbarEditable` — `true` only for `dev` \| `stage`
- PATCH may include `symfonyDebugToolbar` alone or with `adminAccess`. Toolbar-only save does not end the session. Reject toolbar changes when not editable (422).

## UI

- GroupBox legend `Symfony`, Checkbox `Debug toolbar`.
- Non-editable envs: forced unchecked + disabled.
