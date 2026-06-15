# Navigation And Theme

Navigation owns registered menu definitions, named-route references, hierarchy, ordering, localization-key labels, visibility resolution, active-route resolution, and menu composition. It does not own authorization, feature access, subscriptions, translations, routes, or page content.

Theme owns registered presentation tokens, public theme definitions, Blade theme context, and Tailwind-compatible CSS variables. It does not own page content, navigation, authorization, legal content, mailbox logic, ads business rules, or business data.

## Navigation Registry

Navigation items are registered in `NavigationRegistry` with stable keys, localization label keys, named routes, areas, order, optional parent keys, icon identifiers, optional feature flags, and bounded active-route patterns.

Initial areas are `public`, `guest`, `user`, and `admin`. STEP006 registers only `public.home` because `home` is the only current named application route.

Duplicate keys, missing parents, hierarchy cycles, excessive depth, unsupported areas, invalid icons, and unsafe active patterns fail explicitly.

## Visibility

Visibility may consume Feature Flags for operational availability. STEP006 does not implement auth, authorization, plans, subscriptions, or Feature Gates. Hidden navigation must never replace backend authorization.

## Localization

Labels are translation keys resolved by the Translation module. Navigation does not store translated labels. Locale must be part of menu resolution context and cache strategy.

## Public Theme System

The public homepage supports three distinct theme compositions:

- `atlas`: modern SaaS and premium landing composition.
- `horizon`: balanced centered temp-mail experience and default public theme.
- `legacy`: compact classic temp-mail tool composition.

These themes are not color-only skins. They may share Blade components and business services, but each theme owns its layout composition, spacing rhythm, hero treatment, mailbox presentation, inbox presentation, CTA treatment, optional section presentation, and footer density.

## Appearance Admin

Appearance owns safe presentation settings:

- Themes
- Brand / Colors
- Fonts
- Homepage Sections
- Ads

Appearance settings must be token- and registry-based. Page builders, arbitrary HTML, arbitrary CSS, arbitrary JavaScript, animation editors, and pixel-level ad placement are not part of v1.

## Theme Tokens

Themes define semantic tokens such as background, foreground, muted, border, focus, radius, and shadow. Arbitrary CSS and JavaScript-like values are rejected. CSS variables in `resources/css/app.css` provide Tailwind-compatible usage.

## Fonts And Direction

Fonts are selected through safe presets only. Font upload, arbitrary external font injection, and custom CSS font declarations are not part of v1.

Localization owns locale direction. Theme and Appearance consume that direction and must render `dir="ltr"` or `dir="rtl"` safely. Public layouts should use logical start/end alignment and spacing where practical so header, footer, mailbox controls, FAQ, ads, and CTA sections do not break in RTL.

## Homepage Sections

Homepage sections are registry-defined. Header, footer, mailbox generator, and inbox preview are locked core sections. Optional sections such as FAQ, Blog teaser, Plans teaser, Documentation teaser, Knowledge Base teaser, CTA, Trust/Security, and Ads render only when enabled and backed by valid public localized content. Empty shells are not allowed.

## Ads

Ads are slot-based. They may render only in approved theme slots and must not be injected into mailbox inputs, inbox rows, header internals, or footer internals. Empty slots do not render. Ad failures must not break the page.

## Theme Resolution

Resolution priority is published Appearance configuration, validated public theme key, then the application default from Settings. Horizon is the default public theme. Invalid theme keys must fall back safely.

## Blade/Tailwind/Alpine

STEP006 prepares minimal Blade layout and navigation components. Pages remain server-rendered and usable without JavaScript. Alpine may be used later for lightweight interaction only.

## Cache And Security

Navigation cache context must include area and locale, plus auth/permission/feature/subscription versions later. Do not cache rendered private HTML globally. Labels, attributes, URLs, and tokens must be escaped, icon identifiers restricted, and unavailable routes hidden.
