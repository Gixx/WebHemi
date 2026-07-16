# Local development (hub)

## Quick start

From the hub root:

```bash
make up      # Storybook + UI watch/sync + Symfony
make status
make down
```

| URL | What you get |
|-----|----------------|
| http://127.0.0.1:6006 | Storybook (`webhemi-ui`) |
| http://127.0.0.1:8000/login | Admin login (React UI) |
| http://127.0.0.1:8000/admin | Admin dashboard (after login) |
| http://127.0.0.1:8000/ | JSON site stub (by design) |

Logs and PIDs live under `.dev/` (gitignored).

## Why `http://127.0.0.1:8000/` is JSON

`/` is the **site** home controller and currently returns JSON context (app, surface, host). That is intentional until a public site UI exists.

Use **`/login`** or **`/admin`** for the control panel — paths work on any Host header, including `127.0.0.1`.

## Why `admin.webhemi.local` does not resolve

The seed creates DB rows for `admin.webhemi.local` / `www.webhemi.local`, but your OS must map those names to the machine running Symfony.

### WSL2 + Windows browser

Edit the **Windows** hosts file (Admin notepad):

`C:\Windows\System32\drivers\etc\hosts`

```text
127.0.0.1   admin.webhemi.local
127.0.0.1   www.webhemi.local
```

Then open:

- http://admin.webhemi.local:8000/login
- http://www.webhemi.local:8000/

`make up` starts Symfony with `--allow-http --no-tls` so plain HTTP works with custom hostnames (no HTTPS redirect loop).

### Linux-only

```bash
sudo tee -a /etc/hosts <<'EOF'
127.0.0.1   admin.webhemi.local
127.0.0.1   www.webhemi.local
EOF
```

## What `make up` runs

1. One-shot `webhemi-ui` build + `webhemi-php` `bin/sync-ui.sh`
2. Storybook on port **6006**
3. `chokidar` watch on `webhemi-ui/src` → rebuild → sync into PHP AssetMapper
4. Symfony CLI daemon on port **8000** (or `php -S` fallback)

## Makefile vs DDEV

| | Makefile (now) | DDEV (later) |
|--|----------------|--------------|
| Storybook + UI watch | Native Node processes | Extra service / sidecar |
| Symfony | `symfony server` / `php -S` | nginx + PHP-FPM container |
| Custom hostnames | Manual `/etc/hosts` | Built-in `*.ddev.site` |
| DB | SQLite file (default) | MySQL/Postgres easy |
| Fit | Hub multi-process orchestration | PHP+DB+HTTPS “real” stack |

**Use Make now** for UI↔PHP sync ergonomics. Consider DDEV later if you want containerized PHP, managed hostnames, and a non-SQLite database without fighting WSL networking.

## Prerequisites

- PHP ≥ 8.4, Composer, Symfony CLI (`symfony`)
- Node/npm (for Storybook + UI build)
- Seeded PHP app: `cd webhemi-php && composer install && php bin/console doctrine:schema:create -n && php bin/console app:seed -n`
