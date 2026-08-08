# WebHemi.UI — senior code review (fresh eyes)

**Scope:** `webhemi-ui/` (React design system + Admin Retro OS shell)  
**Perspective:** treat the package as unfamiliar production UI; do not assume roadmap intent.  
**Audience:** a follow-up session that will fix findings (file paths + concrete behavior).  
**Date:** 2026-08-08  
**Method:** static read of `src/`, package/build config, and how PHP Twig mounts the exported pages.

> **Product decision (2026-08-08):** Legacy Tailwind admin stack **deleted** — [`docs/plan/Remove_Legacy_Admin_UI.md`](../plan/Remove_Legacy_Admin_UI.md). Findings **P0-1**, **P1-7**, and **P2-1** are closed by that remove. Follow-up sessions should target desktop API/shell issues (P1-1…P1-6, P0-2/P0-3).

---

## Executive summary

`@webhemi/ui` was reviewed as a dual-era library: a **legacy Tailwind admin** (`AdminLayout`, `SitesPage`, `HostsPage`, shared atoms) still exported alongside a **Retro OS desktop** (`AdminDesktop`, chrome, bricks, `/admin/api` client) that is the real product surface. The desktop API path is generally careful (CSRF header, session-expiry detection, React text escaping). Highest-risk **desktop** gaps: **unhandled `fetch` throws that leave UI stuck**, **sticky form-error state on Sites**, plus structural debt (god-object desktop, client-only file explorer). Legacy CSRF/form findings are resolved by deleting that stack (see product decision above), not by patching forms.

Severity key:

| Level | Meaning |
|-------|---------|
| **P0** | Security or data-integrity risk in shipped paths |
| **P1** | Clear bug / broken UX / exploitability that users can hit |
| **P2** | Design flaw, inconsistency, or maintainability debt that will cause bugs |
| **P3** | Optimization / polish / nice-to-have |

---

## P0 — Security / trust boundary

### P0-1. Legacy `SitesPage` / `HostsPage` create forms omit CSRF — **superseded by delete**

**Where (historical):** `SitesPage` / `HostsPage` + orphan Twig `sites.html.twig` / `hosts.html.twig` (PHP already redirected to dashboard).

**Resolution:** Remove the legacy stack per [Remove_Legacy_Admin_UI.md](../plan/Remove_Legacy_Admin_UI.md). Do **not** add CSRF fields to those forms.

### P0-2. Mutating API client makes CSRF optional

**Where:** `src/admin/api/client.ts` — CSRF header only if `csrfToken` is truthy; `AdminDesktop` gates `canEdit` on `apiCsrfToken` / mock client.

**Issue:** The client itself will happily POST/PATCH/DELETE without `X-CSRF-TOKEN` if a consumer forgets the prop or injects a custom `sitesApi`. Defense relies on caller discipline + backend. Fine for Storybook mocks; fragile as a public package API.

**Fix direction:** Fail closed for non-GET when no token (unless explicit `csrfToken: false` / mock mode). Document that production mounts must pass Twig `csrf_token('admin_api')`.

### P0-3. Hard-coded session redirect + open-ended `logoutHref`

**Where:**

- `AdminDesktop.tsx` — `handleAlertClose` → `window.location.assign('/login')`  
- `StartMenu.tsx` — `window.location.assign(logoutHref)` with no same-origin check

**Issue:** Login path is not configurable (breaks apps not hosted at `/login`). `logoutHref` is trusted from props (OK from Twig `path('app_logout')`); as a library surface it is an **open redirect / javascript: URL** vector if a future consumer passes query/user input.

**Fix direction:** `loginHref` prop (default `/login`); validate `logoutHref` as relative same-origin path (reject `//`, `http:`, `javascript:`).

---

## P1 — Bugs and exploitable UX holes

### P1-1. Network / thrown `fetch` errors leave loading forever

**Where:** `createAdminApiClient` (`client.ts`) — `await fetchImpl(...)` not wrapped; all `AdminDesktop` handlers (`listSites`, `listHosts`, save/delete/assign/verify) `await api.*` without `try/catch`.

**Issue:** Offline, DNS failure, or aborted network throws → **unhandled rejection**, `sitesLoading` / `hostsLoading` / `saving` / `deleting` flags never cleared. Window stuck on “Loading…” / disabled buttons.

**Fix direction:** Wrap `request()` in try/catch → `{ ok: false, code: 'network_error', message }`. In desktop handlers, `finally { set*Loading(false) }` / clear busy flags. Add Storybook case with throwing `fetch`.

### P1-2. Sites form errors stick; Hosts clears them

**Where:**

- `HostsWindow` — `onClearFormError` on alert dismiss, open New/Edit, close form, selection change  
- `SitesWindow` — **no** equivalent; `AdminDesktop` only wires `onClearFormError` for Hosts  
- Sticky `sitesFormError` remains after dismiss; reopening a form can re-fire the error `useEffect` because `formError` is still truthy

**Issue:** After a failed save/assign/unassign, dismissing the Error dialog does not clear parent state. Re-open New/Edit can **immediately re-show** the previous error. Hosts already solved this.

**Fix direction:** Mirror Hosts: `onClearFormError` on Sites + clear in `AdminDesktop` when opening/closing form and on alert close (without clearing the pending login-redirect flag incorrectly).

### P1-3. Optimistic host unassign writes a non-existent field

**Where:** `AdminDesktop.tsx` ~`handleUnassignHost` fallback when `listHosts` fails:

```ts
status: row.verification, // HostsWindowHost has `verification`, not `status`
```

**Issue:** Excess property after spread bypasses TypeScript. Dead field; does not update verification. Confuses future readers and any code that might expect `SiteFormHostOption.status` shape on host rows.

**Fix direction:** Only clear `siteId` / `siteSlug` / `siteName`; keep `verification` via spread. Prefer typed helper `toWindowHost` only.

### P1-4. `LoginForm` documents `remember` but UI has no checkbox

**Where:** `LoginForm.tsx` — `onSubmit` payload includes `remember: data.get('remember') === 'on'`, but no `name="remember"` control. Native submit path never sends remember either.

**Issue:** Dead / misleading API; Symfony “remember me” cannot be enabled from this form.

**Fix direction:** Add chrome Checkbox, or remove `remember` from the payload type until product needs it.

### P1-5. Persisted window geometry can restore off-screen / oversized

**Where:** `shell/persistence.ts` + hydrate in `AdminDesktop`; drag clamp exists in `geometry.ts` / `DesktopWindow`, but **load path does not clamp** `left/top/width/height` to current work area. `maximized: true` restored without re-querying work size.

**Issue:** After viewport change (laptop ↔ external monitor, zoom), windows can open **unreachable** or maximized with stale dimensions until user resizes.

**Fix direction:** On hydrate and on `resize` of dashboard, clamp all entries; if maximized, recompute work size like `toggleMaximize`.

### P1-6. Module-global explorer DnD stash

**Where:** `explorerDnd.ts` — `let activeDragIds: string[] = []` at module scope.

**Issue:** Two explorer windows (or Storybook + app) share one drag buffer. Stale IDs can apply to the wrong tree after interrupted drags. Not a server exploit (client-only tree today), but a logic footgun once multi-window / real FS lands.

**Fix direction:** Per-instance drag id (WeakMap / React context), clear on `dragend`/`pointerup` reliably.

### P1-7. Legacy list links assume REST-ish routes that may not exist — **superseded by delete**

**Where (historical):** `SiteListView`, `UserListView`, `RoleListView`, `SiteHostListView` — default `editHref` / `createHref` like `/admin/sites/${id}`.

**Resolution:** Delete legacy list views with the AdminLayout stack ([Remove_Legacy_Admin_UI.md](../plan/Remove_Legacy_Admin_UI.md)).

---

## P2 — Design / architecture / consistency

### P2-1. Two admin products in one package — **superseded by delete**

**Evidence (historical):** Retro OS (`AdminDesktop`) vs legacy Tailwind (`AdminLayout`, `SitesPage`, …) both exported; orphan Twig still present.

**Resolution:** Delete legacy admin exports/components/templates ([Remove_Legacy_Admin_UI.md](../plan/Remove_Legacy_Admin_UI.md)). Leftover `shared/` → `themes/default` remains a later follow-up.

### P2-2. `AdminDesktop` is a ~1200-line orchestration god object

**Where:** `src/admin/pages/AdminDesktop.tsx`

**Issue:** Shell window manager + sites CRUD + hosts CRUD + status flash timers + auth redirect + explorer open live in one component. Hard to test mutations in isolation; easy to miss Sites/Hosts parity (see P1-2).

**Fix direction:** Extract `useAdminSitesApi` / `useAdminHostsApi` / `useDesktopShell` hooks; keep page as composition.

### P2-3. File explorer is a local simulation with no API

**Where:** `bricks/FileExplorerWindow/*`, `explorerTreeOps.ts`, `AdminDesktop` `explorerTreeForSite` defaulting to empty tree.

**Issue:** Cut/copy/paste/trash/undo work only in React state. Operators may believe files persist. DnD MIME `text/plain` also exposes ids to other drop targets.

**Fix direction:** Explicit “preview / not connected” status in UI; gate destructive actions until backend exists; avoid advertising as production FS.

### P2-4. Deprecated dual save APIs on Sites

**Where:** `SitesWindow` — `onSave` vs deprecated `onCreate`; success close logic depends on `saving` + error flags.

**Issue:** `onCreate` path closes form immediately without waiting for async result (`handleFormSave`), unlike `onSave`. Trap for Storybook/old callers.

**Fix direction:** Remove `onCreate`; single async contract (`Promise` or explicit `onSaveResult`).

### P2-5. Package surface: `export *` from admin chrome

**Where:** `src/admin/index.ts` → `export * from './chrome'`; root re-exports admin + themes.

**Issue:** Many atoms and internal helpers (`promoteTabRow`, `attachCustomScrollbar`, `useTableView`) are public API. Breaking changes become semver landmines. Shared Button intentionally not on root, but chrome Button is — naming collision risk if someone deep-imports wrong path.

**Fix direction:** Explicit public export list; keep `_lib` private.

### P2-6. `"use client"` banner on entire tsup bundle

**Where:** `tsup.config.ts` banner on all JS.

**Issue:** Forces Client Components semantics for Next consumers even for pure helpers/types. Harmless for PHP AssetMapper; noisy for RSC.

**Fix direction:** Split entries (client UI vs isomorphic utils) or document that the whole package is client-only.

### P2-7. Accessibility gaps on “modal” layers

**Where:** `DesktopModal` / `FloatingModal` / `MessageDialog` — no focus trap, no initial focus, no `aria-modal` on floating layer (legacy `Modal.tsx` has `role="dialog"` but is unused by desktop). Escape on Start menu only; MessageDialog Close does not consistently match Win98 focus rules.

**Issue:** Keyboard / screen-reader users can tab behind blockers; blocked clicks only flash/ding.

**Fix direction:** Focus trap + restore focus on close; `role="alertdialog"` for errors; document intentional Retro quirks vs WCAG target.

### P2-8. Global `accessKey` collisions

**Where:** Sites and Hosts both use `n/e/d/c` (and Hosts `v`); form dialogs reuse `o/c` etc.

**Issue:** With both windows open, browser access keys are document-global — unpredictable activation.

**Fix direction:** Enable access keys only on the active (top z) window / modal; or scope via custom keydown handler instead of native `accesskey`.

### P2-9. Host / slug validation weaker than product rules may need

**Where:**

- Host: `/^[a-z0-9]([a-z0-9.-]*[a-z0-9])?$/` — allows odd labels (`a.-.b`), no IDNA/punycode story  
- Site slug: kebab-case lowercase — good baseline  

**Issue:** UI accepts values backend may reject (or worse, accepts values backend should reject). Relying only on server is OK if errors always surface (they do via `fields`), but client regex gives false confidence.

**Fix direction:** Share validation with PHP (documented contract) or keep client checks minimal + always show server `fields`.

### P2-10. `SystemIcon` default `href="#"`

**Where:** `SystemIcon.tsx`

**Issue:** Desktop icons are `<a href="#">`. Rely on click `preventDefault`. Middle-click / open-in-new-tab navigates to `#`. Prefer `<button>` or `role="button"` with `href` only when real navigation is intended.

### P2-11. Fixed DOM ids on login fields

**Where:** `LoginForm` — `id="email"`, `id="password"`.

**Issue:** Collides if two forms mount; brittle for tests. Prefer `useId()`.

### P2-12. Error message duplication / noise

**Where:** `formatSaveErrors` joins `formError` + field messages; server may already put the same text in both.

**Issue:** Duplicate lines in MessageDialog (mitigated partly by `Set`).

---

## P3 — Performance / quality / ops

### P3-1. Refetch storms after every mutation

**Where:** After create/update/delete/assign, `AdminDesktop` often `listSites` + `listHosts` again.

**Issue:** Correct but chatty; double-fetch when both windows open (effects also load on open). Fine at small N; will hurt later.

**Fix direction:** Apply returned entity to local state; optional invalidate; shared SWR/query layer later.

### P3-2. `attachCustomScrollbar` always listens on `window`

**Where:** `attachCustomScrollbar.ts` — `pointermove` / `up` / `blur` for lifetime of every scrollable.

**Issue:** Many tables/panels ⇒ many global listeners (cleanup exists — good). Consider one shared pointer router if profiling shows cost.

### P3-3. No non-Storybook unit tests for API client / tree ops

**Where:** CI runs typecheck, oxlint, Storybook Vitest, Chromatic. Pure functions (`explorerTreeOps`, `parseResult`, persistence parse) lack fast node tests.

**Fix direction:** Vitest node project for `api/client` parse + persistence + tree ops.

### P3-4. CSS build disables minify

**Where:** `vite.config.ts` / `vite.css.config.ts` — `cssMinify: false` (lightningcss + `@media (not (hover))`).

**Issue:** Larger CSS to PHP. Documented tradeoff; track upstream fix.

### P3-5. README typo / Chromatic public storybook

**Where:** README badge “Stoybook”; Chromatic publishes UI publicly.

**Issue:** Cosmetic; ensure no secrets in stories (fixtures look fine). Avoid real-looking credentials in Storybook defaults.

### P3-6. `playAdminSound` creates a new `Audio` each time

Minor; consider pooled elements if sounds spam.

---

## What looks solid (do not “fix” away)

- React children for API/error strings → **no `dangerouslySetInnerHTML`** observed; XSS via message text is low risk.  
- API client uses `credentials: 'same-origin'`, `redirect: 'manual'`, and maps login bounce / 401 to a clear unauthorized result.  
- Desktop edit gated when CSRF missing (read-only chrome).  
- Persistence parse is defensive (version check, kind allowlist, corrupt → empty).  
- Modal blocker + owned z-index model is thoughtfully documented (complex but intentional).  
- Cancel flags on list effects avoid setState-after-unmount for the happy path.

---

## Suggested fix order for a follow-up session

0. **P0-1 / P1-7 / P2-1** — **done** (legacy stack deleted; [Remove_Legacy_Admin_UI.md](../plan/Remove_Legacy_Admin_UI.md))  
1. **P1-1** — network error handling in `client.ts` + busy `finally` in `AdminDesktop`  
2. **P1-2** — Sites `onClearFormError` parity with Hosts  
3. **P1-3** — unassign optimistic update field  
4. **P0-2 / P0-3** — CSRF fail-closed + configurable/safe redirects  
5. **P1-4, P1-5, P1-6** — login remember, geometry clamp, DnD instance state  
6. **P2-2** — split `AdminDesktop` once bugs above are green  
7. **P2-7 / P2-8** — a11y + accessKey scoping as a dedicated slice  

---

## File index (highest signal)

| Path | Why |
|------|-----|
| `src/admin/api/client.ts` | Transport, CSRF, auth detection, missing network catch |
| `src/admin/pages/AdminDesktop.tsx` | Shell + CRUD orchestration, sticky errors, redirects |
| `src/admin/components/SitesWindow/*` | Sites UX, missing error clear |
| `src/admin/components/HostsWindow/*` | Reference implementation for error clearing |
| `src/admin/components/LoginForm/LoginForm.tsx` | Auth form gaps |
| `src/admin/shell/persistence.ts` | localStorage hydrate |
| `src/admin/bricks/FileExplorerWindow/explorerDnd.ts` | Global DnD state |
| `src/admin/bricks/DesktopModal/DesktopModal.tsx` | Modal stacking / blocker |
| `src/index.ts`, `src/admin/index.ts` | Public API |
| `tsup.config.ts`, `vite.css.config.ts` | Bundle / CSS pipeline |

---

## Out of scope (note for fixers)

- PHP controller CSRF configuration and API authorization — verify when fixing P0-1; do not assume UI-only patches are enough.  
- Real file persistence / host ownership probe correctness — backend contracts.  
- Visual Chromatic diffs — not reviewed here.

---

## Reviewer notes

This review intentionally ignored prior chat context and treated naming like `Phase 6` as comments only. Prefer deleting the legacy Tailwind admin mount points over indefinitely maintaining two product UIs.
