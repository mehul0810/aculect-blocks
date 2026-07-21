# Aculect Blocks Design Baseline

## Default system

Use the [WordPress Design Handbook](https://make.wordpress.org/design/handbook/)
and the [Block Editor Handbook](https://developer.wordpress.org/block-editor/)
as the default design system for WordPress admin and editor work. Prefer native
WordPress controls, familiar labels, normal admin spacing, and core block
supports before introducing a new visual language.

Aculect Blocks augments those surfaces; it does not replace them with a branded
application shell. Keep controls understandable without JavaScript-only
interactions and retain normal keyboard, focus, and reduced-motion behavior.

## Aculect visual language

- **Primary accent:** `#1d4ed8` for positive emphasis, selected states, and
  focused Aculect-only controls; never use it as the only indication of state.
- **Ink and muted text:** `#1d2327` and `#646970`, aligned with WordPress admin
  contrast expectations.
- **Surfaces and borders:** white or WordPress neutral surfaces with restrained
  `#dcdcde` borders, 8px radii, and modest shadows only when they establish a
  clear grouping.
- **Block styles:** consume `theme.json` presets first, with fallbacks only to
  keep generic block themes legible. Do not assume Aculect Theme tokens exist.
- **Motion:** use short, non-essential transitions and avoid motion that hides
  focus or makes content harder to read.

## Surface rules

- Settings use WordPress-native forms and buttons. Explain modules in plain
  language and avoid marketing, onboarding, or artificial urgency.
- Editor guidance must help an author complete a task without masking the core
  block UI. Important actions belong in the content surface or block toolbar;
  advanced controls may live in the settings sidebar.
- Patterns use core blocks only and remain readable with long text, missing
  media, and narrower viewports.
- Frontend styles stay scoped to their Aculect style class. They must preserve
  the active theme's typography, colors, spacing controls, and child-theme
  overrides.

## Visual review

Follow `TESTING.md` for visual evidence. Any material change to the settings
screen, editor guidance, patterns, block styles, or frontend rendering requires
desktop and mobile proof from a real WordPress runtime before the related issue
can close.
