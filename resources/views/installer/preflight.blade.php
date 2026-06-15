<x-layouts.public locale="en" direction="ltr" theme="system" title="Installer preflight" description="Safe shared-hosting readiness checks.">
    <style>
        .installer-shell {
            margin: 0 auto;
            max-width: 960px;
            padding: 40px 24px;
        }

        .installer-header {
            border-bottom: 1px solid var(--color-border);
            padding-bottom: 24px;
        }

        .installer-title {
            font-size: 32px;
            line-height: 1.15;
            margin: 0;
        }

        .installer-copy {
            color: var(--color-muted);
            font-size: 15px;
            margin: 10px 0 0;
        }

        .installer-checks {
            display: grid;
            gap: 12px;
            margin: 24px 0 0;
        }

        .installer-check {
            border: 1px solid var(--color-border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
            display: grid;
            gap: 12px;
            grid-template-columns: minmax(0, 190px) 1fr;
            padding: 16px;
        }

        .installer-check__label {
            font-weight: 650;
        }

        .installer-check__message {
            color: var(--color-muted);
            font-size: 14px;
            margin: 8px 0 0;
        }

        .installer-status {
            border: 1px solid var(--color-border);
            border-radius: 999px;
            display: inline-flex;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            padding: 4px 9px;
            text-transform: uppercase;
        }

        .installer-status--ok {
            background: #ecfdf5;
            border-color: #a7f3d0;
            color: #047857;
        }

        .installer-status--warning {
            background: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }

        .installer-status--blocker {
            background: #fef2f2;
            border-color: #fecaca;
            color: #b91c1c;
        }

        .installer-actions {
            margin-top: 24px;
        }

        .installer-button {
            background: var(--color-fg);
            border: 1px solid var(--color-fg);
            border-radius: var(--radius-md);
            color: var(--color-bg);
            cursor: pointer;
            font: inherit;
            font-weight: 650;
            padding: 10px 14px;
        }

        .installer-button:focus {
            outline: 2px solid var(--color-focus);
            outline-offset: 2px;
        }

        @media (max-width: 640px) {
            .installer-shell {
                padding: 28px 16px;
            }

            .installer-check {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <main class="installer-shell">
        <header class="installer-header">
            <h1 class="installer-title">Installer preflight</h1>
            <p class="installer-copy">
                Safe shared-hosting readiness checks. Secrets and internal paths are not displayed.
            </p>
            <p class="installer-copy">
                This foundation screen does not collect database credentials or create an admin account. Configure environment values before deployment; authentication and staff setup arrive in later steps.
            </p>
        </header>

        @if ($errors->any())
            <section class="installer-check" role="alert" aria-label="Installer errors">
                <div class="installer-check__label">Cannot complete installation</div>
                <div>
                    <span class="installer-status installer-status--blocker">blocker</span>
                    @foreach ($errors->all() as $error)
                        <p class="installer-check__message">{{ $error }}</p>
                    @endforeach
                </div>
            </section>
        @endif

        <div class="installer-checks">
            @foreach ($checks as $check)
                <section class="installer-check" aria-labelledby="installer-check-{{ $check->key }}">
                    <div id="installer-check-{{ $check->key }}" class="installer-check__label">{{ $check->label }}</div>
                    <div>
                        <span class="installer-status installer-status--{{ $check->status }}">{{ $check->status }}</span>
                        <p class="installer-check__message">{{ $check->message }}</p>
                    </div>
                </section>
            @endforeach
        </div>

        <form method="POST" action="{{ route('installer.lock') }}" class="installer-actions">
            @csrf
            <button type="submit" class="installer-button">
                Lock installer routes
            </button>
        </form>
    </main>
</x-layouts.public>
