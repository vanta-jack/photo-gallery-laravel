<dialog id="contactDialog" data-contact-dialog class="w-full max-w-lg rounded border border-border bg-card p-0 text-card-foreground">
    <div class="border-b border-border px-6 py-4">
        <h2 class="text-lg font-bold text-foreground">Contact {{ $displayName }}</h2>
    </div>

    <div class="space-y-4 px-6 py-5">
        @php
            $hasContactField = $user->email || $contact['phone'] || $contact['linkedin'] || $contact['github'] || count($otherLinks) > 0;
        @endphp

        <dl class="space-y-3 text-sm">
            <div>
                <dt class="text-muted-foreground">Name</dt>
                <dd class="font-medium text-foreground">{{ $displayName }}</dd>
            </div>

            @if($user->email)
                <div>
                    <dt class="text-muted-foreground">Email</dt>
                    <dd><a href="mailto:{{ $user->email }}" class="text-primary hover:underline">{{ $user->email }}</a></dd>
                </div>
            @endif

            @if($contact['phone'])
                <div>
                    <dt class="text-muted-foreground">Phone</dt>
                    <dd>{{ $contact['phone'] }}</dd>
                </div>
            @endif

            @if($contact['linkedin'])
                <div>
                    <dt class="text-muted-foreground">LinkedIn</dt>
                    <dd><a href="{{ $contact['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">{{ $contact['linkedin'] }}</a></dd>
                </div>
            @endif

            @if($contact['github'])
                <div>
                    <dt class="text-muted-foreground">GitHub</dt>
                    <dd><a href="{{ $contact['github'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">{{ $contact['github'] }}</a></dd>
                </div>
            @endif

            @if(count($otherLinks) > 0)
                <div>
                    <dt class="text-muted-foreground">Other Links</dt>
                    <dd class="space-y-1">
                        @foreach($otherLinks as $link)
                            <div>
                                <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">{{ $link['label'] }}</a>
                            </div>
                        @endforeach
                    </dd>
                </div>
            @endif
        </dl>

        @if(!$hasContactField)
            <p class="text-sm text-muted-foreground">No public contact details are available right now.</p>
        @endif
    </div>

    <div class="flex justify-end gap-2 border-t border-border px-6 py-4">
        <button type="button" data-contact-close data-contact-initial-focus class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
            Close
        </button>
    </div>
</dialog>
