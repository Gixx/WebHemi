# Host Ownership Verification Flow

This document explains how site host verification works during create and manual re-verify actions.

## What it validates

When a host is submitted (for `site` surface), the app proves ownership by writing a temporary token file to `public/`, then reading that file through the submitted host URL.

- Match found -> host status becomes `verified`
- No match / request failure -> host status becomes `pending`
- Temporary file is always deleted in `finally`

## Sequence

```mermaid
sequenceDiagram
    participant Admin as Admin User
    participant C as SiteHostController
    participant V as HostOwnershipVerifier
    participant FS as Local public/ filesystem
    participant H as Submitted Host

    Admin->>C: POST create or verify
    C->>V: verify(hostname)
    V->>FS: write <token>.txt
    V->>H: GET candidate URL(s)
    H-->>V: response body or failure
    alt token content matches
        V-->>C: verified=true
        C->>C: set status=verified
    else mismatch or timeout
        V-->>C: verified=false
        C->>C: set status=pending
    end
    V->>FS: delete <token>.txt (always)
    C-->>Admin: flash + redirect
```

## URL building behavior

Verification URLs are built from current request context when available:

- Primary candidate uses current request scheme (`http` or `https`)
- If current request uses non-default port, that port is included
- Secondary candidate uses the opposite scheme as fallback
- Without current request context, default candidates are:
  - `http://<host>/<token>.txt`
  - `https://<host>/<token>.txt`

## Validation and guardrails

- New hosts are restricted to `surface=site` in create action
- Hostname validation is regex-based (hostname only, no protocol/path)
- Manual verify endpoint allows re-check after DNS/server setup changes

## References

- `src/Admin/Controller/SiteHostController.php`
- `src/SiteHost/Verification/HostOwnershipVerifier.php`
- `src/SiteHost/Verification/HostVerificationResult.php`
- `templates/admin/site_host/list.html.twig`
- `templates/admin/site_host/form.html.twig`
