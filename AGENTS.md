# Aculect Blocks Contribution Rules

## Source of truth

Use this order when decisions conflict:

1. The current GitHub issue and its acceptance criteria.
2. Reviewed code and documentation on `main`.
3. This file and the repository release/testing guides.
4. Chat context or local runtime observations.

`main` is the only release source. A tag, GitHub release, WordPress.org upload,
or other public distribution always needs explicit owner approval.

## Working rules

- Start by checking the branch, worktree status, relevant issue, and open pull
  requests. Preserve unrelated local changes.
- Use a focused branch and one pull request per cohesive change. Rebase or
  merge the current `main` before handing off if CI requires it.
- Do not use destructive recovery commands, force-push, or alter a contributor's
  branch without explicit owner approval.
- Keep plugin behavior portable across block themes. Aculect Theme may improve
  the presentation, but this plugin must not depend on it.
- Extend core blocks with styles, variations, patterns, or render-time behavior
  before proposing a custom block.
- Schema belongs here only when it is derived from visible supported core-block
  content. Do not add generic site-wide schema.

## Evidence and handoff

- Run the checks named in `TESTING.md` and report the exact commands and result.
- For admin, editor, pattern, or frontend visual changes, attach the screenshot
  evidence required by `TESTING.md`; terminal output alone is insufficient.
- Build candidates from a clean reviewed head using `RELEASE.md`. Do not ship
  `vendor`, `node_modules`, tests, CI files, or local Studio files.
- Keep updates concise: source checked, change made, validation evidence,
  remaining blocker, and owner decision needed.

## Companion boundary

- **Aculect Blocks** owns portable core-block styles, patterns, variations,
  settings, and block-derived schema.
- **Aculect Theme** owns the optional site-wide FSE baseline, templates,
  `theme.json` tokens, and theme-only presentation.
- Child themes own their own `theme.json` and template overrides. Do not require
  a child theme to edit this plugin to make those changes.
