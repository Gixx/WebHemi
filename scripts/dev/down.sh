#!/usr/bin/env bash
# Stop processes started by scripts/dev/up.sh
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
PID_DIR="$ROOT/.dev/pids"

stop_pid_file() {
  local name="$1"
  local pid_file="$PID_DIR/${name}.pid"
  if [[ ! -f "$pid_file" ]]; then
    echo "[$name] not running (no pid file)"
    return 0
  fi
  local pid
  pid="$(cat "$pid_file")"
  if kill -0 "$pid" 2>/dev/null; then
    echo "[$name] stopping pid $pid..."
    # Kill process group if possible (children of storybook/npx)
    kill -TERM "-$pid" 2>/dev/null || kill -TERM "$pid" 2>/dev/null || true
    sleep 0.5
    if kill -0 "$pid" 2>/dev/null; then
      kill -KILL "-$pid" 2>/dev/null || kill -KILL "$pid" 2>/dev/null || true
    fi
    echo "[$name] stopped"
  else
    echo "[$name] stale pid file (process gone)"
  fi
  rm -f "$pid_file"
}

if command -v symfony >/dev/null 2>&1; then
  echo "[symfony] stopping..."
  symfony server:stop --dir="$ROOT/webhemi-php" >/dev/null 2>&1 || true
fi

stop_pid_file storybook
stop_pid_file ui-watch
stop_pid_file php-server

# Extra cleanup: Storybook/vite sometimes leave orphans on the ports
for port in 6006 8000; do
  pids="$(lsof -t -iTCP:"$port" -sTCP:LISTEN 2>/dev/null || true)"
  if [[ -n "${pids:-}" ]]; then
    echo "[cleanup] freeing port $port (pids: $pids)"
    # shellcheck disable=SC2086
    kill -TERM $pids 2>/dev/null || true
  fi
done

echo "Dev stack is down."
