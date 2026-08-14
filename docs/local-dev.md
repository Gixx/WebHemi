# Local development (hub)

## Quick start

From the hub root:

```bash
make cert    # once: *.webhemi.local PKCS#12 signed by Symfony CA
make up      # Storybook + UI watch/sync + Symfony HTTPS
make status
make down
make test php   # PHPUnit
make test ui    # typecheck, lint, Storybook Vitest, Chromatic
make test       # both
```

### Chromatic token (for `make test ui`)

Chromatic publishes from your machine; set the project token once:

```bash
# hub root .env (gitignored)
echo 'CHROMATIC_PROJECT_TOKEN=chpt_…' >> .env
```

Copy the token from Chromatic → project → Manage → Configure. Same secret as GitHub Actions `CHROMATIC_PROJECT_TOKEN`.

| URL | What you get |
|-----|----------------|
| http://127.0.0.1:6006 | Storybook (`webhemi-ui`) |
| https://127.0.0.1:8000/admin/login | Admin login (path mode / IP) |
| https://admin.webhemi.local:8000/login | Admin login (domain mode; rewritten to `/admin/login`) |
| https://www.webhemi.local:8000/login | Frontend (site) login stub |
| https://www.webhemi.local:8000/ | Public site home (shipped `default` theme Hello world) |
| https://www.webhemi.local:8000/api/site | Thin public site API (Host → Site + theme) |

Logs and PIDs live under `.dev/` (gitignored).
Certificate files live under `webhemi-php/var/certs/` (gitignored via `var/`).

## HTTPS certificate (`*.webhemi.local`)

Same flow as the archived monolith docs:

1. Trust Symfony local CA (once per machine):

```bash
symfony server:ca:install
```

2. Generate wildcard p12:

```bash
make cert
# → webhemi-php/var/certs/webhemi.local.p12
```

SAN includes: `webhemi.local`, `*.webhemi.local`, `localhost`, `127.0.0.1`.

3. `make up` starts:

```bash
symfony serve -d --dir=webhemi-php --p12=webhemi-php/var/certs/webhemi.local.p12
```

If the p12 is missing, `make up` generates it automatically.

### Firefox

Import `$HOME/.config/symfony-cli/certs/rootCA.pem` under
Settings → Privacy & Security → Certificates → Authorities → Import
(trust for websites). Or enable `security.enterprise_roots.enabled` in `about:config`.

## Why `/` is JSON

`/` is the **site** home (theme-aware Hello world). Use **`/admin`** or **`/admin/login`** for the control panel (or `admin.webhemi.local` in domain mode).

## Hosts file (`admin.webhemi.local`)

### Windows browser → edit Windows hosts

`C:\Windows\System32\drivers\etc\hosts`

```text
127.0.0.1   webhemi.local
127.0.0.1   admin.webhemi.local
127.0.0.1   www.webhemi.local
```

### curl / tools inside WSL → edit WSL `/etc/hosts`

```bash
sudo tee -a /etc/hosts <<'EOF'
127.0.0.1   webhemi.local
127.0.0.1   admin.webhemi.local
127.0.0.1   www.webhemi.local
EOF
```

## React UI (zero-Node in webhemi-php)

All TypeScript/React (including admin pages) lives in **`webhemi-ui`**.

`webhemi-php` only has tiny plain JS controllers that re-export from `@webhemi/ui`:

```js
import { LoginPage } from '@webhemi/ui';
export default LoginPage;
```

No `package.json` / `node_modules` in `webhemi-php`. After UI changes:

```bash
make sync-ui
# or: (cd webhemi-ui && npm run build) && (cd webhemi-php && bash bin/sync-ui.sh)
```

`make up` runs Storybook + UI watch→sync + Symfony; Node runs only for `webhemi-ui`.

## Makefile vs DDEV

| | Makefile (now) | DDEV (later) |
|--|----------------|--------------|
| Storybook + UI watch | Native Node processes | Extra service / sidecar |
| Symfony | `symfony serve` + local p12 | nginx + PHP-FPM container |
| Custom hostnames | Manual hosts file | Built-in `*.ddev.site` |
| DB | MariaDB `webhemi_dev` (default) | MySQL/Postgres easy |

**Use Make now** for UI↔PHP sync ergonomics. Consider DDEV later for containerized PHP/DB.

## Prerequisites

- PHP ≥ 8.4, Composer, Symfony CLI (`symfony`), OpenSSL
- Node/npm (Storybook + UI build)
- Seeded PHP app: see `webhemi-php/README.md`
- `symfony server:ca:install` once
