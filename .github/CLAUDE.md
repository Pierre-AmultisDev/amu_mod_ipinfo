# GitHub Actions and Release Automation

This directory contains release automation.

## Main workflow:

workflows/build-release.yml

## Purpose:

* Build Joomla release ZIP.
* Generate update.xml.
* Generate changelog.xml.
* Create GitHub Release.
* Publish update metadata.

## Important files:

.github/workflows/build-release.yml
.github/scripts/update_xml.py

## Release process:

1. Read version information.
2. Update manifest version.
3. Build Joomla package.
4. Generate update metadata.
5. Commit generated metadata.
6. Create GitHub Release.
7. Upload release assets.

## Rules:

* Do not manually edit generated update.xml files.
* Preserve release workflow compatibility.
* Preserve semantic versioning.
* Preserve GitHub Release generation.
* Preserve automatic changelog generation.

### When modifying the workflow:

* Validate YAML syntax.
* Ensure generated ZIP remains Joomla-installable.
* Ensure update server URLs remain correct.
* Ensure update_xml.py continues to function.

## Testing Checklist

Before release:

* Install on Joomla 5.
* Install on Joomla 6.
* Verify language switching.
* Verify IPv4 detection.
* Verify IPv6 detection.
* Verify private IP handling.
* Verify Cloudflare support.
* Verify Geo IP providers.
* Verify update.xml generation.
* Verify installable ZIP creation.
