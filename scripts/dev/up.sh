#!/usr/bin/env bash
# Start Storybook, UI build-watch+sync, and Symfony local server.
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
PID_DIR="$ROOT/.dev/pids"
LOG_DIR="$ROOT/.dev/logs"
mkdir -p "$PID_DIR" "$LOG_DIR"

is_running() {
  local pid_file="$1"
  [[ -f "$pid_file" ]] || return 1
  local pid
  pid="$(cat "$pid_file")"
  kill -0 "$pid" 2>/dev/null
}

start_bg() {
  local name="$1"
  shift
  local pid_file="$PID_DIR/${name}.pid"
  local log_file="$LOG_DIR/${name}.log"

  if is_running "$pid_file"; then
    echo "[$name] already running (pid $(cat "$pid_file"))"
    return 0
  fi

  echo "[$name] starting..."
  (
    cd "$ROOT"
    "$@"
  ) >"$log_file" 2>&1 &
  echo $! >"$pid_file"
  echo "[$name] pid $(cat "$pid_file") — log: $log_file"
}

# Initial UI build + sync so PHP has assets before the server starts
echo "[ui-sync] initial build..."
(
  cd "$ROOT/webhemi-ui"
  npm run build
  cd "$ROOT/webhemi-php"
  bash bin/sync-ui.sh
) >"$LOG_DIR/ui-sync-initial.log" 2>&1
echo "[ui-sync] done — see $LOG_DIR/ui-sync-initial.log"

# Storybook (UI design system)
start_bg storybook \
  bash -c "cd webhemi-ui && npm run storybook -- --host 127.0.0.1 --port 6006 --no-open"

# Watch UI sources → rebuild → sync into webhemi-php AssetMapper
start_bg ui-watch \
  bash -c '
    npx --yes chokidar-cli \
      "webhemi-ui/src/**/*" \
      "webhemi-ui/package.json" \
      -c "cd webhemi-ui && npm run build && cd ../webhemi-php && bash bin/sync-ui.sh" \
      --initial=false \
      --debounce 400
  '

# Symfony CLI server (daemon). --allow-http avoids forced HTTPS redirect for local hostnames.
# Admin UI: http://127.0.0.1:8000/login or http://admin.webhemi.local:8000/login (after /etc/hosts)
if command -v symfony >/dev/null 2>&1; then
  echo "[symfony] starting daemon..."
  symfony server:stop --dir="$ROOT/webhemi-php" >/dev/null 2>&1 || true
  symfony server:start -d \
    --dir="$ROOT/webhemi-php" \
    --port=8000 \
    --allow-http \
    --no-tls
  echo "[symfony] http://127.0.0.1:8000  (login: /login , admin: /admin)"
else
  start_bg php-server \
    bash -c "cd webhemi-php && php -S 127.0.0.1:8000 -t public"
fi

echo
echo "Dev stack is up."
echo "  Storybook:  http://127.0.0.1:6006"
echo "  PHP app:    http://127.0.0.1:8000/login"
echo "  Admin host: http://admin.webhemi.local:8000/login  (needs hosts entry — see docs/local-dev.md)"
echo "  Logs:       $LOG_DIR"
echo "  Stop with:  make down"
