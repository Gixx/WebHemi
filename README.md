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

### UI (Storybook / library build)

```bash
cd webhemi-ui
npm install
npm run storybook    # develop components
npm run build        # dist/ for PHP sync or NPM publish
```

### PHP engine

See [`webhemi-php/README.md`](webhemi-php/README.md). Typical flow:

```bash
cd webhemi-php
composer install
composer run sync-ui   # copies ../webhemi-ui/dist into AssetMapper
php bin/console doctrine:migrations:migrate -n
php bin/console app:seed -n
```

Default seed (local): `admin@webhemi.local` / `ChangeMe!` — hosts `admin.webhemi.local` and `www.webhemi.local` (add to `/etc/hosts` for multi-domain smoke tests).

### JS engine

Not implemented yet. See [`webhemi-php/docs/webhemi-js-phase3-outline.md`](webhemi-php/docs/webhemi-js-phase3-outline.md) or [`webhemi-js/README.md`](webhemi-js/README.md).

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
| [Architecture & roadmap](docs/plan/WebHemi_Architecture_and_Roadmap.md) | Multi-repo design and implementation phases |
| [webhemi-php README](webhemi-php/README.md) | PHP setup, sync-ui, QA |
| [webhemi-ui README](webhemi-ui/README.md) | Storybook and library build |
| [Host ownership flow](webhemi-php/docs/host-ownership-verification-flow.md) | Domain verification behaviour |

---

## License

MIT — see [LICENSE](LICENSE).
