#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
PID_DIR="$ROOT/.dev/pids"

status_one() {
  local name="$1"
  local pid_file="$PID_DIR/${name}.pid"
  if [[ -f "$pid_file" ]] && kill -0 "$(cat "$pid_file")" 2>/dev/null; then
    echo "[$name] running (pid $(cat "$pid_file"))"
  else
    echo "[$name] stopped"
  fi
}

status_one storybook
status_one ui-watch
status_one php-server

if command -v symfony >/dev/null 2>&1; then
  echo "[symfony]"
  symfony server:status --dir="$ROOT/webhemi-php" 2>/dev/null || echo "  (no Symfony server)"
fi
