# Storybook MSW for `/admin/api`

> **Status:** done (CURRENT Phase 3).  
> **Parent:** [CURRENT.md](./CURRENT.md) · was Phase 6b in [Admin98_Product_Integration.md](./Admin98_Product_Integration.md).  
> **Language:** English only.

## Intent

Review Admin data windows in Storybook **without** a running Symfony backend, by mocking the live `/admin/api` HTTP contract with MSW.

## Setup (webhemi-ui)

- `msw` + `msw-storybook-addon` (devDependencies)
- Worker: `public/mockServiceWorker.js` (`npx msw init ./public --save`)
- Storybook `staticDirs` includes `../public`
- `.storybook/preview.tsx`: `mswLoader` with `onUnhandledRequest: 'bypass'`
- Handlers co-located at `src/admin/api/msw/` (excluded from package `dist` / `tsconfig.lib`)

## Stories

`Admin/Components/AdminDesktop`:

| Story | Behavior |
|-------|----------|
| `MswOpenSitesWindow` | List Sites via MSW |
| `MswCreateSite` | New Site → POST → row appears |
| `MswSitesListError` | 500 list error dialog |

Stories use `apiCsrfToken` (real `createAdminApiClient` + `fetch`), **not** the injected `sitesApi` mock.

There is no empty-sites demo: after install the product always has at least one protected main site.

## Out of scope

- Shipping MSW / worker in the published NPM package
- Replacing all `sitesApi` inject stories (keep for isolated unit plays)
- AssetMapper / PHP mount changes
