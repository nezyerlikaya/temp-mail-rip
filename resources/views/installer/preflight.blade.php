<x-layouts.public locale="en" direction="ltr" theme="system">
    <main class="mx-auto max-w-3xl p-6">
        <h1 class="text-2xl font-semibold">Installer preflight</h1>
        <p class="mt-2 text-sm text-[var(--color-muted)]">
            Safe shared-hosting readiness checks. Secrets and internal paths are not displayed.
        </p>

        <dl class="mt-6 divide-y divide-[var(--color-border)] border-y border-[var(--color-border)]">
            @foreach ($checks as $check)
                <div class="grid gap-2 py-4 sm:grid-cols-[12rem_1fr]">
                    <dt class="font-medium">{{ $check->label }}</dt>
                    <dd>
                        <span class="rounded-md border border-[var(--color-border)] px-2 py-1 text-xs uppercase">{{ $check->status }}</span>
                        <p class="mt-2 text-sm text-[var(--color-muted)]">{{ $check->message }}</p>
                    </dd>
                </div>
            @endforeach
        </dl>

        <form method="POST" action="{{ route('installer.lock') }}" class="mt-6">
            @csrf
            <button type="submit" class="rounded-md border border-[var(--color-border)] px-4 py-2 text-sm font-medium focus:outline focus:outline-2 focus:outline-[var(--color-focus)]">
                Create installer lock
            </button>
        </form>
    </main>
</x-layouts.public>
