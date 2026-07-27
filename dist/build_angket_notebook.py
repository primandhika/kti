#!/usr/bin/env python3
from pathlib import Path
import runpy
import sys
root = Path(__file__).resolve().parent
target = root / 'build' / 'build_angket_notebook.py'
if not target.exists():
    raise SystemExit('Target builder not found')
runpy.run_path(str(target), run_name='__main__')
