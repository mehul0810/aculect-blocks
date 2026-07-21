# Testing Guide

Use this guide for Aculect Blocks validation before merging any change that
touches block styles, patterns, schema output, editor behavior, or the plugin
admin screen.

## Test Layers

- `composer test` covers unit-level PHP behavior: schema builders, duplicate
detection, block-style registration, pattern registration, FAQ extraction,
and Breadcrumb schema output.
- `composer syntax`, `composer lint`, and `composer phpstan` cover PHP syntax,
coding standards, and static analysis.
- `npm run lint` covers the editor JS and CSS entry points.
- Studio runtime checks cover WordPress behavior that cannot be proven by static
analysis alone: settings screens, editor rendering, frontend output, and
schema emission.

Do not treat static checks as a substitute for runtime proof when the change
affects the editor canvas, admin UI, or public rendering.

## Local Commands

Run the repo commands from the plugin root:

```bash
composer validate --strict
composer install
composer syntax
composer test
composer lint
composer phpstan
npm ci
npm run lint
```

The CI workflow runs the same checks on PHP 8.2, 8.3, and 8.4 plus Node 24.
Use the same command names locally so failures line up with CI output.

## Runtime Checks

Use an isolated `wp-env`, Docker, or WordPress Playground environment for
release proof. A Studio site may be used only when the owner has explicitly
designated it as a persistent proof environment. Replace `<site-path>` with the
approved proof site's path when using Studio:

```bash
studio wp --path=<site-path> plugin list --status=active --field=name
studio wp --path=<site-path> option get home
studio wp --path=<site-path> option get siteurl
studio wp --path=<site-path> eval 'echo wp_is_block_theme() ? "block-theme" : "classic-theme";'
```

Use additional Studio checks when needed for the touched surface:

```bash
studio wp --path=<site-path> post get <page-id> --field=post_status
studio wp --path=<site-path> post get <page-id> --field=guid
studio wp --path=<site-path> plugin check aculect-blocks
```

If Plugin Check is not installed or cannot run in the current Studio setup,
record the blocker and keep the release issue open until the check is run in an
environment that supports it.

## Runtime Coverage Expectations

Use static and runtime proof together:

- Static coverage proves the code shape, syntax, and expected registration
  surface.
- Runtime coverage proves the plugin behaves correctly inside WordPress.
- Editor proof is required when a change affects block variations, inspector
  controls, inserted patterns, or the Site Editor.
- Frontend proof is required when a change affects visible rendering, layout,
  clickable cards, breadcrumb output, or schema that depends on rendered block
  content.

For 0.1.0, the minimum runtime matrix is:

- plugin activation without fatal errors,
- Settings > Aculect Blocks loads for an authorized user,
- module toggles save and persist sanitized boolean values,
- clickable card variations render and remain editable,
- patterns stay core-block-only and editable,
- Breadcrumb schema emits one `BreadcrumbList` node when enabled and does not
  duplicate graph output when Rank Math or Yoast already provide it,
- Accordion schema emits `FAQPage` only from complete visible rows.

## Browser and Screenshot Evidence

When a change is visually meaningful, capture screenshots from the approved
runtime instead of relying on terminal output alone. This is mandatory for
acceptance of changes to settings, editor guidance, block styles, patterns, or
frontend rendering. Schema-only code needs reproducible rendered-output proof;
capture a screenshot too when a UI control changes its behavior.

Capture at least these desktop views:

- admin/settings surface,
- editor or block editor surface,
- frontend rendering surface.

Capture a mobile viewport for each changed frontend pattern or block style, and
for any responsive admin/editor layout touched by the change. The screenshots
must show the browser chrome or enough surrounding WordPress UI to identify the
surface, and they must be attached to the pull request or linked from the issue.

For a release candidate, retain one evidence set covering the settings screen,
an inserted pattern in the editor, the pattern on the frontend, and the
relevant schema output. If a required surface cannot be captured, record the
specific runtime blocker and keep the issue open.

Use proof artifacts that show the full affected surface and enough surrounding
UI to confirm context. If a surface is partially loaded or blocked, do not claim
visual parity.

## Local HTTPS-Signal Recovery

The Studio proof environment has previously canonicalized `http://localhost:8896`
to `https://localhost:8896` during browser/admin proof even while `home` and
`siteurl` remained HTTP. That is a runtime transport issue, not a product-code
behavior change.

Proven recovery steps for proof-only sessions:

1. Back up `wp-config.php` before touching runtime config.
2. Apply the narrowest local-only override needed to clear the HTTPS signal for
   localhost proof requests.
3. Recheck the affected URLs with `curl -I` and `studio wp` before opening the
   browser.
4. Capture screenshots.
5. Restore the temporary change immediately after proof.

The reversible override used during recovery was:

- clear `$_SERVER['HTTPS']`,
- set `$_SERVER['SERVER_PORT']` to `80`,
- unset `HTTP_X_FORWARDED_PROTO` and `HTTP_X_FORWARDED_SSL`
  for localhost proof hosts only.

Do not keep that override in place unless the product thread explicitly decides
it should remain a local-only runtime rule for the shared Studio instance.
