# Navigation And Theme

Navigation owns registered menu definitions, named-route references, hierarchy, ordering, localization-key labels, visibility resolution, active-route resolution, and menu composition. It does not own authorization, feature access, subscriptions, translations, routes, or page content.

Theme owns registered presentation tokens, light/dark/system resolution, Blade theme context, and Tailwind-compatible CSS variables. It does not own page content, navigation, authorization, CMS blocks, legal content, or business data.

## Navigation Registry

Navigation items are registered in `NavigationRegistry` with stable keys, localization label keys, named routes, areas, order, optional parent keys, icon identifiers, optional feature flags, and bounded active-route patterns.

Initial areas are `public`, `guest`, `user`, and `admin`. STEP006 registers only `public.home` because `home` is the only current named application route.

Duplicate keys, missing parents, hierarchy cycles, excessive depth, unsupported areas, invalid icons, and unsafe active patterns fail explicitly.

## Visibility

Visibility may consume Feature Flags for operational availability. STEP006 does not implement auth, authorization, plans, subscriptions, or Feature Gates. Hidden navigation must never replace backend authorization.

## Localization

Labels are translation keys resolved by the Translation module. Navigation does not store translated labels. Locale must be part of menu resolution context and cache strategy.

## Theme Tokens

Themes define semantic tokens such as background, foreground, muted, border, focus, radius, and shadow. Arbitrary CSS and JavaScript-like values are rejected. CSS variables in `resources/css/app.css` provide Tailwind-compatible usage.

## Theme Resolution

Resolution priority is future user preference, validated cookie preference, browser system preference when `system` is selected, then application default from Settings. No theme history is stored.

## Blade/Tailwind/Alpine

STEP006 prepares minimal Blade layout and navigation components. Pages remain server-rendered and usable without JavaScript. Alpine may be used later for lightweight interaction only.

## Cache And Security

Navigation cache context must include area and locale, plus auth/permission/feature/subscription versions later. Do not cache rendered private HTML globally. Labels, attributes, URLs, and tokens must be escaped, icon identifiers restricted, and unavailable routes hidden.
