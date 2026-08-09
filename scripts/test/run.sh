#!/usr/bin/env bash
# Hub test runner: php | ui | (empty = both)
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
SUITE="${1:-}"

load_env() {
  local f
  for f in "$ROOT/.env" "$ROOT/webhemi-ui/.env"; do
    if [[ -f "$f" ]]; then
      set -a
      # shellcheck disable=SC1090
      source "$f"
      set +a
    fi
  done
}

run_php() {
  echo "==> PHPUnit (webhemi-php)"
  (cd "$ROOT/webhemi-php" && composer test:phpunit)
}

run_ui() {
  load_env
  echo "==> UI typecheck"
  (cd "$ROOT/webhemi-ui" && npm run typecheck)
  echo "==> UI lint"
  (cd "$ROOT/webhemi-ui" && npm run lint)
  echo "==> Playwright Chromium (Storybook Vitest)"
  (cd "$ROOT/webhemi-ui" && npx playwright install chromium)
  echo "==> Storybook Vitest"
  (cd "$ROOT/webhemi-ui" && npm run test-storybook -- --run)
  echo "==> Chromatic"
  if [[ -z "${CHROMATIC_PROJECT_TOKEN:-}" ]]; then
    echo "CHROMATIC_PROJECT_TOKEN is not set."
    echo "Add it to the hub .env (gitignored), e.g.:"
    echo "  CHROMATIC_PROJECT_TOKEN=chpt_…"
    echo "Token: Chromatic project → Manage → Configure → Project token"
    exit 1
  fi
  (cd "$ROOT/webhemi-ui" && npm run chromatic)
}

case "$SUITE" in
  php)
    run_php
    ;;
  ui)
    run_ui
    ;;
  "")
    run_php
    run_ui
    ;;
  *)
    echo "Unknown suite: $SUITE"
    echo "Usage: make test | make test php | make test ui"
    exit 1
    ;;
esac
