#!/usr/bin/env bash
# Generate a wildcard PKCS#12 cert for *.webhemi.local signed by Symfony CLI CA.
set -euo pipefail

DOMAIN="${DOMAIN:-webhemi.local}"
PHP_ROOT="$(cd "$(dirname "$0")/../../webhemi-php" && pwd)"
CERT_DIR="$PHP_ROOT/var/certs"
CA_DIR="${HOME}/.config/symfony-cli/certs"

if [[ ! -f "$CA_DIR/rootCA.pem" || ! -f "$CA_DIR/rootCA-key.pem" ]]; then
  echo "Symfony local CA not found. Installing..."
  symfony server:ca:install
fi

mkdir -p "$CERT_DIR"

echo "Generating key + CSR for ${DOMAIN} / *.${DOMAIN} ..."
openssl req -new -nodes -newkey rsa:2048 \
  -keyout "${CERT_DIR}/${DOMAIN}.key" \
  -out "${CERT_DIR}/${DOMAIN}.csr" \
  -subj "/CN=${DOMAIN}" \
  -addext "subjectAltName=DNS:${DOMAIN},DNS:*.${DOMAIN},DNS:localhost,IP:127.0.0.1"

echo "Signing with Symfony root CA..."
openssl x509 -req \
  -in "${CERT_DIR}/${DOMAIN}.csr" \
  -CA "$CA_DIR/rootCA.pem" \
  -CAkey "$CA_DIR/rootCA-key.pem" \
  -CAcreateserial \
  -out "${CERT_DIR}/${DOMAIN}.crt" \
  -days 825 \
  -sha256 \
  -copy_extensions copy

echo "Exporting PKCS#12 (empty password)..."
openssl pkcs12 -export \
  -out "${CERT_DIR}/${DOMAIN}.p12" \
  -inkey "${CERT_DIR}/${DOMAIN}.key" \
  -in "${CERT_DIR}/${DOMAIN}.crt" \
  -passout pass:

chmod 600 "${CERT_DIR}/${DOMAIN}.key" "${CERT_DIR}/${DOMAIN}.p12" 2>/dev/null || true

echo
echo "Created: ${CERT_DIR}/${DOMAIN}.p12"
echo "SAN check:"
openssl x509 -in "${CERT_DIR}/${DOMAIN}.crt" -noout -ext subjectAltName
echo
echo "Start with: make up"
echo "Or: symfony serve -d --dir=webhemi-php --p12=webhemi-php/var/certs/${DOMAIN}.p12"
