@php
/** @var \App\Models\User|null $user */
$user = $user ?? null;
$showTrigger = (bool) ($showTrigger ?? true);
$triggerLabel = trim((string) ($triggerLabel ?? 'Contact Me'));
$dialogId = trim((string) ($dialogId ?? 'contact-dialog'));

$displayName = trim((string) ($displayName ?? ''));
if ($displayName === '') {
    $displayName = trim(sprintf('%s %s', (string) ($user?->first_name ?? ''), (string) ($user?->last_name ?? '')));
}
if ($displayName === '') {
    $displayName = 'Unknown user';
}

$contact = is_array($contact ?? null) ? $contact : [];

$normalizeUrl = static function (?string $url): ?string {
    $url = trim((string) $url);

    if ($url === '' || ! filter_var($url, FILTER_VALIDATE_URL)) {
        return null;
    }

    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

    return in_array($scheme, ['http', 'https'], true) ? $url : null;
};

$email = trim((string) ($contact['email'] ?? ($user?->email ?? '')));
$linkedin = $normalizeUrl($contact['linkedin'] ?? null);
$github = $normalizeUrl($contact['github'] ?? null);
$phone = trim((string) ($contact['phone'] ?? ''));

$hasChannels = $email !== '' || $linkedin !== null || $github !== null || $phone !== '';
@endphp

@if($showTrigger)
    <x-ui.button
        type="button"
        data-modal-open
        aria-controls="{{ $dialogId }}"
        aria-haspopup="dialog"
    >
        {{ $triggerLabel !== '' ? $triggerLabel : 'Contact Me' }}
    </x-ui.button>
@endif

<div
    id="{{ $dialogId }}"
    data-modal
    aria-hidden="true"
    hidden
    class="fixed inset-0 z-50 hidden flex items-center justify-center bg-background/85 p-4"
>
    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $dialogId }}-title"
        aria-describedby="{{ $dialogId }}-description"
        class="max-h-[calc(100vh-2rem)] w-full max-w-lg overflow-y-auto rounded border border-border bg-card p-0 text-card-foreground"
    >
        <div class="space-y-4 p-4">
            <div class="flex items-start justify-between gap-3 border-b border-border pb-3">
                <div class="space-y-1">
                    <h2 id="{{ $dialogId }}-title" class="text-lg font-bold text-foreground">Contact Me</h2>
                    <p id="{{ $dialogId }}-description" class="text-sm text-muted-foreground">
                        Published contact channels for {{ $displayName }}.
                    </p>
                </div>

                <x-ui.button
                    type="button"
                    variant="secondary"
                    size="sm"
                    data-modal-close
                    data-modal-id="{{ $dialogId }}"
                    data-modal-initial-focus
                    aria-label="Close contact modal"
                >
                    Close
                </x-ui.button>
            </div>

            <dl class="space-y-3 text-sm">
                <div class="rounded border border-border bg-background p-3">
                    <dt class="text-xs font-bold text-muted-foreground">Name</dt>
                    <dd class="mt-1 text-foreground">{{ $displayName }}</dd>
                </div>

                @if($email !== '')
                    <div class="rounded border border-border bg-background p-3">
                        <dt class="text-xs font-bold text-muted-foreground">Email</dt>
                        <dd class="mt-1">
                            <a href="mailto:{{ $email }}" class="text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $email }}
                            </a>
                        </dd>
                    </div>
                @endif

                @if($linkedin)
                    <div class="rounded border border-border bg-background p-3">
                        <dt class="text-xs font-bold text-muted-foreground">LinkedIn</dt>
                        <dd class="mt-1">
                            <a href="{{ $linkedin }}" target="_blank" rel="noopener noreferrer" class="break-all text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $linkedin }}
                            </a>
                        </dd>
                    </div>
                @endif

                @if($github)
                    <div class="rounded border border-border bg-background p-3">
                        <dt class="text-xs font-bold text-muted-foreground">GitHub</dt>
                        <dd class="mt-1">
                            <a href="{{ $github }}" target="_blank" rel="noopener noreferrer" class="break-all text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $github }}
                            </a>
                        </dd>
                    </div>
                @endif

                @if($phone !== '')
                    <div class="rounded border border-border bg-background p-3">
                        <dt class="text-xs font-bold text-muted-foreground">Phone</dt>
                        <dd class="mt-1">
                            <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}" class="text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $phone }}
                            </a>
                        </dd>
                    </div>
                @endif
            </dl>

            @unless($hasChannels)
                <x-ui.alert
                    variant="muted"
                    title="No public contact channels available."
                    description="Contact details have not been published for this profile."
                />
            @endunless

            <div class="flex items-center justify-end border-t border-border pt-3">
                <x-ui.button type="button" variant="secondary" data-modal-close data-modal-id="{{ $dialogId }}">
                    Done
                </x-ui.button>
            </div>
        </div>
    </div>
</div>
