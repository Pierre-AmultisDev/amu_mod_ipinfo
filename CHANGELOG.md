# Changelog — mod_amu_ipinfo

All notable changes to this project are documented in this file.  
Format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).  
Versioning follows [Semantic Versioning](https://semver.org/).

---

## [1.0.0] — 2024-01-01

### Added
- Initial release
- IPv4, IPv6, and dual-stack IP address detection
- Reverse-proxy header chain support (Cloudflare, Nginx, X-Forwarded-For)
- Private / loopback IP detection with configurable notice
- Copy-to-clipboard button
- IPv4 / IPv6 version badge
- Geo IP support with 5 free providers:
  - ip-api.com (no key, 45 req/min)
  - ipwhois.app (no key, 10,000 req/month)
  - ipapi.co (no key, 1,000 req/day)
  - ipgeolocation.io (free API key, 1,000 req/day)
  - abstractapi.com (free API key, 20,000 req/month)
- Country flag display via flagcdn.com
- OpenStreetMap coordinate link
- ISP and ASN display
- 10-minute Geo IP result caching
- Dutch (nl-NL) and English (en-GB) translations
- CSS custom properties for template theming
- Automatic dark mode via `prefers-color-scheme`
- Joomla update server support:
  - Primary: https://update.amultis.dev/joomla/modules/amu_ipinfo/update.xml
  - Fallback: GitHub raw (.joomla/update.xml)
- Joomla 5.x and 6.x compatibility
- PHP 8.1+ requirement
