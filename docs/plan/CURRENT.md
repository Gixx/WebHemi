# WebHemi — current progress (single source of truth)

> **For new chat sessions:** read this file first.  
> Detailed ADRs / slice notes remain under `docs/plan/` for history; **status and next work are only maintained here.**  
> Last updated: 2026-08-13.

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
- **Legacy Tailwind admin removed** — `AdminLayout` / `SitesPage` / `HostsPage` / list views + orphan Twig; redirects kept ([Remove_Legacy_Admin_UI.md](./Remove_Legacy_Admin_UI.md))
- **Admin deep links** — `?window=` / `?id=` opens Sites/Hosts/site explorer (+ CP/Settings); legacy `/admin/sites|hosts` redirect with query ([Deep_Links.md](./Deep_Links.md))
- **Context menu chrome** — `ContextMenu`/`MenuPopup` + ExplorerMenuBar icon/check gutters ([Admin_Context_Menu.md](./Admin_Context_Menu.md)); product `onContextMenu` wiring later
- **Storybook MSW** — `/admin/api` handlers + AdminDesktop list/create demos without PHP ([Storybook_MSW.md](./Storybook_MSW.md))
- **RBAC reset (R1–R3)** — protected Admin + Site Admin; empty permission seed; voter rewrite ([RBAC_Reset.md](./RBAC_Reset.md))
- **Settings window** — `access.admin` path \| domain; GET/PATCH `/admin/api/settings`; host-loss reset to path ([Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md); shipped with Phase 1)
- **Settings: Symfony debug toolbar** — GroupBox + checkbox; editable in `dev`/`stage` only ([Settings_Symfony_Debug_Toolbar.md](./Settings_Symfony_Debug_Toolbar.md))
- **Protected Main site + primary www host** — `is_protected` flags; API 409 + Sites/Hosts UI locks ([Protected_Main_Base_Guards.md](./Protected_Main_Base_Guards.md))

---

## Remaining (renumbered phases)

Former “Phase 6 Slice D”, “6b”, installer P0… are **flattened**: each item below is its own phase, numbered from **1**.  
Do them in order unless a note says otherwise.

### Phase 1 — Admin access mode + reserved paths

Install-global `var/config/webhemi.yaml` (`access.admin`: path \| domain); HostContext redirects; drop `api` host surface; Main-only `admin` surface; reserved `/admin`, `/api`, `/login`, `/register`; dual admin vs frontend auth; **Settings window** for access mode. Detail: [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md) · [Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md). **Phase 1 complete.**

### Phase 2 — Deep links `?window=…`

**Complete.** Open Sites/Hosts (and site explorer) from `?window=` with entity support (`?id=`). Detail: [Deep_Links.md](./Deep_Links.md). Was Phase 6 Slice D.

### Phase 3 — Storybook MSW for `/admin/api`

**Complete.** `msw` + storybook addon; handlers in `webhemi-ui/src/admin/api/msw/`; AdminDesktop list/create demos without PHP. Detail: [Storybook_MSW.md](./Storybook_MSW.md). Was Phase 6b.

### Phase 3b — RBAC reset (before Users / Roles / Permissions)

**R1–R3 done.** Protected **Admin** + **Site Admin**; empty permission seed; rewritten voter. Admin = full CP + all sites; Site Admin = per-site interior via `site_assignment`. R4 = CP CRUD windows. Detail: [RBAC_Reset.md](./RBAC_Reset.md).

### Phase 4 — Control Panel: Permissions window

**Complete.** Full CRUD (create / edit / delete). Seed catalog may be empty; operators add permissions for testing. Readonly flags deferred. Depends on Phase 3b. Detail: [Permissions_Window.md](./Permissions_Window.md).

### Phase 5 — Control Panel: Roles window

**Complete.** Full CRUD (create / edit / delete, attach permissions). **Admin** and **Site Admin** locked (no delete/edit). Seed those two; custom roles for testing. Depends on Phase 3b / Phase 4 permissions catalog. Detail: [Roles_Window.md](./Roles_Window.md).

### Phase 6 — Control Panel: Users window

**Complete.** API + Retro window + shell kind. Global roles + `site_assignment`. Password on create only; self-delete / last-Admin locks. Depends on Phase 3b / Phase 5 roles. Detail: [Users_Window.md](./Users_Window.md).

### Phase 7 — Control Panel: Settings window

**Complete** (same work as Phase 1 Settings slice). `access.admin` path \| domain; domain gated on healthy Main admin host; auto-reset to path on host loss. Detail: [Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md) · [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md).

### Phase 8 — Protected base site + host

**Complete.** Main site + primary www host use `is_protected`; delete/disable/slug (site) and delete/disable/unassign/surface (host) locked; admin-surface host remains unprotected. Detail: [Protected_Main_Base_Guards.md](./Protected_Main_Base_Guards.md) · [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md).

### Phase 9 — Hello world public site

Minimal frontend on `/` for the base site after install.

### Phase 10 — Packaging / distribution

Zip/git release of WebHemi.PHP; document zero-Node production. Aligns with architecture roadmap packaging.

### Phase 11 — File Explorer: PHP tree API

Replace empty/demo explorer forest with real nav/media (or equivalent) from Symfony. Honor reserved paths.

### Phase 12 — File Explorer: shared MenuBar chrome (optional)

Extract `ExplorerMenuBar` to a reusable chrome atom if other windows need it.

### Phase 13 — webhemi-js engine (later)

Next.js + Payload outline; consume `@webhemi/ui`. Not blocking PHP admin work.

### Phase 14 — Control Panel: Themes window (deferred)

Admin Theme / site theme picker (scope TBD; do not confuse with frontend Default theme work). **Parked near end** until product approach is clear.

### Phase 15 — Installer wizard (deferred)

WordPress-style first-run: language, DB, primary domain, admin user → migrations → protected main site/host with **path** admin. Lock when done. **Parked at end** until ready to schedule — was formerly Phase 9.

---

## Detail docs (reference only)

| File | Role |
|------|------|
| [Admin98_Product_Integration.md](./Admin98_Product_Integration.md) | Original phase narrative 0–7 |
| [Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md) | ADR / contract |
| [Admin98_Phase5_Desktop_Shell.md](./Admin98_Phase5_Desktop_Shell.md) | Shell slices A–F (done) |
| [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md) | Windows slices A–F + follow-ups (mostly done) |
| [Remove_Legacy_Admin_UI.md](./Remove_Legacy_Admin_UI.md) | Legacy Tailwind admin delete (**done**) |
| [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md) | Admin path/domain, reserved paths, dual auth, no `api` surface |
| [Host_Ownership_Verification.md](./Host_Ownership_Verification.md) | Ownership rules (done; naming note) |
| [Sites_Hosts_Full_CRUD.md](./Sites_Hosts_Full_CRUD.md) | CRUD contract (done) |
| [Deep_Links.md](./Deep_Links.md) | Admin `?window=` / `?id=` deep links (**done**) |
| [Storybook_MSW.md](./Storybook_MSW.md) | Storybook MSW for `/admin/api` (**done**) |
| [Permissions_Window.md](./Permissions_Window.md) | CP Permissions CRUD (**done**, Phase 4) |
| [Roles_Window.md](./Roles_Window.md) | CP Roles CRUD (**done**, Phase 5) |
| [Users_Window.md](./Users_Window.md) | CP Users CRUD (**done**, Phase 6) |
| [Users_RBAC_and_My_Account.md](./Users_RBAC_and_My_Account.md) | Users permissions + Start My Account (**done**) |
| [Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md) | CP Settings: `access.admin` path \| domain (**done**, Phase 1 / 7) |
| [Settings_Symfony_Debug_Toolbar.md](./Settings_Symfony_Debug_Toolbar.md) | Settings Symfony debug toolbar (**done**) |
| [RBAC_Reset.md](./RBAC_Reset.md) | RBAC reset: Admin + Site Admin baseline before CP user windows |
| [Admin_Context_Menu.md](./Admin_Context_Menu.md) | Context menu chrome + optional menu icons (slice A done) |
| [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) | Installer + protected main; defers to access-mode ADR (**installer deferred**, Phase 15) |
| [Protected_Main_Base_Guards.md](./Protected_Main_Base_Guards.md) | `is_protected` Main site + www host guards (**done**, Phase 8) |
| [FileExplorer_Window.md](./FileExplorer_Window.md) | Explorer slices A–I (done); PHP API open |
| [Content_Security_Policy.md](./Content_Security_Policy.md) | CSP / Nelmio / nonce plan (report-only → enforce; parallel to product phases) |
| [WebHemi_Architecture_and_Roadmap.md](./WebHemi_Architecture_and_Roadmap.md) | Multi-repo / dual-engine vision |

When status changes, **update this file** (move a bullet from Remaining → Done, renumber only if you deliberately reorder work).
