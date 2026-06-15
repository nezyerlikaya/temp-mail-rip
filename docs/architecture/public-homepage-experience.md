# Public Homepage Experience

The public homepage is a product experience, not a static landing page. It must keep the temporary mailbox usable while allowing safe theme, section, language, ad, font, and appearance customization.

## Themes

The homepage supports three distinct public themes:

- `atlas`: modern SaaS and premium landing composition.
- `horizon`: balanced centered temp-mail experience and default theme.
- `legacy`: compact classic temp-mail tool composition.

The themes may share Blade components and backend services, but they must not collapse into the same layout with different colors.

## Locked Core Experience

These sections are locked:

- header
- footer
- mailbox generator
- inbox preview

The mailbox generator and inbox preview are product-critical. They must remain visible in the primary public experience.

## Sections

Sections are registry-defined. Admins may enable, disable, and reorder allowed optional sections within theme guardrails. v1 does not include a page builder, arbitrary layout grid editor, custom HTML editor, custom CSS editor, or custom JavaScript editor.

Optional sections render only when valid public localized content exists. Empty Blog, FAQ, Plans, Documentation, Knowledge Base, CTA, or Ads shells are not allowed.

## Appearance

Appearance owns safe public presentation settings:

- theme selection
- brand and color tokens
- font presets
- homepage section configuration
- ad slots
- preview/publish state where implemented

All settings must be validated and fall back safely.

## Languages And Direction

Active locales appear publicly. Passive locales do not. Content is locale-specific, but theme selection is global in v1.

RTL/LTR direction comes from Localization. Homepage components must use logical alignment and spacing where practical so RTL languages do not break layout.

## Mailbox Controls

Mailbox controls must be explicit:

- Refresh checks for new messages only and never changes the address.
- Change validates username and active public domain selection.
- Delete requires confirmation, expires/deletes the current mailbox, and generates a replacement mailbox.
- History is scoped to a safe session/device/user context, never raw IP alone.
- Current can switch only to eligible active mailboxes.

Deleted, expired, quarantined, or retention-cleaned data must not be restored through public UI.

## Ads

Ads are slot-based and theme-aware. They must not render inside mailbox inputs, inbox rows, header internals, or footer internals. Empty slots do not render. Ad failures must not break the page.

## Loading States

A lightweight top loading bar may be used for mailbox actions, language switching, theme preview, and other bounded interactions. It must use Blade/Tailwind/Alpine, respect theme tokens, stop on success/failure/timeout, and respect `prefers-reduced-motion`.
