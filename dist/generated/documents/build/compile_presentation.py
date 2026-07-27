#!/usr/bin/env python3
from pathlib import Path
import subprocess
import sys

root = Path(__file__).resolve().parent.parent
builder = root / 'build_html_presentation.py'
if not builder.exists():
    raise SystemExit('Builder not found: build_html_presentation.py')

cmd = [sys.executable, str(builder)]
print('Running:', ' '.join(cmd))
result = subprocess.run(cmd, cwd=root)
raise SystemExit(result.returncode)
