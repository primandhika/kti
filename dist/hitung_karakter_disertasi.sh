#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")"
python3 scripts/compare_bab_characters.py

if [[ -t 0 ]]; then
  printf '\nTekan Enter untuk menutup...'
  read -r _
fi
