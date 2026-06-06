# AMU IP Info — Joomla Module

[![Latest Release](https://img.shields.io/github/v/release/Pierre-AmultisDev/amu_mod_ipinfo?label=release)](https://github.com/Pierre-AmultisDev/amu_mod_ipinfo/releases/latest)
[![Joomla 5+](https://img.shields.io/badge/Joomla-5%20%7C%206-blue)](https://www.joomla.org)
[![PHP 8.1+](https://img.shields.io/badge/PHP-8.1%2B-8892BF)](https://www.php.net)
[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-green)](LICENSE)

Displays the visitor's IP address (IPv4 / IPv6) with optional Geo IP lookup.  
Built and maintained by [aMultis](https://amultis.eu).

---

## Features

- Detects **IPv4**, **IPv6**, and dual-stack addresses
- Traverses the full proxy header chain (Cloudflare, Nginx, X-Forwarded-For, …)
- Copy-to-clipboard button
- IPv4 / IPv6 version badge
- Optional **Geo IP** with 5 free providers (see table below)
- Country flag, timezone, ISP, ASN, map link
- 10-minute Geo IP caching
- Dutch (nl-NL) + English (en-GB) included
- CSS custom properties — fits any template
- Automatic dark mode (`prefers-color-scheme`)
- Joomla 5.x & 6.x · PHP 8.1+

---

## Installation

Download the latest ZIP from [Releases](https://github.com/Pierre-AmultisDev/amu_mod_ipinfo/releases/latest) and install via **Extensions → Manage → Install** in Joomla.

---

## Automatic Updates

The module registers two update servers. Joomla will notify you of new versions via **Extensions → Update**:

| Priority | URL |
|---|---|
| 1 — primary | `https://update.amultis.dev/joomla/modules/amu_ipinfo/update.xml` |
| 2 — fallback | `https://raw.githubusercontent.com/Pierre-AmultisDev/amu_mod_ipinfo/main/.joomla/update.xml` |

---

## Geo IP Providers

| Provider | API key? | Free limit |
|---|---|---|
| [ip-api.com](https://ip-api.com) | No | 45 req/min |
| [ipwhois.app](https://ipwhois.app) | No | 10,000 req/month |
| [ipapi.co](https://ipapi.co) | No | 1,000 req/day |
| [ipgeolocation.io](https://ipgeolocation.io) | Yes (free) | 1,000 req/day |
| [abstractapi.com](https://app.abstractapi.com) | Yes (free) | 20,000 req/month |

---

## Repository Layout

```
amu_mod_ipinfo/
├── .github/
│   └── workflows/
│       └── build-release.yml   # CI: ZIP + update.xml + GitHub Release + deploy
├── .joomla/
│   └── update.xml              # Auto-updated by CI — Joomla fallback update server
├── src/
│   └── mod_amu_ipinfo/         # All installable module source files
│       ├── mod_amu_ipinfo.php
│       ├── mod_amu_ipinfo.xml
│       ├── helper.php
│       ├── tmpl/default.php
│       ├── media/style.css
│       └── language/
│           ├── en-GB/
│           └── nl-NL/
├── CHANGELOG.md
├── LICENSE
└── README.md
```

---

## Development Workflow

### New release

```bash
# 1. Make your changes in src/mod_amu_ipinfo/
# 2. Update CHANGELOG.md
# 3. Commit and tag
git add .
git commit -m "feat: your change description"
git tag v1.2.3
git push origin main --tags
```

GitHub Actions will automatically:
1. Patch the version number in `mod_amu_ipinfo.xml`
2. Build `mod_amu_ipinfo_v1.2.3.zip`
3. Regenerate `.joomla/update.xml` and commit it back
4. Create a GitHub Release with the ZIP attached
5. Upload the ZIP to `download.amultis.dev` via SSH
6. Write the new `update.xml` to `update.amultis.dev` via SSH

### Required GitHub Secrets

Configure these under **Settings → Secrets and variables → Actions**:

| Secret | Description |
|---|---|
| `DEPLOY_HOST` | Hostname/IP of your server |
| `DEPLOY_USER` | SSH username |
| `DEPLOY_SSH_KEY` | Private SSH key (RSA or Ed25519) |
| `DEPLOY_PORT` | SSH port (default: 22) |
| `DEPLOY_PATH_MODULES` | Base path for downloads, e.g. `/var/www/download.amultis.dev/joomla/modules` or `/domains/amultis.dev/public_html/downnload/joomla/modules/amu_ipinfo` |
| `DEPLOY_PATH_UPDATES` | Base path for update XMLs, e.g. `/var/www/update.amultis.dev` or `/domains/amultis.dev/public_html/update/joomla/modules/amu_ipinfo` |

---

## License

GNU General Public License version 2 or later.  
See [LICENSE](LICENSE).
