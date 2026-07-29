#!/usr/bin/env bash
set -euo pipefail

# Spustí CodeIgniter lokálny server a otvorí aplikáciu v prehliadači.
#
# Použitie:
#   ./startApp.sh
#   ./startApp.sh --host=127.0.0.1 --port=8080
#   ./startApp.sh --no-browser
#   ./startApp.sh --skip-ext-check
#   ./startApp.sh --php-bin=/usr/bin/php8.3

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_DIR="$ROOT_DIR/codei"

HOST="127.0.0.1"
PORT="8080"
NO_BROWSER=false
SKIP_EXT_CHECK=false
PHP_BIN="${PHP_BIN:-}"

for arg in "$@"; do
  case "$arg" in
    --host=*)
      HOST="${arg#*=}"
      ;;
    --port=*)
      PORT="${arg#*=}"
      ;;
    --no-browser)
      NO_BROWSER=true
      ;;
    --skip-ext-check)
      SKIP_EXT_CHECK=true
      ;;
    --php-bin=*)
      PHP_BIN="${arg#*=}"
      ;;
    -h|--help)
      cat <<'EOF'
Usage: ./startApp.sh [--host=IP] [--port=PORT] [--no-browser] [--skip-ext-check] [--php-bin=PATH]

Spustí CodeIgniter server v adresári codei/ a otvorí URL v prehliadači.

Voľby:
  --host=IP      Predvolené: 127.0.0.1
  --port=PORT    Predvolené: 8080
  --no-browser   Neotvorí prehliadač
  --skip-ext-check
                 Preskočí kontrolu povinných PHP extensionov (intl, mbstring)
  --php-bin=PATH Použije konkrétny PHP binár (napr. /usr/bin/php8.3)
EOF
      exit 0
      ;;
    *)
      echo "Unknown argument: $arg" >&2
      echo "Use --help for usage." >&2
      exit 1
      ;;
  esac
done

if [[ ! -f "$APP_DIR/spark" ]]; then
  echo "Error: missing CodeIgniter launcher at $APP_DIR/spark" >&2
  exit 1
fi

if [[ -z "$PHP_BIN" ]]; then
  if command -v php >/dev/null 2>&1; then
    PHP_BIN="$(command -v php)"
  fi
fi

if [[ -n "$PHP_BIN" && ! -x "$PHP_BIN" ]]; then
  echo "Error: selected PHP binary is not executable: $PHP_BIN" >&2
  exit 1
fi

if [[ -z "$PHP_BIN" ]]; then
  echo "Error: php command is not available on PATH." >&2
  exit 1
fi

REQUIRED_EXTENSIONS=("intl" "mbstring")

missing_extensions_for() {
  local bin="$1"
  local missing=()
  local extension

  for extension in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! "$bin" -m | grep -qi "^${extension}$"; then
      missing+=("$extension")
    fi
  done

  echo "${missing[*]}"
}

if [[ "$SKIP_EXT_CHECK" == false ]]; then
  MISSING_EXTENSIONS_STR="$(missing_extensions_for "$PHP_BIN")"

  if [[ -n "$MISSING_EXTENSIONS_STR" ]]; then
    FALLBACK_CANDIDATES=("/usr/bin/php8.4" "/usr/bin/php8.3" "/usr/bin/php8.2")
    for candidate in "${FALLBACK_CANDIDATES[@]}"; do
      [[ -x "$candidate" ]] || continue
      CANDIDATE_MISSING="$(missing_extensions_for "$candidate")"
      if [[ -z "$CANDIDATE_MISSING" ]]; then
        echo "Info: switching PHP runtime to $candidate (missing in $PHP_BIN: $MISSING_EXTENSIONS_STR)"
        PHP_BIN="$candidate"
        MISSING_EXTENSIONS_STR=""
        break
      fi
    done
  fi

  if [[ -n "$MISSING_EXTENSIONS_STR" ]]; then
    echo "Error: missing required PHP extension(s): $MISSING_EXTENSIONS_STR" >&2
    echo "Active PHP binary: $PHP_BIN" >&2
    echo "CodeIgniter 4 requires these extensions to start." >&2
    echo "Install them for this PHP runtime, pass --php-bin=/path/to/php, or use --skip-ext-check." >&2
    exit 1
  fi
fi

if [[ "$SKIP_EXT_CHECK" == false ]]; then
  echo "Using PHP runtime: $PHP_BIN"
fi

cd "$APP_DIR"
APP_URL="http://${HOST}:${PORT}/"

echo "Starting CodeIgniter server at ${APP_URL}"
"$PHP_BIN" spark serve --host "$HOST" --port "$PORT" &
SERVER_PID=$!

if ! kill -0 "$SERVER_PID" >/dev/null 2>&1; then
  echo "Error: CodeIgniter server failed to start." >&2
  wait "$SERVER_PID" || true
  exit 1
fi

cleanup() {
  if kill -0 "$SERVER_PID" >/dev/null 2>&1; then
    kill "$SERVER_PID" >/dev/null 2>&1 || true
  fi
}
trap cleanup EXIT INT TERM

if [[ "$NO_BROWSER" == false ]]; then
  if [[ -n "${BROWSER:-}" ]]; then
    "$BROWSER" "$APP_URL" >/dev/null 2>&1 || true
  elif command -v xdg-open >/dev/null 2>&1; then
    xdg-open "$APP_URL" >/dev/null 2>&1 || true
  else
    echo "Browser launcher not found. Open manually: $APP_URL"
  fi
fi

wait "$SERVER_PID"