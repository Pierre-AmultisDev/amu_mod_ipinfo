# Repository Overview

This repository contains the Joomla module mod_amu_ipinfo.

## Purpose:

* Display visitor IP information.
* Support IPv4 and IPv6.
* Support reverse proxies and Cloudflare.
* Optionally enrich information using Geo IP services.

## Repository sections:

* src/ contains the Joomla extension source code.
* .github/ contains release automation.
* update/ contains update server metadata.

### Important Architecture Rules

#### Installable Package

Only the contents of:

```text
src/mod_amu_ipinfo/
```

are included in the Joomla installation ZIP.

Files outside this folder are development and release tooling.

## Important:

* The Joomla extension code lives entirely inside src/mod_amu_ipinfo.
* GitHub Actions automatically build release packages.
* update.xml and changelog.xml are generated automatically.
* Follow the CLAUDE.md files in subdirectories for detailed instructions.

## General rules:

* Maintain Joomla 5 and Joomla 6 compatibility.
* Avoid introducing unnecessary dependencies.
* Preserve release automation.
* Preserve update server compatibility.

## AI Assistant Guidance

When proposing code changes:

* Prefer Joomla APIs over raw PHP alternatives.
* Maintain backwards compatibility.
* Keep templates simple.
* Do not remove update server support.
* Do not remove GitHub Actions automation.
* Do not introduce framework dependencies.
* Minimize external package usage.
* Preserve current repository structure.
