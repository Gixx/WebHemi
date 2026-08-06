# WebHemi — current progress (single source of truth)

> **For new chat sessions:** read this file first.  
> Detailed ADRs / slice notes remain under `docs/plan/` for history; **status and next work are only maintained here.**  
> Last updated: 2026-08-06.

---

## Done

Simple checklist of what already shipped (Admin98 + Sites/Hosts path).

- Integration contract (layers, theme scope, no shared Admin↔theme UI)
- Admin styles in `webhemi-ui` (scoped Retro OS / Admin Theme)
- Chrome atoms + Storybook (`Admin/Atoms/*`)
- Product layout bricks + SystemIcon + scrollbar
- Dynamic `accessKey` (Button + FieldRow)
- Field label ownership / FieldRow conventions
- Storybook Guide items marked complete (as applicable)
- AdminDesktop product surface in PHP (Login + desktop mount; live AdminLayout dropped)
- Desktop shell: registry, drag, taskbar, Start menu, resize/maximize, persistence
- File Explorer brick (slices A–I): nav, menubar, site open, delete/undo, clipboard, properties, splitter, multi-select, drag-drop
- Sites window + `/admin/api` Sites list/create (Phase 6 A–C)
- Hosts window + `/admin/api` Hosts list/create
- Host ownership: create unassigned → verify → assign; Verification (`pending`|`verified`) + Status Enabled/Disabled
- Operator feedback (Slice F): success in status bar; errors in MessageDialog; confirm delete
- Sites & Hosts full CRUD: GET-by-id, PATCH, DELETE; site delete blocked while hosts assigned
- Bootstrap Doctrine migrations restored from pre-hub monolith (fresh `migrate` path)
- Entity methods: `setVerification` / `setIsEnabled` (not status/active)
- Late-horizon plans written: installer + protected base site; path-based `/admin` heritage noted

---

## Remaining (renumbered phases)

Former “Phase 6 Slice D”, “6b”, “Phase 7”, installer P0… are **flattened**: each item below is its own phase, numbered from **1**.  
Do them in order unless a note says otherwise.

### Phase 1 — Deep links `?window=…`

Open Sites/Hosts (and later windows) from a query string. Was Phase 6 Slice D (deferred). Needs a clear acceptance criteria before coding.

### Phase 2 — Storybook MSW for `/admin/api`

`msw` + storybook addon; handlers co-located with Admin surfaces; at least one list/save demo without PHP. Was Phase 6b.

### Phase 3 — Remove legacy admin UI

Delete unused Twig/modern AdminLayout stacks and obsolete stories; finish themes/default ownership; changelog for `@webhemi/ui` if visuals break. Was Phase 7 cleanup (UI half).

### Phase 4 — Control Panel: Users window

API + Retro window + shell kind (same pattern as Sites/Hosts).

### Phase 5 — Control Panel: Roles window

Same pattern; respect read-only seeded roles where applicable.

### Phase 6 — Control Panel: Permissions window

List (and edit if product requires); tie to RBAC seed.

### Phase 7 — Control Panel: Settings window

Product settings surface (scope TBD when starting).

### Phase 8 — Control Panel: Themes window

Admin Theme / site theme picker (scope TBD; do not confuse with frontend Default theme work).

### Phase 9 — Path-based admin restore

Port monolith behavior: `/admin` on site host = admin surface; optional `admin.*` → canonical site `/admin` redirect. Prerequisite for single-domain install. See [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md).

### Phase 10 — Protected base site + host

Installer-created (or seeded) main site/host cannot be deleted or disabled. Schema flag + API/UI guards.

### Phase 11 — Installer wizard

WordPress-style first-run: language, DB, primary domain, admin user → migrations → protected main site/host. Lock when done.

### Phase 12 — Hello world public site

Minimal frontend on `/` for the base site after install.

### Phase 13 — Packaging / distribution

Zip/git release of WebHemi.PHP; document zero-Node production. Aligns with architecture roadmap packaging.

### Phase 14 — File Explorer: PHP tree API

Replace empty/demo explorer forest with real nav/media (or equivalent) from Symfony.

### Phase 15 — File Explorer: shared MenuBar chrome (optional)

Extract `ExplorerMenuBar` to a reusable chrome atom if other windows need it.

### Phase 16 — webhemi-js engine (later)

Next.js + Payload outline; consume `@webhemi/ui`. Not blocking PHP admin work.

---

## Detail docs (reference only)

| File | Role |
|------|------|
| [Admin98_Product_Integration.md](./Admin98_Product_Integration.md) | Original phase narrative 0–7 |
| [Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md) | ADR / contract |
| [Admin98_Phase5_Desktop_Shell.md](./Admin98_Phase5_Desktop_Shell.md) | Shell slices A–F (done) |
| [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md) | Windows slices A–F + follow-ups (mostly done) |
| [Host_Ownership_Verification.md](./Host_Ownership_Verification.md) | Ownership rules (done; naming note) |
| [Sites_Hosts_Full_CRUD.md](./Sites_Hosts_Full_CRUD.md) | CRUD contract (done) |
| [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) | Detail for phases 9–13 |
| [FileExplorer_Window.md](./FileExplorer_Window.md) | Explorer slices A–I (done); PHP API open |
| [WebHemi_Architecture_and_Roadmap.md](./WebHemi_Architecture_and_Roadmap.md) | Multi-repo / dual-engine vision |

When status changes, **update this file** (move a bullet from Remaining → Done, renumber only if you deliberately reorder work).
