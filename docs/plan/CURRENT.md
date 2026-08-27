# WebHemi — current progress (single source of truth)

> **For new chat sessions:** read this file first.  
> Detailed ADRs / notes remain under `docs/plan/` for history; **status and next work are only maintained here.**  
> Last updated: 2026-08-27.

---

## Done

- Integration contract (layers, theme scope, no shared Admin↔theme UI)
- Admin styles in `webhemi-ui` (scoped Retro OS / Admin Theme)
- Chrome atoms + Storybook; layout bricks, SystemIcon, scrollbar
- Dynamic `accessKey`, FieldRow label conventions, Storybook Guide
- AdminDesktop in PHP (Login + desktop mount); legacy Tailwind admin removed
- Desktop shell: registry, drag, taskbar, Start menu, resize/maximize, persistence
- File Explorer brick: nav, menubar, site open, delete/undo, clipboard, properties, splitter, multi-select, drag-drop
- Sites & Hosts full CRUD, ownership verify→assign, operator feedback (status bar / MessageDialog)
- Bootstrap Doctrine migrations restored; entity `setVerification` / `setIsEnabled`
- Admin deep links (`?window=` / `?id=`)
- Context menu chrome (Storybook + ExplorerMenuBar icons); product `onContextMenu` wiring still open
- Storybook MSW for `/admin/api`
- RBAC baseline: protected Admin + Site Admin, empty permission seed, voter rewrite
- Control Panel: Permissions, Roles, Users (full CRUD)
- Users RBAC rules + Start → My Account; My Account profile (avatar, links, Security)
- Settings: `access.admin` path \| domain; Symfony debug toolbar toggle
- Admin access mode + reserved paths + dual admin/frontend auth
- Protected Main site + primary www host (`is_protected` guards)
- Frontend sites/themes seams; Hello world Host→Site→theme resolve; `GET /api/site`
- Site content schema + admin content/media APIs (soft-delete, publication, hidden)
- Explorer live forest + mutations (cut/move; New Folder/Page, trash, restore)
- Site-interior Settings surface
- Lexical document editor + accordion custom block
- Public CMS routing + Lexical/accordion theme render
- Desktop icon drag: grid snap, nearest-free collision, localStorage (`webhemi.admin.desktop.icons.v1`)
- Explorer server-side copy-paste (deep node copy; media move still open)

---

## Remaining

Absolute order. Every item is its own phase (no slices / sub-phases). Do in sequence unless you deliberately reorder and renumber here.

**Next up:** Phase 1.

### Phase 1 — Media library move / reparent

Move media assets between media folders (API + explorer DnD / paste). Detail: [Site_Content_Slice2.md](./Site_Content_Slice2.md).

### Phase 2 — Context menu product wiring

Wire `onContextMenu` on desktop, explorer, Sites/Hosts rows, taskbar, etc. Chrome atom already exists. Detail: [Admin_Context_Menu.md](./Admin_Context_Menu.md).

### Phase 3 — Content Security Policy

NelmioSecurityBundle: report-only → enforce; nonces for AssetMapper / admin boot; later public-site policy. Detail: [Content_Security_Policy.md](./Content_Security_Policy.md).

### Phase 4 — Packaging / distribution

Zip/git release of WebHemi.PHP; document zero-Node production and `var/themes` / shipped theme paths. Aligns with [WebHemi_Architecture_and_Roadmap.md](./WebHemi_Architecture_and_Roadmap.md).

### Phase 5 — Control Panel: Themes window

List shipped + `var/themes`; zip upload + validation; per-Site theme assignment. Admin theme is not an installable row. Detail: [Frontend_Sites_and_Themes.md](./Frontend_Sites_and_Themes.md).

### Phase 6 — Locale / multi-language folder trees

`locale` folder type and language-tree rules (hreflang / `lang`). Detail: [Site_Content_Model.md](./Site_Content_Model.md).

### Phase 7 — Folder types: gallery, file_list, filter_list

Index/view modes and virtual `filter_list` children after MVP folder support. Detail: [Site_Content_Model.md](./Site_Content_Model.md).

### Phase 8 — Permission readonly flags

Mark deliberate permissions readonly in API + Permissions UI; then decide default-readonly custom roles. Detail: [RBAC_Reset.md](./RBAC_Reset.md) · [Permissions_Window.md](./Permissions_Window.md).

### Phase 9 — Sites list filtered for non-Admin

Operators without Admin see only sites they are assigned to. Detail: [RBAC_Reset.md](./RBAC_Reset.md) · [Users_Window.md](./Users_Window.md).

### Phase 10 — Shared MenuBar chrome

Extract `ExplorerMenuBar` to a reusable chrome atom if other windows need it. Detail: [FileExplorer_Window.md](./FileExplorer_Window.md).

### Phase 11 — Installer wizard

WordPress-style first-run: language, DB, primary domain, admin user → migrations → protected main site/host with path admin; lock when done. Detail: [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md).

### Phase 12 — webhemi-js engine

Next.js + Payload outline; consume `@webhemi/ui`. Not blocking PHP admin work. Detail: [WebHemi_Architecture_and_Roadmap.md](./WebHemi_Architecture_and_Roadmap.md).

---

## Detail docs (reference only)

| File | Role |
|------|-------|
| [Admin98_Product_Integration.md](./Admin98_Product_Integration.md) | Original phase narrative (historical) |
| [Admin98_Integration_Contract.md](./Admin98_Integration_Contract.md) | ADR / contract |
| [Admin98_Phase5_Desktop_Shell.md](./Admin98_Phase5_Desktop_Shell.md) | Shell MVP + icon drag (**done**) |
| [Admin98_Phase6_Admin_Windows.md](./Admin98_Phase6_Admin_Windows.md) | Windows slices + follow-ups (mostly done) |
| [Remove_Legacy_Admin_UI.md](./Remove_Legacy_Admin_UI.md) | Legacy Tailwind admin delete (**done**) |
| [Admin_API_Access_Mode.md](./Admin_API_Access_Mode.md) | Admin path/domain, reserved paths, dual auth |
| [Host_Ownership_Verification.md](./Host_Ownership_Verification.md) | Ownership rules (**done**) |
| [Sites_Hosts_Full_CRUD.md](./Sites_Hosts_Full_CRUD.md) | CRUD contract (**done**) |
| [Deep_Links.md](./Deep_Links.md) | Admin deep links (**done**) |
| [Storybook_MSW.md](./Storybook_MSW.md) | Storybook MSW (**done**) |
| [Permissions_Window.md](./Permissions_Window.md) | CP Permissions (**done**; readonly = Phase 8) |
| [Roles_Window.md](./Roles_Window.md) | CP Roles (**done**) |
| [Users_Window.md](./Users_Window.md) | CP Users (**done**) |
| [Users_RBAC_and_My_Account.md](./Users_RBAC_and_My_Account.md) | Users permissions + My Account (**done**) |
| [My_Account_Profile.md](./My_Account_Profile.md) | My Account personal data + Security (**done**) |
| [Settings_Window_Access_Mode.md](./Settings_Window_Access_Mode.md) | CP Settings access mode (**done**) |
| [Settings_Symfony_Debug_Toolbar.md](./Settings_Symfony_Debug_Toolbar.md) | Settings debug toolbar (**done**) |
| [RBAC_Reset.md](./RBAC_Reset.md) | RBAC baseline (**done**; filters/readonly = Phases 8–9) |
| [Admin_Context_Menu.md](./Admin_Context_Menu.md) | Context menu chrome (**done**; product wiring = Phase 2) |
| [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) | Installer + protected main (installer = Phase 11) |
| [Protected_Main_Base_Guards.md](./Protected_Main_Base_Guards.md) | `is_protected` guards (**done**) |
| [Site_Content_Model.md](./Site_Content_Model.md) | Content ADR (MVP done; locale/gallery = Phases 6–7) |
| [Site_Content_Slice1.md](./Site_Content_Slice1.md) | Schema + admin APIs (**done**) |
| [Site_Content_Slice2.md](./Site_Content_Slice2.md) | Explorer wiring + copy (**done**; media-move = Phase 1) |
| [Site_Content_Slice3.md](./Site_Content_Slice3.md) | Site Settings surface (**done**) |
| [Site_Content_Slice4.md](./Site_Content_Slice4.md) | Lexical editor (**done**) |
| [Site_Content_Slice5.md](./Site_Content_Slice5.md) | Public routing + theme render (**done**) |
| [Frontend_Sites_and_Themes.md](./Frontend_Sites_and_Themes.md) | Themes seams (**done**; Themes CP = Phase 5) |
| [FileExplorer_Window.md](./FileExplorer_Window.md) | Explorer brick (**done**; MenuBar extract = Phase 10) |
| [Content_Security_Policy.md](./Content_Security_Policy.md) | CSP plan (Phase 3) |
| [WebHemi_Architecture_and_Roadmap.md](./WebHemi_Architecture_and_Roadmap.md) | Multi-repo / dual-engine vision |

When status changes, **update this file** (move a phase into Done as a short bullet, renumber Remaining from 1).
