# Application Modules

This directory contains the Temp Mail SaaS v1 modular monolith boundaries.

Modules own domain concepts defined by `docs/roadmap/03-ownership-matrix.md`. Laravel-native areas such as `app/Http`, `app/Models`, `app/Providers`, `bootstrap`, `config`, `database`, `resources`, `routes`, `storage`, and `tests` remain the application infrastructure.

STEP001 establishes only structural ownership. Do not add business behavior, routes, database tables, jobs, DTOs, services, repositories, or policies here unless a later scoped prompt requires them.
