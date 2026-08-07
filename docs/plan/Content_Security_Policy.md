# Content Security Policy (CSP) for WebHemi.PHP

> Status: **plan only** — not implemented yet.  
> Scope: `webhemi-php` (Symfony shell). Storybook (`webhemi-ui`) and `webhemi-js` are out of scope for this doc.  
> Related: [CURRENT.md](./CURRENT.md) (schedule note), [Installer_and_Protected_Base_Site.md](./Installer_and_Protected_Base_Site.md) (public site surfaces).

---

## Why

CSP is a browser-enforced allowlist for scripts, styles, images, frames, connections, etc. It is **defense in depth against XSS**, not a replacement for Twig escaping, CSRF, or RBAC (those already exist / are planned separately).

WebHemi will render multi-tenant **admin** and later **public site** HTML. Once editors or themes can inject markup, CSP becomes much more valuable than for a pure JSON API.

**Today:** no `Content-Security-Policy` header, no `nelmio/security-bundle`, no nonce wiring.  
**`symfony/security-bundle`** = auth / firewalls / voters — **not** CSP.

---

## Goals / non-goals

### Goals

1. Ship CSP via **NelmioSecurityBundle** (Symfony-native headers + Twig `csp_nonce()`).
2. Roll out in two modes: **report-only first**, then **enforce**.
3. Keep the current admin boot working: Twig → AssetMapper `importmap('app')` → Stimulus / Turbo / UX React → `@webhemi/ui`.
4. Stay compatible with **zero-Node production** (no Vite/webpack CSP plugins required).
5. Leave clear hooks for the future public frontend (Phase 12+) and user-generated content.

### Non-goals (v1)

- Perfect CSP for third-party embeds (YouTube, maps, CDNs) before those features exist.
- Hardening Storybook / Next.js (`webhemi-js`) in this pass.
- Replacing CSRF or auth with CSP.
- Immediate enforce in production without a report window.

---

## Stack constraints (why the policy looks like this)

| Piece | CSP impact |
|-------|------------|
| `{{ importmap('app') }}` in [`base.html.twig`](../../webhemi-php/templates/base.html.twig) | Renders **inline** `<script type="importmap">` + module bootstrap. Needs a **script nonce** (or `'unsafe-inline'`, which we avoid). |
| AssetMapper CSS-in-importmap trick | May emit `data:application/javascript,…` entries. With a strict `script-src`, Symfony docs recommend adding **`'strict-dynamic'`**. |
| `'strict-dynamic'` | Browsers then **ignore** `'self'` / host allowlists for scripts; trust flows from the nonced bootstrap. **Every** top-level `<script>` must carry the nonce. |
| Admin CSS | `<link href="{{ asset('admin/index.css') }}">` → `style-src 'self'` is enough for stylesheets. |
| React `style={{…}}` | Uses the CSSOM from an already-allowed script; usually fine **without** `'unsafe-inline'` on `style-src`. Prefer classes over inline style long-term. |
| Admin JSON API | Same origin (`/admin/api/…`) → `connect-src 'self'`. |
| Turbo Drive | After navigation, injected scripts/styles may need the nonce on a `<meta name="csp-nonce">` (and optionally reuse via `X-CSP-Nonce`). Wire this when Turbo navigations are real product paths; admin is mostly a single React mount today. |
| Web Profiler (dev) | Extra inline scripts/styles — **relax or disable CSP in `dev`**, or use a separate report-only policy. Do not fight the profiler in enforce mode. |

Official AssetMapper + CSP pattern (Symfony docs):

```twig
{{ importmap('app', { nonce: csp_nonce('script') }) }}
```

---

## Package choice

Install in `webhemi-php`:

```bash
composer require nelmio/security-bundle
```

Use Nelmio for:

- CSP (`enforce` + `report` / report-only)
- Optional later: clickjacking (`X-Frame-Options` / `frame-ancestors`), HSTS, content-type sniffing headers

Do **not** confuse with `symfony/security-bundle` (already required).

---

## Target policy (admin + login shell)

Starting point for **HTML** responses. JSON API responses can omit CSP or use a minimal policy (CSP mainly protects document contexts).

### Directives (intended meaning)

| Directive | Value (concept) | Reason |
|-----------|-----------------|--------|
| `default-src` | `'self'` | Safe fallback |
| `base-uri` | `'self'` | Block `<base>` hijack |
| `object-src` | `'none'` | No plugins |
| `frame-ancestors` | `'self'` (or `'none'` for admin) | Clickjacking |
| `form-action` | `'self'` | Login / future forms stay same-origin |
| `script-src` | `'nonce-…'` + `'strict-dynamic'` (+ `'self'` for older browsers if desired) | Importmap + module graph |
| `style-src` | `'self'` (+ style nonce only if we add inline `<style>`) | Linked admin CSS |
| `img-src` | `'self'` `data:` | Icons / possible data URIs |
| `font-src` | `'self'` | Theme fonts if any |
| `connect-src` | `'self'` | `/admin/api`, same-host fetch |
| `frame-src` | `'none'` until embeds exist | Tighten by default |
| `upgrade-insecure-requests` | prod only, behind HTTPS | Optional later |

Avoid `'unsafe-inline'` and `'unsafe-eval'` on `script-src` in the **target** enforce policy.

### Report endpoint

Nelmio built-in reporter + Monolog (security channel), e.g.:

- Route: `POST /csp/report` (exact path TBD; keep it outside auth or allow public POST)
- Log violations; do not block the reporter with a too-strict CSP on itself

---

## Suggested Nelmio sketch

Illustrative only — tune after report-only noise settles.

```yaml
# config/packages/nelmio_security.yaml
nelmio_security:
    csp:
        enabled: true
        report_logger_service: monolog.logger.security
        hosts: []          # all hosts; revisit if multi-domain admin needs splits
        content_types: []  # or restrict to text/html later

        # Phase A: report only (header: Content-Security-Policy-Report-Only)
        report:
            level1_fallback: false
            browser_adaptive:
                enabled: true
            report-uri: ['/csp/report']
            default-src: ["'self'"]
            base-uri: ["'self'"]
            object-src: ["'none'"]
            frame-ancestors: ["'self'"]
            form-action: ["'self'"]
            script-src:
                - "'self'"
                - "'strict-dynamic'"
            style-src: ["'self'"]
            img-src: ["'self'", 'data:']
            font-src: ["'self'"]
            connect-src: ["'self'"]
            frame-src: ["'none'"]

        # Phase B: copy the same block under `enforce:` after reports are clean
        # enforce: … (omit until ready)
```

```yaml
# config/routes.yaml (or config/routes/nelmio_security.yaml)
csp_report:
    path: /csp/report
    methods: [POST]
    defaults:
        _controller: nelmio_security.csp_reporter_controller::indexAction
```

```twig
{# templates/base.html.twig — javascripts / importmap block #}
{% block importmap %}
    {{ importmap('app', { nonce: csp_nonce('script') }) }}
{% endblock %}
```

Optional Turbo bridge (when needed):

```twig
{% set _cspNonce = csp_nonce('script') %}
<meta name="csp-nonce" content="{{ _cspNonce }}">
{# also call csp_nonce('style') if/when inline styles need it — same or separate usage per Nelmio #}
```

```yaml
# config/packages/nelmio_security.yaml — when@dev
when@dev:
    nelmio_security:
        csp:
            enabled: false   # simplest: don’t fight Web Profiler
            # or: report-only with a looser script-src including 'unsafe-inline'
```

---

## Rollout phases

### A — Wire + report-only (implementation slice)

1. `composer require nelmio/security-bundle`
2. Add CSP **report** config + `/csp/report` route + logger channel
3. Pass nonce into `importmap()` in `base.html.twig`
4. Manually smoke-test: `/login`, `/admin` (desktop mount), Sites/Hosts open + API calls
5. Watch Monolog / browser console for violations for a few days of local/dev use

**Done when:** pages work; violations are logged; no enforce header yet.

### B — Tighten from reports

1. Fix real violations (missing nonce, unexpected hosts, `data:` scripts → confirm `'strict-dynamic'`)
2. Decide `frame-ancestors` for admin (`'none'` vs `'self'`)
3. Confirm JSON endpoints don’t need a document CSP (optional: skip CSP for `application/json`)

**Done when:** report noise is understood and acceptable (profiler noise excluded via `when@dev`).

### C — Enforce on admin + login

1. Copy the stable report policy under `enforce:`
2. Keep report-uri for a while (enforce + report is fine)
3. Add a small WebTestCase or manual checklist: login, dashboard, one mutating API call

**Done when:** admin/login work under enforce; no `'unsafe-inline'` on scripts.

### D — Public site / CMS content (align with CURRENT Phase 12+)

When `/` serves themes and later rich content:

1. Possibly **separate** CSP for site vs admin (Nelmio `hosts` / request matcher / path-based config)
2. Prefer **no** arbitrary inline scripts in theme HTML; if needed, server-issued nonces only
3. For user HTML: sanitize first; CSP is the backstop (`script-src` without unsafe-inline)
4. Revisit `img-src` / `media-src` / `frame-src` when media and embeds land

**Done when:** public shell has at least report-only CSP before shipping user-editable HTML widely.

---

## Scheduling note (CURRENT.md)

This is **security hardening**, not a product window. Suggested timing:

- **Do Phase A** anytime after the admin shell is stable (now is fine) — low product risk in report-only.
- **Finish Phase C before or with Phase 12** (Hello world public site), so the public surface does not launch with zero CSP while admin already has habits for nonces.
- Do **not** block Control Panel phases 4–8 on CSP.

No renumber of CURRENT product phases required; treat CSP as a parallel checklist linked from CURRENT’s detail table.

---

## Test / acceptance checklist

- [ ] `Content-Security-Policy-Report-Only` present on HTML admin/login responses (Phase A)
- [ ] Importmap scripts carry matching `nonce`
- [ ] React admin boots; `/admin/api` fetches succeed (`connect-src`)
- [ ] Violations land in logs via `/csp/report`
- [ ] Dev profiler either exempt or not running under enforce
- [ ] Enforce mode: no console CSP errors on happy path (Phase C)
- [ ] Doc note in `webhemi-php` README: how to temporarily disable CSP if a theme breaks (operators)

---

## References

- [NelmioSecurityBundle — CSP + nonce](https://symfony.com/bundles/NelmioSecurityBundle/current/index.html)
- [Symfony AssetMapper — Using a CSP](https://symfony.com/doc/current/frontend/asset_mapper.html#using-a-content-security-policy-csp)
- MDN: [Content-Security-Policy](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy), [`nonce`](https://developer.mozilla.org/en-US/docs/Web/HTML/Global_attributes/nonce)
