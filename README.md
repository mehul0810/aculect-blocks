# Aculect Blocks

Aculect Blocks extends WordPress core blocks with portable styles, practical
authoring patterns, and optional structured data generated from visible block
content. The plugin is designed to work with block-based themes and prefers core
block capabilities before introducing custom blocks.

## Current Features

- Adds a modular settings screen under Settings > Aculect Blocks.
- Registers portable style variations for core Group, Columns, List, and Button
  blocks.
- Adds a core Group variation for clickable cards using one stretched Button
  link.
- Registers daily-workflow patterns for card grids, FAQ sections, and breadcrumb
  page headers.
- Loads scoped frontend and editor CSS for Aculect block variations.
- Adds `FAQPage` schema for WordPress core Accordion blocks.
- Preserves supported HTML in FAQ answers, matching Google Search's documented
  `Answer.text` support.
- Avoids duplicate output when Rank Math already has an `FAQPage` schema node.
- Adds `BreadcrumbList` schema from rendered WordPress 7.0 core Breadcrumbs
  blocks and avoids duplicate output when Rank Math already provides it.

Google currently limits FAQ rich-result eligibility to well-known,
authoritative government or health sites. This plugin emits valid structured
data from visible Accordion content, but Google still decides whether a rich
result is shown.

## Development

```bash
composer install
npm install
composer lint
composer phpstan
npm run lint
```

Custom blocks should only be added after verifying that core blocks, block
styles, block variations, patterns, and `theme.json` support cannot satisfy the
use case.

## Architecture Principles

- Use block styles for portable visual treatments.
- Use block variations and patterns to speed up common authoring workflows.
- Generate schema only from visible core block content.
- Prefer SEO plugin graph integration when available and standalone JSON-LD only
  as a fallback.
- Do not add generic site-wide schema in this plugin unless it is derived from a
  core block enhancement.
