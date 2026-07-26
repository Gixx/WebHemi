# WebHemi

**WebHemi** is an educational, production-minded **dual-engine CMS**. The core idea: keep one **design system** as the visual source of truth, while two backend stacks can render it — a PHP-first path and a JavaScript-first path.

This repository is the **hub (meta-repo)**. The runnable code lives in three Git submodules side by side.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

---

## What we are building

A multi-repository platform where:

1. **`webhemi-ui`** — React components developed and reviewed in **Storybook**, published as `@webhemi/ui` (NPM).
2. **`webhemi-php`** — **WebHemi.PHP**: Symfony + Twig shell, React admin via AssetMapper/importmap. **No Node.js required in production.**
3. **`webhemi-js`** — **WebHemi.JS** (later phase): Next.js + Payload CMS, consuming the same `@webhemi/ui` package.

```
                    +---------------------------+
                    |   webhemi-ui (Storybook)  |
                    |   @webhemi/ui on NPM      |
                    +---------------------------+
                                  |
              +-------------------+-------------------+
              |                                       |
              v                                       v
    +-------------------+                 +-------------------+
    |   webhemi-php     |                 |   webhemi-js      |
    |   Symfony CMS     |                 |   Next + Payload  |
    |   zero-Node prod  |                 |   (phase 3)       |
    +-------------------+                 +-------------------+
```

### Platform capabilities (both engines)

| Capability | Description |
|------------|-------------|
| **Multi-tenant** | Sites as tenants; users, roles, and assignments scoped per site where needed |
| **Multi-domain** | Multiple hostnames per site; request host drives routing context |
| **Surfaces** | `admin`, `site`, and `api` — same host model, different entry points |
| **RBAC** | Roles and permission strings (e.g. `site.list`, `host.verify`) |
| **Host ownership** | File-based verification for `site` surface hosts (pending → verified → active) |

The legacy Symfony monolith (Twig admin, inline CSS) is **retired**. Admin UI comes only from `@webhemi/ui`.

---

## Repository layout (this hub)

| Path | Repository | Role |
|------|------------|------|
| [`webhemi-ui/`](webhemi-ui/) | [Gixx/webhemi-ui](https://github.com/Gixx/webhemi-ui) | Design system + Storybook |
| [`webhemi-php/`](webhemi-php/) | [Gixx/webhemi-php](https://github.com/Gixx/webhemi-php) | Symfony CMS engine |
| [`webhemi-js/`](webhemi-js/) | [Gixx/webhemi-js](https://github.com/Gixx/webhemi-js) | Next + Payload (outline only for now) |

Architecture and roadmap: [`docs/plan/WebHemi_Architecture_and_Roadmap.md`](docs/plan/WebHemi_Architecture_and_Roadmap.md)

---

## Quick start

### Clone with submodules

```bash
git clone --recurse-submodules git@github.com:Gixx/WebHemi.git
cd WebHemi
```

If you already cloned without submodules:

```bash
git submodule update --init --recursive
```

### One-command local stack

```bash
# once: install deps + seed PHP (see webhemi-php/README.md)
make up       # Storybook + UI watch→sync + Symfony
make status
make down
```

| URL | Purpose |
|-----|---------|
| http://127.0.0.1:6006 | Storybook |
| https://127.0.0.1:8000/login | Admin login (HTTPS + local p12) |
| https://admin.webhemi.local:8000/login | Admin via seeded host |
| https://www.webhemi.local:8000/ | Site JSON stub (by design) |

Custom hosts and certificate: [`docs/local-dev.md`](docs/local-dev.md). First time: `make cert` (or `symfony server:ca:install` then `make cert`).

### UI / PHP / JS separately

- UI: `cd webhemi-ui && npm install && npm run storybook` / `npm run build`
- PHP: see [`webhemi-php/README.md`](webhemi-php/README.md)
- JS: not implemented yet — [`webhemi-js/README.md`](webhemi-js/README.md)

---

## Legacy monolith

The previous single-repo Symfony application is preserved in Git history:

- Tag: **`archive/pre-hub-monolith`**
- A local copy may exist in `.old/` (gitignored) for quick reference on your machine

Do not treat `.old/` as the source of truth — use the tag or submodule repos.

---

## Documentation

| Document | Description |
|----------|-------------|
| [Local development](docs/local-dev.md) | `make up` / hosts / Makefile vs DDEV |
| [Architecture & roadmap](docs/plan/WebHemi_Architecture_and_Roadmap.md) | Multi-repo design and implementation phases |
| [Admin98 product integration](docs/plan/Admin98_Product_Integration.md) | Retro OS admin tech-demo → `@webhemi/ui` + PHP |
| [Admin98 integration contract](docs/plan/Admin98_Integration_Contract.md) | Phase 0 ADR: layers, theme scope, markup, no shared UI |
| [webhemi-php README](webhemi-php/README.md) | PHP setup, sync-ui, QA |
| [webhemi-ui README](webhemi-ui/README.md) | Storybook and library build |
| [Host ownership flow](webhemi-php/docs/host-ownership-verification-flow.md) | Domain verification behaviour |

---

## License

MIT — see [LICENSE](LICENSE).
