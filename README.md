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
composer syntax
composer lint
composer phpstan
composer test
npm ci
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

## Module Boundaries

- `includes/Blocks/` registers portable core-block styles and patterns.
- `src/index.js` registers the editor-only clickable-card variation.
- `includes/Settings/` owns the sanitized module settings and defaults.
- `includes/Admin/` owns the capability-protected Settings > Aculect Blocks
  screen.
- `includes/Integrations/` and `includes/StructuredData/` derive FAQPage and
  BreadcrumbList data from visible supported core-block output.
- `includes/Schema/` outputs one JSON-LD node only when a compatible SEO graph
  has not already supplied it.
- `includes/Assets/` loads scoped frontend, editor, and admin assets.

Aculect Theme is optional. It owns site-wide FSE templates and design tokens;
this plugin owns portable enhancements and must work with other block themes.

## Requirements and Compatibility

- WordPress 7.0 or later
- PHP 8.2 or later
- Node 20.10 or later and npm 10.2.3 or later for development

The plugin supports block themes. It uses core block capabilities and does not
provide a custom block library. Rank Math is detected for duplicate FAQPage and
BreadcrumbList schema protection; the plugin otherwise falls back to scoped
JSON-LD from visible block content.

## Filters

These filters are public extension points. Filter callbacks must preserve the
visible-content rule and should not add unrelated site-wide schema.

- `aculect_blocks_enable_faq_schema` receives `bool $enabled` and can enable or
  disable FAQPage output.
- `aculect_blocks_faq_schema_items` receives `array $items, int $post_id` and
  can adjust the complete FAQ items derived from a rendered Accordion block.
- `aculect_blocks_enable_breadcrumb_schema` receives `bool $enabled` and can
  enable or disable BreadcrumbList output.

## 0.1.0 Upgrade Notes

This is the first public release. Install it as a new plugin and configure
modules under Settings > Aculect Blocks. Do not rely on internal PHP classes as
extension points; the filters above are the supported 0.1.0 contract.
