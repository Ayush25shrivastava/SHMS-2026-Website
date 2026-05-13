# SHMS‑2026 site tools

## `scrape_chemconflux_reference.py`

Fetches the initial HTML from [CHEM‑CONFLUX’26](https://mnnit.ac.in/chemconflux26/) into `tools/chemconflux26_reference.html` for offline comparison (layout ideas, copy patterns). The live site may rely on JavaScript for full content; the saved file is only the first response.

```bash
python tools/scrape_chemconflux_reference.py
```

Do not commit scraped third-party content unless your license/policy allows it.
