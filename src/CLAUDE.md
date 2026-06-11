# Joomla Module Development Guide

This directory contains the Joomla extension source code.

## Module:

mod_amu_ipinfo

## Supported platforms:

* Joomla 5.x
* Joomla 6.x
* PHP 8.1+

## Architecture:

* mod_amu_ipinfo.php is the module bootstrap.
* helper.php contains business logic.
* tmpl/default.php contains rendering logic.
* language/ contains translations.
* media/ contains CSS and client-side assets.

## Development rules:

* Follow Joomla Coding Standards.
* Keep business logic out of template files.
* Use Joomla APIs whenever possible.
* Avoid APIs marked as deprecated.
* Use language constants instead of hardcoded text.
* Preserve multilingual support.

## IP Detection Rules:

The module supports:

* REMOTE_ADDR
* X-Forwarded-For
* Cloudflare headers
* Reverse proxies
* Load balancers
* IPv4
* IPv6

Do not simplify proxy handling without understanding all supported scenarios.

### When modifying IP detection:

* Preserve IPv4 support.
* Preserve IPv6 support.
* Preserve proxy chain traversal.
* Never assume a single proxy configuration.

## Geo IP Functionality:

Geo IP lookups are optional.

Supported providers include:

* ip-api
* ipwhois
* ipapi.co
* ipgeolocation.io
* abstractapi

Requirements:

* Handle provider outages gracefully.
* Never break module rendering when API calls fail.
* Continue using Joomla caching.
* Cache Geo IP responses whenever possible.

## Joomla Development Guidelines

### Coding Standards

Follow:

* Joomla Coding Standards
* PSR-12 where applicable

### Security

Always:

```php
defined('_JEXEC') or die;
```

* Validate all external input.
* Do not trust proxy headers without validation.
* Be careful when displaying external API data.

## CSS

Styles are located in:

```text
media/style.css
```

Requirements:

* Keep CSS template-independent.
* Preserve dark-mode support.
* Continue using CSS custom properties where possible.

## Versioning

Version number is stored in:

```text
src/mod_amu_ipinfo/mod_amu_ipinfo.xml
```

The version is automatically updated by the release workflow.

Do not manually edit the version during normal development unless preparing a release.

## Language Files

Supported languages:

* en-GB
* nl-NL

Whenever adding new text:

1. Add language keys to both languages.
2. Avoid hardcoded strings in PHP.
3. Use Joomla translation constants.

## Testing checklist:

* Joomla 5 installation
* Joomla 6 installation
* IPv4 lookup
* IPv6 lookup
* Cloudflare proxy
* Language switching
* Mobile rendering
