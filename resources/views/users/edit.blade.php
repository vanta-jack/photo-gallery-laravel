@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-card text-card-foreground border border-border rounded p-6 max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-foreground mb-6">Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST" id="profile-form">
        @csrf
        @method('PUT')

        {{-- Basic Information --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Basic Information</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="first_name" class="block text-sm font-bold mb-2 text-foreground">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="{{ old('first_name', $user->first_name) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    @error('first_name')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-bold mb-2 text-foreground">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="{{ old('last_name', $user->last_name) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    @error('last_name')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-sm font-bold mb-2 text-foreground">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                @error('email')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Professional Summary --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Professional Summary</h2>
            <div class="mb-4">
                <label for="bio" class="block text-sm font-bold mb-2 text-foreground">Bio <span class="text-muted-foreground font-normal">(Markdown supported, max 5000 characters)</span></label>
                <textarea id="bio" name="bio" rows="10" data-markdown-editor class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">{{ old('bio', $user->bio) }}</textarea>
                @error('bio')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Contact Information</h2>
            
            <div class="mb-4">
                <label for="phone" class="block text-sm font-bold mb-2 text-foreground">Phone Number</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 123-4567" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                @error('phone')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                
                <div class="mt-2">
                    <label class="flex items-center">
                        <input type="hidden" name="phone_public" value="0">
                        <input type="checkbox" name="phone_public" value="1" {{ old('phone_public', $user->phone_public) ? 'checked' : '' }} class="mr-2">
                        <span class="text-sm text-foreground">Make phone number publicly visible</span>
                    </label>
                </div>
            </div>

            <div class="mb-4">
                <label for="linkedin" class="block text-sm font-bold mb-2 text-foreground">LinkedIn Profile URL</label>
                <input type="url" id="linkedin" name="linkedin" value="{{ old('linkedin', $user->linkedin) }}" placeholder="https://linkedin.com/in/yourprofile" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                @error('linkedin')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
            </div>
        </div>

        {{-- Academic History --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Academic History</h2>
            <div id="academic-history-container" class="space-y-4">
                @php
                    $academicHistory = old('academic_history', $user->academic_history ?? []);
                @endphp
                @forelse($academicHistory as $index => $entry)
                    <div class="academic-entry bg-muted/30 border border-border rounded p-4">
                        <div class="flex justify-between mb-3">
                            <span class="text-sm font-bold text-foreground">Education Entry #{{ $index + 1 }}</span>
                            <button type="button" class="remove-academic text-destructive text-sm hover:underline">Remove</button>
                        </div>
                        <div class="grid grid-cols-3 gap-3">
                            <div class="col-span-2">
                                <input type="text" name="academic_history[{{ $index }}][degree]" value="{{ $entry['degree'] ?? '' }}" placeholder="Degree (e.g., BSc Computer Science)" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted-foreground mb-1">Graduation Date</label>
                                <input type="date" name="academic_history[{{ $index }}][graduation_date]" value="{{ $entry['graduation_date'] ?? '' }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                        </div>
                        <div class="mt-3">
                            <input type="text" name="academic_history[{{ $index }}][institution]" value="{{ $entry['institution'] ?? '' }}" placeholder="Institution" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No education entries yet. Click "Add Education" to get started.</p>
                @endforelse
            </div>
            <button type="button" id="add-academic" class="mt-3 text-sm text-primary hover:underline">+ Add Education</button>
        </div>

        {{-- Professional Experience --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Professional Experience</h2>
            <div id="experience-container" class="space-y-4">
                @php
                    $experience = old('professional_experience', $user->professional_experience ?? []);
                @endphp
                @forelse($experience as $index => $entry)
                    <div class="experience-entry bg-muted/30 border border-border rounded p-4">
                        <div class="flex justify-between mb-3">
                            <span class="text-sm font-bold text-foreground">Position #{{ $index + 1 }}</span>
                            <button type="button" class="remove-experience text-destructive text-sm hover:underline">Remove</button>
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <input type="text" name="professional_experience[{{ $index }}][title]" value="{{ $entry['title'] ?? '' }}" placeholder="Job Title" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            <input type="text" name="professional_experience[{{ $index }}][company]" value="{{ $entry['company'] ?? '' }}" placeholder="Company" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-3">
                            <div>
                                <label class="block text-xs font-medium text-muted-foreground mb-1">Start Date</label>
                                <input type="date" name="professional_experience[{{ $index }}][start_date]" value="{{ $entry['start_date'] ?? '' }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-muted-foreground mb-1">End Date</label>
                                <input type="date" name="professional_experience[{{ $index }}][end_date]" value="{{ $entry['end_date'] ?? '' }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                        </div>
                        <textarea name="professional_experience[{{ $index }}][description]" rows="3" placeholder="Brief description" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">{{ $entry['description'] ?? '' }}</textarea>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No work experience yet. Click "Add Position" to get started.</p>
                @endforelse
            </div>
            <button type="button" id="add-experience" class="mt-3 text-sm text-primary hover:underline">+ Add Position</button>
        </div>

        {{-- Skills --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Skills & Qualifications</h2>
            <div class="mb-4">
                <label for="skills-input" class="block text-sm font-bold mb-2 text-foreground">Skills <span class="text-muted-foreground font-normal">(comma-separated)</span></label>
                <input type="text" id="skills-input" name="skills-input-display" value="{{ old('skills') ? implode(', ', old('skills')) : ($user->skills ? implode(', ', $user->skills) : '') }}" placeholder="PHP, Laravel, JavaScript, React..." class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                <div id="skills-hidden-container"></div>
            </div>
        </div>

        {{-- Additional Information --}}
        <div class="mb-8">
            <h2 class="text-lg font-bold text-foreground mb-4 border-b border-border pb-2">Additional Information</h2>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="orcid_id" class="block text-sm font-bold mb-2 text-foreground">ORCID ID</label>
                    <input type="text" id="orcid_id" name="orcid_id" value="{{ old('orcid_id', $user->orcid_id) }}" placeholder="0000-0000-0000-0000" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    @error('orcid_id')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label for="github" class="block text-sm font-bold mb-2 text-foreground">GitHub Profile</label>
                    <input type="url" id="github" name="github" value="{{ old('github', $user->github) }}" placeholder="https://github.com/username" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    @error('github')<span class="text-destructive text-sm mt-1 block">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-sm font-bold text-foreground mb-3">Certifications</h3>
                <div id="certifications-container" class="space-y-3">
                    @php
                        $certifications = old('certifications', $user->certifications ?? []);
                    @endphp
                    @forelse($certifications as $index => $entry)
                        <div class="certification-entry bg-muted/30 border border-border rounded p-4">
                            <div class="flex justify-between mb-3">
                                <span class="text-sm font-bold text-foreground">Certification #{{ $index + 1 }}</span>
                                <button type="button" class="remove-certification text-destructive text-sm hover:underline">Remove</button>
                            </div>
                            <div class="grid grid-cols-3 gap-3 mb-3">
                                <input type="text" name="certifications[{{ $index }}][name]" value="{{ $entry['name'] ?? '' }}" placeholder="Certification name" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                                <input type="text" name="certifications[{{ $index }}][issuer]" value="{{ $entry['issuer'] ?? '' }}" placeholder="Issuer" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                                <label class="block text-xs font-medium text-muted-foreground mb-1">Awarded On</label>
                                <input type="date" name="certifications[{{ $index }}][awarded_on]" value="{{ $entry['awarded_on'] ?? '' }}" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                            <input type="number" min="1" name="certifications[{{ $index }}][photo_id]" value="{{ $entry['photo_id'] ?? '' }}" placeholder="Optional certification photo ID" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground">No certifications yet. Click "Add Certification" to get started.</p>
                    @endforelse
                </div>
                <button type="button" id="add-certification" class="mt-3 text-sm text-primary hover:underline">+ Add Certification</button>
            </div>

            <div class="mb-4">
                <h3 class="text-sm font-bold text-foreground mb-3">Other Links</h3>
                <div id="other-links-container" class="space-y-3">
                    @php
                        $otherLinks = old('other_links', $user->other_links ?? []);
                    @endphp
                    @forelse($otherLinks as $index => $entry)
                        <div class="other-link-entry bg-muted/30 border border-border rounded p-4">
                            <div class="flex justify-between mb-3">
                                <span class="text-sm font-bold text-foreground">Link #{{ $index + 1 }}</span>
                                <button type="button" class="remove-other-link text-destructive text-sm hover:underline">Remove</button>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="other_links[{{ $index }}][label]" value="{{ $entry['label'] ?? '' }}" placeholder="Label (e.g., Portfolio)" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                                <input type="url" name="other_links[{{ $index }}][url]" value="{{ $entry['url'] ?? '' }}" placeholder="https://example.com" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-muted-foreground">No additional links yet. Click "Add Link" to get started.</p>
                    @endforelse
                </div>
                <button type="button" id="add-other-link" class="mt-3 text-sm text-primary hover:underline">+ Add Link</button>
            </div>
        </div>

        <div class="flex gap-4 pt-4 border-t border-border">
            <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-6 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Update Profile</button>
            <a href="{{ route('home') }}" class="bg-card text-foreground font-bold text-sm px-6 py-2 rounded border border-border hover:bg-muted/50 transition-colors duration-150">Cancel</a>
        </div>
    </form>
</div>

<script>
// Repeatable input groups for academic history
let academicIndex = {{ count(old('academic_history', $user->academic_history ?? [])) }};
document.getElementById('add-academic').addEventListener('click', function() {
    const container = document.getElementById('academic-history-container');
    const empty = container.querySelector('p');
    if (empty) empty.remove();
    
    const entry = document.createElement('div');
    entry.className = 'academic-entry bg-muted/30 border border-border rounded p-4';
    entry.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="text-sm font-bold text-foreground">Education Entry #${academicIndex + 1}</span>
            <button type="button" class="remove-academic text-destructive text-sm hover:underline">Remove</button>
        </div>
        <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <input type="text" name="academic_history[${academicIndex}][degree]" placeholder="Degree (e.g., BSc Computer Science)" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                </div>
                <div>
                    <label class="block text-xs font-medium text-muted-foreground mb-1">Graduation Date</label>
                    <input type="date" name="academic_history[${academicIndex}][graduation_date]" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                </div>
        </div>
        <div class="mt-3">
            <input type="text" name="academic_history[${academicIndex}][institution]" placeholder="Institution" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
        </div>
    `;
    container.appendChild(entry);
    academicIndex++;
});

// Remove academic entry
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-academic')) {
        e.target.closest('.academic-entry').remove();
        const container = document.getElementById('academic-history-container');
        if (!container.querySelector('.academic-entry')) {
            container.innerHTML = '<p class="text-sm text-muted-foreground">No education entries yet. Click "Add Education" to get started.</p>';
        }
    }
});

// Repeatable input groups for professional experience
let experienceIndex = {{ count(old('professional_experience', $user->professional_experience ?? [])) }};
document.getElementById('add-experience').addEventListener('click', function() {
    const container = document.getElementById('experience-container');
    const empty = container.querySelector('p');
    if (empty) empty.remove();
    
    const entry = document.createElement('div');
    entry.className = 'experience-entry bg-muted/30 border border-border rounded p-4';
    entry.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="text-sm font-bold text-foreground">Position #${experienceIndex + 1}</span>
            <button type="button" class="remove-experience text-destructive text-sm hover:underline">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <input type="text" name="professional_experience[${experienceIndex}][title]" placeholder="Job Title" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            <input type="text" name="professional_experience[${experienceIndex}][company]" placeholder="Company" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
        </div>
        <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
                <label class="block text-xs font-medium text-muted-foreground mb-1">Start Date</label>
                <input type="date" name="professional_experience[${experienceIndex}][start_date]" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            </div>
            <div>
                <label class="block text-xs font-medium text-muted-foreground mb-1">End Date</label>
                <input type="date" name="professional_experience[${experienceIndex}][end_date]" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            </div>
        </div>
        <textarea name="professional_experience[${experienceIndex}][description]" rows="3" placeholder="Brief description" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"></textarea>
    `;
    container.appendChild(entry);
    experienceIndex++;
});

// Repeatable input groups for certifications
let certificationIndex = {{ count(old('certifications', $user->certifications ?? [])) }};
document.getElementById('add-certification').addEventListener('click', function() {
    const container = document.getElementById('certifications-container');
    const empty = container.querySelector('p');
    if (empty) empty.remove();

    const entry = document.createElement('div');
    entry.className = 'certification-entry bg-muted/30 border border-border rounded p-4';
    entry.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="text-sm font-bold text-foreground">Certification #${certificationIndex + 1}</span>
            <button type="button" class="remove-certification text-destructive text-sm hover:underline">Remove</button>
        </div>
        <div class="grid grid-cols-3 gap-3 mb-3">
            <input type="text" name="certifications[${certificationIndex}][name]" placeholder="Certification name" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            <input type="text" name="certifications[${certificationIndex}][issuer]" placeholder="Issuer" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            <div>
                <label class="block text-xs font-medium text-muted-foreground mb-1">Awarded On</label>
                <input type="date" name="certifications[${certificationIndex}][awarded_on]" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            </div>
        </div>
        <input type="number" min="1" name="certifications[${certificationIndex}][photo_id]" placeholder="Optional certification photo ID" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
    `;
    container.appendChild(entry);
    certificationIndex++;
});

// Repeatable input groups for other links
let otherLinkIndex = {{ count(old('other_links', $user->other_links ?? [])) }};
document.getElementById('add-other-link').addEventListener('click', function() {
    const container = document.getElementById('other-links-container');
    const empty = container.querySelector('p');
    if (empty) empty.remove();

    const entry = document.createElement('div');
    entry.className = 'other-link-entry bg-muted/30 border border-border rounded p-4';
    entry.innerHTML = `
        <div class="flex justify-between mb-3">
            <span class="text-sm font-bold text-foreground">Link #${otherLinkIndex + 1}</span>
            <button type="button" class="remove-other-link text-destructive text-sm hover:underline">Remove</button>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <input type="text" name="other_links[${otherLinkIndex}][label]" placeholder="Label (e.g., Portfolio)" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
            <input type="url" name="other_links[${otherLinkIndex}][url]" placeholder="https://example.com" class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
        </div>
    `;
    container.appendChild(entry);
    otherLinkIndex++;
});

// Remove experience entry
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-experience')) {
        e.target.closest('.experience-entry').remove();
        const container = document.getElementById('experience-container');
        if (!container.querySelector('.experience-entry')) {
            container.innerHTML = '<p class="text-sm text-muted-foreground">No work experience yet. Click "Add Position" to get started.</p>';
        }
    }
});

// Remove certification/link entries
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-certification')) {
        e.target.closest('.certification-entry').remove();
        const container = document.getElementById('certifications-container');
        if (!container.querySelector('.certification-entry')) {
            container.innerHTML = '<p class="text-sm text-muted-foreground">No certifications yet. Click "Add Certification" to get started.</p>';
        }
    }

    if (e.target.classList.contains('remove-other-link')) {
        e.target.closest('.other-link-entry').remove();
        const container = document.getElementById('other-links-container');
        if (!container.querySelector('.other-link-entry')) {
            container.innerHTML = '<p class="text-sm text-muted-foreground">No additional links yet. Click "Add Link" to get started.</p>';
        }
    }
});

// Skills handling - convert comma-separated to array on submit
document.getElementById('profile-form').addEventListener('submit', function(e) {
    const skillsInput = document.getElementById('skills-input').value;
    const skillsArray = skillsInput.split(',').map(s => s.trim()).filter(s => s.length > 0);
    const container = document.getElementById('skills-hidden-container');
    container.innerHTML = '';
    skillsArray.forEach((skill, index) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `skills[${index}]`;
        input.value = skill;
        container.appendChild(input);
    });
});
</script>
@endsection
