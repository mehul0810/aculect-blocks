# Aculect Blocks

Aculect Blocks extends WordPress core blocks with enterprise-ready variations,
patterns, styles, and integrations. The plugin is designed to work with any
block-based theme and should prefer core block capabilities before introducing
custom blocks.

## Current Features

- Adds a modular settings screen under Settings > Aculect Blocks.
- Registers portable style variations for core Group, Columns, List, and Button
  blocks.
- Loads scoped frontend and editor CSS for Aculect block variations.
- Adds Rank Math `FAQPage` schema for WordPress 6.9 core Accordion blocks.
- Preserves supported HTML in FAQ answers, matching Google Search's documented
  `Answer.text` support.
- Avoids duplicate output when Rank Math already has an `FAQPage` schema node.

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
