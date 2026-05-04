# Local Dev Server Setup

This guide explains how to run the Symfony local server over HTTPS with a trusted local certificate.

It uses a generic local domain pattern so you can replace it with your own values:

- Base domain: `<your-domain>.local`
- Wildcard subdomains: `*.<your-domain>.local`

Example values in commands below use `<your-domain>.local` and `admin.<your-domain>.local`.

## Prerequisites

- Symfony CLI installed (`symfony` command available)
- OpenSSL installed
- Local project path available in WSL/Linux shell
- Firefox users: CA import is required (Firefox has its own certificate store)

## 1) Install and trust Symfony local CA

Run once on your machine:

```bash
symfony server:ca:install
```

This creates a local Certificate Authority used to sign local development certificates.

## 2) Add local domains to hosts file

Map your domain(s) to localhost.

### Linux/WSL `/etc/hosts`

```text
127.0.0.1 <your-domain>.local
127.0.0.1 admin.<your-domain>.local
```

### Windows hosts file (if browser runs on Windows)

Path: `C:\Windows\System32\drivers\etc\hosts`

```text
127.0.0.1 <your-domain>.local
127.0.0.1 admin.<your-domain>.local
```

## 3) Generate wildcard certificate signed by Symfony CA

From the project root (set your domain first):

```bash
DOMAIN="<your-domain>.local"
mkdir -p var/certs

openssl req -new -nodes -newkey rsa:2048 \
  -keyout "var/certs/${DOMAIN}.key" \
  -out "var/certs/${DOMAIN}.csr" \
  -subj "/CN=${DOMAIN}" \
  -addext "subjectAltName=DNS:${DOMAIN},DNS:*.${DOMAIN},IP:127.0.0.1"

openssl x509 -req \
  -in "var/certs/${DOMAIN}.csr" \
  -CA "$HOME/.config/symfony-cli/certs/rootCA.pem" \
  -CAkey "$HOME/.config/symfony-cli/certs/rootCA-key.pem" \
  -CAcreateserial \
  -out "var/certs/${DOMAIN}.crt" \
  -days 825 \
  -sha256 \
  -copy_extensions copy

openssl pkcs12 -export \
  -out "var/certs/${DOMAIN}.p12" \
  -inkey "var/certs/${DOMAIN}.key" \
  -in "var/certs/${DOMAIN}.crt" \
  -passout pass:
```

## 4) Start Symfony server with the generated certificate

```bash
DOMAIN="<your-domain>.local"
symfony server:stop
symfony serve -d --p12="var/certs/${DOMAIN}.p12"
```

Now open your app with HTTPS, for example:

- `https://<your-domain>.local:8000`
- `https://admin.<your-domain>.local:8000`

## 5) Firefox trust setup (required)

If Firefox shows `SEC_ERROR_UNKNOWN_ISSUER`, import Symfony's root CA manually:

- Go to: `Settings -> Privacy & Security -> Certificates -> View Certificates -> Authorities -> Import`
- Import this file:
  - Linux/WSL path: `$HOME/.config/symfony-cli/certs/rootCA.pem`
  - Example from WSL path in Explorer: `\\wsl.localhost\Debian\home\<your-user>\.config\symfony-cli\certs\rootCA.pem`
- Enable trust for websites

Restart Firefox after import.

## 6) Verify certificate SANs (optional)

```bash
DOMAIN="<your-domain>.local"
echo | openssl s_client -connect 127.0.0.1:8000 -servername "$DOMAIN" 2>/dev/null \
  | openssl x509 -noout -ext subjectAltName
```

You should see SAN entries including:

- `DNS:<your-domain>.local`
- `DNS:*.<your-domain>.local` (wildcard)

## Troubleshooting

- `SEC_ERROR_UNKNOWN_ISSUER` (Firefox): root CA is not imported into Firefox authorities.
- HTTPS-Only still fails: check hosts file mapping and ensure you open `https://...` explicitly.
- Domain mismatch warning: server is using default localhost cert; restart with `--p12=...`.
- Wrong certificate shown: stop all old Symfony server processes and start again.
- Firefox alternative: set `about:config` -> `security.enterprise_roots.enabled = true` to use OS trust store automatically.
