#!/usr/bin/env python3
"""
Download the public HTML of the CHEM-CONFLUX'26 microsite for offline reference
(https://mnnit.ac.in/chemconflux26/). Many MNNIT conference pages load extra
content with JavaScript; this saves the initial HTML only.

Usage (from project root):
  python tools/scrape_chemconflux_reference.py
"""

from pathlib import Path
import urllib.request

URL = "https://mnnit.ac.in/chemconflux26/"
OUT = Path(__file__).resolve().parent / "chemconflux26_reference.html"


def main() -> None:
    req = urllib.request.Request(
        URL,
        headers={"User-Agent": "SHMS2026-site-tools/1.0 (reference scrape)"},
    )
    with urllib.request.urlopen(req, timeout=30) as resp:
        data = resp.read()
    OUT.write_bytes(data)
    print(f"Wrote {len(data)} bytes to {OUT}")


if __name__ == "__main__":
    main()
