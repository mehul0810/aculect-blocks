# Aculect Blocks Release Guide

## Release policy

Prepare releases on a clean, reviewed branch. Creating a tag, GitHub release,
or public distribution upload requires explicit owner approval; this guide only
prepares and validates a release candidate.

The plugin header is the public version source. Keep the bootstrap constant,
WordPress.org stable tag, and `package.json` version aligned.

## Candidate checks

Run the following from the plugin root:

```bash
composer validate --strict
composer lint
composer phpstan
composer test
npm ci
npm run lint
```

Before handoff, create a clean ZIP that contains only runtime plugin files.
Exclude `vendor`, `node_modules`, tests, CI files, and local Studio data. The
plugin fallback autoloader means the distributable ZIP does not require the
Composer development dependencies.

## WordPress proof

Install the ZIP in a clean WordPress Studio site and confirm activation,
Settings > Aculect Blocks saves successfully, public patterns insert cleanly,
and enabled schema is generated only from visible supported core-block content.
Run the proof with Aculect Theme and a generic block theme to confirm the plugin
remains portable. Record the WordPress, PHP, Node, and active SEO-plugin
versions with the candidate evidence.

## Approval and rollback

Request owner approval only after validation and Studio proof are attached to
the candidate. If package or runtime proof fails, do not tag or publish; fix
the candidate, repeat the checks, and prepare a new archive.
