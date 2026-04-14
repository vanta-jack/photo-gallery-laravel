@extends('layouts.app')

@section('title', $displayName)

@section('content')
@php
$profilePhotoPath = trim((string) ($user->profilePhoto?->path ?? ''));
$profilePhotoUrl = $profilePhotoPath === ''
    ? null
    : (str_starts_with($profilePhotoPath, 'http://') || str_starts_with($profilePhotoPath, 'https://') || str_starts_with($profilePhotoPath, '/')
        ? $profilePhotoPath
        : \Illuminate\Support\Facades\Storage::url($profilePhotoPath));
$profilePhotoTitle = trim((string) ($user->profilePhoto?->title ?? ''));
if ($profilePhotoTitle === '') {
    $profilePhotoTitle = $displayName.' profile photo';
}

$linkedin = trim((string) ($contact['linkedin'] ?? ''));
$github = trim((string) ($contact['github'] ?? ''));
$orcid = trim((string) ($user->orcid_id ?? ''));
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-6" aria-labelledby="profile-heading">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h1 id="profile-heading" class="text-2xl font-bold text-foreground">{{ $displayName }}</h1>
                    <p class="text-sm text-muted-foreground">{{ $user->email }}</p>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    @include('users.partials.contact-modal', [
                        'user' => $user,
                        'displayName' => $displayName,
                        'contact' => $contact,
                        'dialogId' => 'profile-contact-dialog',
                    ])

                    @if($showEditCta)
                        <a
                            href="{{ route('profile.edit') }}"
                            class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                        >
                            Edit Profile
                        </a>
                    @endif
                </div>
            </div>
        </x-slot:header>

        <div class="flex flex-col gap-6 lg:flex-row lg:gap-6">
            {{-- Profile Picture (Left Column) --}}
            <section class="flex-shrink-0 lg:w-1/3" aria-label="Profile image">
                @if($profilePhotoUrl)
                    <img
                        src="{{ $profilePhotoUrl }}"
                        alt="{{ $profilePhotoTitle }}"
                        class="aspect-square w-full rounded border border-border object-cover"
                        loading="lazy"
                    >
                @else
                    <div class="flex aspect-square w-full items-center justify-center rounded border border-border bg-secondary p-4 text-sm text-muted-foreground">
                        No profile photo
                    </div>
                @endif
            </section>

            {{-- Professional Summary (Right Column) --}}
            <section class="flex-grow lg:w-2/3" aria-labelledby="professional-summary-heading">
                <h2 id="professional-summary-heading" class="text-lg font-bold text-foreground">Professional Summary</h2>

                @if(filled($bioHtml))
                    <div class="mt-3 space-y-3 rounded border border-border bg-background p-4 text-sm leading-6 text-foreground">
                        {!! $bioHtml !!}
                    </div>
                @else
                    <div class="mt-3">
                        <x-ui.empty-state
                            title="No professional summary yet."
                            description="This user has not added a bio."
                            compact
                            align="left"
                        />
                    </div>
                @endif

                {{-- Social Links (LinkedIn, GitHub, ORCID) --}}
                <div class="mt-4 space-y-2">
                    @if($linkedin !== '')
                        <a
                            href="{{ $linkedin }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded border border-border bg-secondary px-3 py-2 text-sm text-foreground transition-opacity duration-150 hover:opacity-80 break-all"
                            aria-label="LinkedIn profile"
                        >
                            <x-icon name="linkedin" class="h-5 w-5 flex-shrink-0" />
                            <span>{{ $linkedin }}</span>
                        </a>
                    @endif

                    @if($github !== '')
                        <a
                            href="{{ $github }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded border border-border bg-secondary px-3 py-2 text-sm text-foreground transition-opacity duration-150 hover:opacity-80 break-all"
                            aria-label="GitHub profile"
                        >
                            <x-icon name="github" class="h-5 w-5 flex-shrink-0" />
                            <span>{{ $github }}</span>
                        </a>
                    @endif

                    @if($orcid !== '')
                        <a
                            href="https://orcid.org/{{ $orcid }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded border border-border bg-secondary px-3 py-2 text-sm text-foreground transition-opacity duration-150 hover:opacity-80 break-all"
                            aria-label="ORCID profile"
                        >
                            <x-icon name="orcid" class="h-5 w-5 flex-shrink-0" />
                            <span>https://orcid.org/{{ $orcid }}</span>
                        </a>
                    @endif

                    @if(filled($contact['phone'] ?? null) && $contact['phone_public'])
                        <a
                            href="tel:{{ $contact['phone'] }}"
                            class="inline-flex items-center gap-2 rounded border border-border bg-secondary px-3 py-2 text-sm text-foreground transition-opacity duration-150 hover:opacity-80"
                            aria-label="Phone contact"
                        >
                            <x-icon name="phone" class="h-5 w-5 flex-shrink-0" />
                            <span>{{ $contact['phone'] }}</span>
                        </a>
                    @endif

                    @foreach($otherLinks as $link)
                        <a
                            href="{{ $link['url'] }}"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="inline-flex items-center gap-2 rounded border border-border bg-secondary px-3 py-2 text-sm text-foreground transition-opacity duration-150 hover:opacity-80 break-all"
                            aria-label="{{ $link['label'] }} profile"
                        >
                            <x-icon name="globe" class="h-5 w-5 flex-shrink-0" />
                            <span>{{ $link['url'] }}</span>
                        </a>
                    @endforeach
                </div>

                {{-- Phone Visibility Notice --}}
                <div class="mt-4 rounded border border-border bg-secondary p-3">
                    <p class="text-xs font-bold text-muted-foreground">Phone visibility</p>
                    <p class="mt-1 text-sm text-foreground">
                        {{ filled($contact['phone'] ?? null) ? 'Phone is visible in Contact Me.' : 'Phone is hidden from public contact channels.' }}
                    </p>
                </div>
            </section>
        </div>
    </x-ui.card>

    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
        <x-ui.card class="space-y-3" aria-labelledby="academic-history-heading">
            <x-slot:header>
                <h2 id="academic-history-heading" class="text-lg font-bold text-foreground">Academic History</h2>
            </x-slot:header>

            @if($academicHistory === [])
                <x-ui.empty-state
                    title="No academic history listed."
                    description="Education credentials will appear here."
                    compact
                    align="left"
                />
            @else
                <ul class="space-y-2">
                    @foreach($academicHistory as $entry)
                        <li class="rounded border border-border bg-background p-3">
                            <p class="text-sm font-bold text-foreground">{{ $entry['degree'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $entry['institution'] }}</p>
                            @if(filled($entry['graduation_date'] ?? null))
                                <p class="text-xs text-muted-foreground">Graduated {{ $entry['graduation_date'] }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card class="space-y-3" aria-labelledby="professional-experience-heading">
            <x-slot:header>
                <h2 id="professional-experience-heading" class="text-lg font-bold text-foreground">Professional Experience</h2>
            </x-slot:header>

            @if($professionalExperience === [])
                <x-ui.empty-state
                    title="No professional experience listed."
                    description="Work history details will appear here."
                    compact
                    align="left"
                />
            @else
                <ul class="space-y-2">
                    @foreach($professionalExperience as $entry)
                        <li class="rounded border border-border bg-background p-3">
                            <p class="text-sm font-bold text-foreground">{{ $entry['title'] }}</p>
                            <p class="text-sm text-muted-foreground">{{ $entry['company'] }}</p>
                            <p class="text-xs text-muted-foreground">
                                {{ $entry['start_date'] }}
                                —
                                {{ filled($entry['end_date'] ?? null) ? $entry['end_date'] : 'Present' }}
                            </p>
                            @if(filled($entry['description_html'] ?? null))
                                <x-ui.markdown-content :html="$entry['description_html']" class="mt-2 text-sm" />
                            @elseif(filled($entry['description'] ?? null))
                                <p class="mt-2 text-sm text-foreground">{{ $entry['description'] }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>

        <x-ui.card class="space-y-3" aria-labelledby="skills-heading">
            <x-slot:header>
                <h2 id="skills-heading" class="text-lg font-bold text-foreground">Skills & Certifications</h2>
            </x-slot:header>

            <div class="space-y-3">
                <section class="space-y-2">
                    <h3 class="text-sm font-bold text-foreground">Skills</h3>
                    @if($skills === [])
                        <p class="text-sm text-muted-foreground">No skills listed.</p>
                    @else
                        <div class="flex flex-wrap gap-2">
                            @foreach($skills as $skill)
                                <x-ui.badge variant="secondary" size="sm">{{ $skill }}</x-ui.badge>
                            @endforeach
                        </div>
                    @endif
                </section>

                <section class="space-y-2 border-t border-border pt-3">
                    <h3 class="text-sm font-bold text-foreground">Certifications</h3>
                    @if($certifications === [])
                        <p class="text-sm text-muted-foreground">No certifications listed.</p>
                    @else
                        <ul class="space-y-2">
                            @foreach($certifications as $certification)
                                <li class="rounded border border-border bg-background p-3">
                                    <p class="text-sm font-bold text-foreground">{{ $certification['name'] }}</p>
                                    @if(filled($certification['issuer'] ?? null))
                                        <p class="text-sm text-muted-foreground">{{ $certification['issuer'] }}</p>
                                    @endif
                                    @if(filled($certification['awarded_on'] ?? null))
                                        <p class="text-xs text-muted-foreground">Awarded {{ $certification['awarded_on'] }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>
        </x-ui.card>

        <x-ui.card class="space-y-3" aria-labelledby="links-heading">
            <x-slot:header>
                <h2 id="links-heading" class="text-lg font-bold text-foreground">Professional Links</h2>
            </x-slot:header>

            @if($otherLinks === [])
                <x-ui.empty-state
                    title="No additional links provided."
                    description="Portfolio and reference links will appear here."
                    compact
                    align="left"
                />
            @else
                <ul class="space-y-2">
                    @foreach($otherLinks as $link)
                        <li class="rounded border border-border bg-background p-3">
                            <p class="text-xs font-bold text-muted-foreground">{{ $link['label'] }}</p>
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="mt-1 block break-all text-sm text-foreground underline-offset-4 transition-opacity duration-150 hover:underline hover:opacity-80">
                                {{ $link['url'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif
        </x-ui.card>
    </div>

    @include('users.partials.engagement-stats', ['engagement' => $engagement])
</div>
@endsection
