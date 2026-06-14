# Admin Shell

The Admin module owns the admin Blade layout, admin page shell, admin navigation composition, breadcrumbs, flash/alert presentation, safe empty states, and dashboard shell preparation.

It does not own authentication, authorization, staff records, business modules, settings definitions, navigation definitions owned by other modules, theme definitions, audit storage, monitoring, mailbox content, or message content.

## Routes

Admin routes live in `routes/admin.php`, use the `admin.` route-name prefix, and use the URL prefix from `config/admin.php`. The initial shell route is `admin.dashboard`.

Authentication and authorization are not implemented yet. The temporary shell middleware blocks production access until STEP032 and STEP033 provide real protection. There are no fake role checks, hardcoded admin emails, or business management routes.

## Layout And Components

The admin shell uses Blade components under `resources/views/components/admin` and `resources/views/components/layouts/admin.blade.php`. Components use escaped output, semantic landmarks, theme tokens, and translation keys.

The dashboard view is a verification shell only. It contains no statistics, charts, business queries, monitoring data, mailbox/message content, support content, or placeholder cards for future modules.

## Integrations

Admin consumes:

- Navigation Registry area `admin`
- Translation Resolver for labels and shell text
- Locale Resolver for `lang` and `dir`
- Theme Resolver for `data-theme`
- Security headers from the global security middleware

Hidden navigation is not authorization. Future admin actions must still use server-side authorization.

## Security And Accessibility

The shell must not render secrets, raw exceptions, internal paths, provider payloads, tokens, audit metadata, or sensitive profile data. It includes semantic regions, a skip link, keyboard focus styles, and accessible navigation labels. Alpine usage is limited to local sidebar state and does not contain private data.

## Shared Hosting

The shell requires no runtime Node.js process, websocket, Redis, daemon, or external UI service. Built Vite assets remain static deployable assets.
