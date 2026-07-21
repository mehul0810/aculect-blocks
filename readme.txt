=== Aculect Blocks ===
Contributors: mehul0810
Tags: blocks, block styles, patterns, schema, breadcrumb, accordion
Requires at least: 7.0
Tested up to: 7.0
Requires PHP: 8.2
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Enhance WordPress core blocks with portable styles, practical patterns, and optional structured data from visible block content.

== Description ==

Aculect Blocks is a core-first block enhancement plugin. It improves daily website building workflows without locking a site into a custom block library.

Current 0.1.0 scope:

* Core Group styles for surfaces, cards, clickable cards, callouts, and hero sections.
* Core Columns, List, and Button styles for common site-building patterns.
* Authoring patterns for card grids, FAQ sections, and breadcrumb page headers.
* BreadcrumbList schema from rendered core Breadcrumbs blocks.
* FAQPage schema from visible core Accordion content.
* Duplicate protection when compatible SEO schema graphs already provide the same node.

FAQ schema is machine-readable structured data generated from visible Accordion content. It does not guarantee Google FAQ rich-result display.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/aculect-blocks`.
2. Activate Aculect Blocks from the Plugins screen.
3. Configure modules under Settings > Aculect Blocks.

== Frequently Asked Questions ==

= Does this plugin add custom blocks? =

No. Aculect Blocks starts by extending WordPress core blocks with styles, variations, patterns, and render-time enhancements.

= Does this replace an SEO plugin? =

No. Schema output is limited to block-derived BreadcrumbList and FAQPage data. Site-wide schema remains the responsibility of an SEO plugin or a dedicated schema plugin.

= Can FAQ schema guarantee Google rich results? =

No. The plugin can emit valid FAQPage structured data from visible Accordion content, but search engines decide how and whether to use it.

= Can developers customize schema output? =

Yes. Aculect Blocks provides the `aculect_blocks_enable_faq_schema`, `aculect_blocks_faq_schema_items`, and `aculect_blocks_enable_breadcrumb_schema` filters. Extensions must preserve the rule that schema comes from visible supported core-block content.

== Changelog ==

= 0.1.0 =

* Public MVP rework for core block enhancements, block-derived schema, patterns, and validation.
* Documents supported schema filters, compatibility, and first-release upgrade expectations.

== Upgrade Notice ==

= 0.1.0 =

First public release. Configure optional modules under Settings > Aculect Blocks after activation.
