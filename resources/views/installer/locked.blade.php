<x-layouts.public locale="en" direction="ltr" theme="system" title="Installer locked">
    <style>
        .installer-shell {
            margin: 0 auto;
            max-width: 720px;
            padding: 40px 24px;
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
    </style>

    <main class="installer-shell">
        <h1 class="installer-title">Installer locked</h1>
        <p class="installer-copy">
            Installation is locked. Browser unlock is not available for safety.
        </p>
        <p class="installer-copy">
            This lock only closes the installer routes. It does not mean database credentials were entered or an admin account was created.
        </p>
    </main>
</x-layouts.public>
