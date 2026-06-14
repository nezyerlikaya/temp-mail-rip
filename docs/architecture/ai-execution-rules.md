# Temp Mail SaaS v1 - AI Execution Rules

This document mirrors the repository-level `AGENTS.md` rules as durable project documentation.

## Primary Rule

Before writing or modifying any code, always read the relevant files under `docs/`.

Required order:

1. `docs/constitutions/`
2. `docs/adr/`
3. `docs/architecture/`
4. `docs/checklists/`
5. Current STEP prompt

If a prompt conflicts with a constitution, the constitution wins.

## Project Stack

- Laravel 13
- PHP 8.5.7
- Blade Components
- Alpine.js
- Tailwind CSS
- Vite
- Modular Monolith
- Shared Hosting First
- Security First
- Webhook First

## Forbidden AI Behavior

Do not:

- invent new architecture
- bypass existing DTOs, Actions, Services, Policies, Registries, or Audit services
- use Redis, Horizon, Supervisor, Reverb, or long-running daemons as requirements
- use IMAP as core architecture
- create a WordPress-like CMS
- create a general Media Library
- create an admin mailbox/message browser
- log raw exceptions
- pass raw request arrays into services
- use unbounded `all()` / `get()` for lists
- write business logic in Blade
- create pure static HTML admin screens

## Required Completion Behavior

Before declaring completion, provide a self-audit summary covering:

- Shared Hosting compliance
- Security compliance
- Module boundary compliance
- Tests added or updated
- Documentation updated
