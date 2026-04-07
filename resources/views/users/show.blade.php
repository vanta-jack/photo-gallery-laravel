@extends('layouts.app')

@section('title', $displayName.' - About Me')

@section('content')
<div class="space-y-6">
    <section class="bg-card text-card-foreground border border-border rounded p-6">
        <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
            <div class="flex gap-4">
                <div class="h-20 w-20 shrink-0 overflow-hidden rounded border border-border bg-muted/40">
                    @if($user->profilePhoto?->path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->profilePhoto->path) }}" alt="{{ $displayName }} profile photo" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center text-xs font-bold text-muted-foreground">No Photo</div>
                    @endif
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-foreground">{{ $displayName }}</h1>
                    @if($user->email)
                        <p class="mt-1 text-sm text-muted-foreground">{{ $user->email }}</p>
                    @endif
                    <p class="mt-2 text-sm text-muted-foreground">Member since {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>

            <button type="button" data-contact-open aria-haspopup="dialog" aria-controls="contactDialog" class="inline-flex items-center justify-center bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                Contact Me
            </button>
        </div>
    </section>

    @include('users.partials.engagement-stats', ['engagement' => $engagement])

    <section class="bg-card text-card-foreground border border-border rounded p-6 space-y-3">
        <h2 class="text-lg font-bold text-foreground">Professional Summary</h2>
        @if($bioHtml)
            <div class="space-y-3 text-sm leading-6 text-foreground">{!! $bioHtml !!}</div>
        @else
            <p class="text-sm text-muted-foreground">No professional summary has been added yet.</p>
        @endif
    </section>

    <section class="bg-card text-card-foreground border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4">Academic History</h2>
        @if(count($academicHistory) > 0)
            <div class="space-y-4">
                @foreach($academicHistory as $entry)
                    <article class="border border-border rounded p-4 bg-muted/20">
                        <h3 class="font-bold text-foreground">{{ $entry['degree'] }}</h3>
                        <p class="text-sm text-foreground">{{ $entry['institution'] }}</p>
                        @if(filled($entry['graduation_date']))
                            <p class="text-xs text-muted-foreground mt-1">Graduated {{ \Illuminate\Support\Carbon::make($entry['graduation_date'])?->format('M Y') ?? $entry['graduation_date'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-sm text-muted-foreground">No academic history available.</p>
        @endif
    </section>

    <section class="bg-card text-card-foreground border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4">Professional Experience</h2>
        @if(count($professionalExperience) > 0)
            <div class="space-y-4">
                @foreach($professionalExperience as $entry)
                    <article class="border border-border rounded p-4 bg-muted/20">
                        <h3 class="font-bold text-foreground">{{ $entry['title'] }}</h3>
                        <p class="text-sm text-foreground">{{ $entry['company'] }}</p>
                        <p class="text-xs text-muted-foreground mt-1">
                            {{ \Illuminate\Support\Carbon::make($entry['start_date'])?->format('M Y') ?? $entry['start_date'] }}
                            —
                            {{ $entry['end_date'] ? (\Illuminate\Support\Carbon::make($entry['end_date'])?->format('M Y') ?? $entry['end_date']) : 'Present' }}
                        </p>
                        @if($entry['description'])
                            <p class="text-sm text-foreground mt-2">{{ $entry['description'] }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        @else
            <p class="text-sm text-muted-foreground">No professional experience available.</p>
        @endif
    </section>

    <section class="bg-card text-card-foreground border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4">Skills & Qualifications</h2>
        @if(count($skills) > 0)
            <div class="flex flex-wrap gap-2">
                @foreach($skills as $skill)
                    <span class="inline-flex items-center rounded border border-border bg-secondary px-2 py-1 text-xs font-bold text-secondary-foreground">{{ $skill }}</span>
                @endforeach
            </div>
        @else
            <p class="text-sm text-muted-foreground">No skills listed yet.</p>
        @endif
    </section>

    <section class="bg-card text-card-foreground border border-border rounded p-6">
        <h2 class="text-lg font-bold text-foreground mb-4">Links & Certifications</h2>
        <div class="grid gap-4 md:grid-cols-2">
            <div>
                <h3 class="text-sm font-bold text-foreground mb-2">Professional Links</h3>
                <ul class="space-y-2 text-sm">
                    @if($contact['linkedin'])
                        <li><a href="{{ $contact['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">LinkedIn</a></li>
                    @endif
                    @if($contact['github'])
                        <li><a href="{{ $contact['github'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">GitHub</a></li>
                    @endif
                    @if($user->orcid_id)
                        <li>ORCID: <span class="font-medium text-foreground">{{ $user->orcid_id }}</span></li>
                    @endif
                    @foreach($otherLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="text-primary hover:underline">{{ $link['label'] }}</a>
                        </li>
                    @endforeach
                    @if(!$contact['linkedin'] && !$contact['github'] && !$user->orcid_id && count($otherLinks) === 0)
                        <li class="text-muted-foreground">No links available.</li>
                    @endif
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold text-foreground mb-2">Certifications</h3>
                @if(count($certifications) > 0)
                    <ul class="space-y-2 text-sm">
                        @foreach($certifications as $certification)
                            <li class="border border-border rounded p-3 bg-muted/20">
                                <p class="font-bold text-foreground">{{ $certification['name'] }}</p>
                                @if($certification['issuer'])
                                    <p class="text-foreground">{{ $certification['issuer'] }}</p>
                                @endif
                                @if($certification['awarded_on'])
                                    <p class="text-xs text-muted-foreground mt-1">Awarded {{ \Illuminate\Support\Carbon::make($certification['awarded_on'])?->format('M Y') ?? $certification['awarded_on'] }}</p>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-muted-foreground">No certifications listed.</p>
                @endif
            </div>
        </div>
    </section>
</div>

@include('users.partials.contact-modal', [
    'user' => $user,
    'displayName' => $displayName,
    'contact' => $contact,
    'otherLinks' => $otherLinks,
])
@endsection
