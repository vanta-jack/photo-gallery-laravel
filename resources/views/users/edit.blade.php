@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
@php
$profilePhotoValue = old('profile_photo_id', $user->profile_photo_id);

$academicHistory = old('academic_history', $user->academic_history ?? []);
$academicHistory = is_array($academicHistory)
    ? array_values(array_filter($academicHistory, static fn ($entry): bool => is_array($entry)))
    : [];

$professionalExperience = old('professional_experience', $user->professional_experience ?? []);
$professionalExperience = is_array($professionalExperience)
    ? array_values(array_filter($professionalExperience, static fn ($entry): bool => is_array($entry)))
    : [];

$skills = old('skills', $user->skills ?? []);
$skills = is_array($skills)
    ? array_values(array_map(static fn ($skill): string => (string) $skill, $skills))
    : [];

$certifications = old('certifications', $user->certifications ?? []);
$certifications = is_array($certifications)
    ? array_values(array_filter($certifications, static fn ($entry): bool => is_array($entry)))
    : [];

$otherLinks = old('other_links', $user->other_links ?? []);
$otherLinks = is_array($otherLinks)
    ? array_values(array_filter($otherLinks, static fn ($entry): bool => is_array($entry)))
    : [];

$phonePublic = (bool) old('phone_public', $user->phone_public);

$inputBaseClass = 'block w-full rounded border border-input bg-background px-3 py-2 text-sm text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 focus-visible:ring-offset-background';
$textareaBaseClass = $inputBaseClass;
@endphp

<div class="space-y-6">
    <x-ui.card class="space-y-6" aria-labelledby="edit-profile-heading">
        <x-slot:header>
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="space-y-1">
                    <h1 id="edit-profile-heading" class="text-2xl font-bold text-foreground">Edit Profile</h1>
                    <p class="text-sm text-muted-foreground">Update your CV details, public contact channels, and profile media.</p>
                </div>

                <a
                    href="{{ route('profile.show') }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Back to profile
                </a>
            </div>
        </x-slot:header>

        @if($errors->any())
            <x-ui.alert
                variant="destructive"
                title="Profile could not be updated."
                description="Please resolve the highlighted fields and try again."
            />
        @endif

        <form id="profile-form" action="{{ route('profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <section class="space-y-4" aria-labelledby="profile-basics-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="profile-basics-heading" class="text-lg font-bold text-foreground">Basic Information</h2>
                    <x-ui.badge variant="outline" size="sm">Required</x-ui.badge>
                </div>

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-ui.form-input
                        name="first_name"
                        label="First name"
                        :value="old('first_name', $user->first_name)"
                        required
                    />

                    <x-ui.form-input
                        name="last_name"
                        label="Last name"
                        :value="old('last_name', $user->last_name)"
                        required
                    />

                    <x-ui.form-input
                        name="email"
                        type="email"
                        label="Email"
                        :value="old('email', $user->email)"
                        required
                    />

                    <x-ui.photo-selector-single
                        name="profile_photo_id"
                        label="Profile photo"
                        :availablePhotos="$availableProfilePhotos"
                        :selectedPhotoId="$profilePhotoValue"
                        help="Choose an existing photo or upload a new one."
                        :allowClear="true"
                    />
                </div>
            </section>

            <section class="space-y-4 border-t border-border pt-4" aria-labelledby="profile-about-heading">
                <h2 id="profile-about-heading" class="text-lg font-bold text-foreground">About & Contact</h2>

                <x-ui.markdown-editor
                    name="bio"
                    label="Professional summary"
                    :value="old('bio', $user->bio)"
                    :rows="8"
                    help="Markdown is supported. Keep it concise and readable."
                    placeholder="Summarize your expertise, interests, and current focus."
                />

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <x-ui.form-input
                        name="phone"
                        label="Phone"
                        :value="old('phone', $user->phone)"
                        placeholder="+1 (555) 123-4567"
                    />

                    <x-ui.form-input
                        name="linkedin"
                        label="LinkedIn URL"
                        :value="old('linkedin', $user->linkedin)"
                        placeholder="https://linkedin.com/in/username"
                    />

                    <x-ui.form-input
                        name="github"
                        label="GitHub URL"
                        :value="old('github', $user->github)"
                        placeholder="https://github.com/username"
                    />

                    <x-ui.form-input
                        name="orcid_id"
                        label="ORCID ID"
                        :value="old('orcid_id', $user->orcid_id)"
                        placeholder="0000-0002-1234-5678"
                    />
                </div>

                <div class="rounded border border-border bg-secondary p-3">
                    <input type="hidden" name="phone_public" value="0">
                    <label class="inline-flex items-center gap-2 text-sm font-bold text-foreground">
                        <input
                            type="checkbox"
                            name="phone_public"
                            value="1"
                            @checked($phonePublic)
                            class="h-4 w-4 rounded border border-input bg-background text-primary focus-visible:ring-2 focus-visible:ring-ring"
                        >
                        Show phone number publicly in the Contact Me modal
                    </label>
                    @error('phone_public')
                        <p class="mt-2 text-sm text-destructive">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            <section class="space-y-3 border-t border-border pt-4" data-repeatable data-next-index="{{ count($skills) }}" aria-labelledby="skills-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="skills-heading" class="text-lg font-bold text-foreground">Skills</h2>
                    <x-ui.button type="button" variant="secondary" size="sm" data-repeatable-add>Add skill</x-ui.button>
                </div>

                <div data-repeatable-empty @class(['rounded border border-dashed border-border bg-secondary p-3 text-sm text-muted-foreground', 'hidden' => $skills !== []])>
                    No skills added yet.
                </div>

                <div class="space-y-3" data-repeatable-list>
                    @foreach($skills as $index => $skill)
                        <div data-repeatable-item class="rounded border border-border bg-background p-3">
                            <div class="mb-3 flex items-center justify-end">
                                <x-ui.button type="button" variant="ghost" size="sm" data-repeatable-remove>Remove</x-ui.button>
                            </div>
                            <x-ui.form-input
                                :name="'skills['.$index.']'"
                                label="Skill"
                                :value="$skill"
                                placeholder="Laravel"
                            />
                        </div>
                    @endforeach
                </div>

                <template data-repeatable-template>
                    <div data-repeatable-item class="rounded border border-border bg-background p-3">
                        <div class="mb-3 flex items-center justify-end">
                            <button type="button" data-repeatable-remove class="inline-flex items-center rounded border border-transparent bg-transparent px-3 py-2 text-xs font-bold text-foreground transition-opacity duration-150 hover:bg-secondary">Remove</button>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-foreground">Skill</label>
                            <input type="text" name="skills[__INDEX__]" class="{{ $inputBaseClass }}" placeholder="Laravel">
                        </div>
                    </div>
                </template>
            </section>

            <section class="space-y-3 border-t border-border pt-4" data-repeatable data-next-index="{{ count($academicHistory) }}" aria-labelledby="academic-history-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="academic-history-heading" class="text-lg font-bold text-foreground">Academic History</h2>
                    <x-ui.button type="button" variant="secondary" size="sm" data-repeatable-add>Add entry</x-ui.button>
                </div>
                @error('academic_history')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror

                <div data-repeatable-empty @class(['rounded border border-dashed border-border bg-secondary p-3 text-sm text-muted-foreground', 'hidden' => $academicHistory !== []])>
                    No academic entries added yet.
                </div>

                <div class="space-y-3" data-repeatable-list>
                    @foreach($academicHistory as $index => $entry)
                        <div data-repeatable-item class="rounded border border-border bg-background p-4">
                            <div class="mb-3 flex items-center justify-end">
                                <x-ui.button type="button" variant="ghost" size="sm" data-repeatable-remove>Remove</x-ui.button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                                <x-ui.form-input
                                    :name="'academic_history['.$index.'][degree]'"
                                    label="Degree"
                                    :value="$entry['degree'] ?? ''"
                                    placeholder="BSc Computer Science"
                                />

                                <x-ui.form-input
                                    :name="'academic_history['.$index.'][institution]'"
                                    label="Institution"
                                    :value="$entry['institution'] ?? ''"
                                    placeholder="MIT"
                                />

                                <x-ui.form-input
                                    :name="'academic_history['.$index.'][graduation_date]'"
                                    type="date"
                                    label="Graduation date"
                                    :value="$entry['graduation_date'] ?? ''"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <template data-repeatable-template>
                    <div data-repeatable-item class="rounded border border-border bg-background p-4">
                        <div class="mb-3 flex items-center justify-end">
                            <button type="button" data-repeatable-remove class="inline-flex items-center rounded border border-transparent bg-transparent px-3 py-2 text-xs font-bold text-foreground transition-opacity duration-150 hover:bg-secondary">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Degree</label>
                                <input type="text" name="academic_history[__INDEX__][degree]" class="{{ $inputBaseClass }}" placeholder="BSc Computer Science">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Institution</label>
                                <input type="text" name="academic_history[__INDEX__][institution]" class="{{ $inputBaseClass }}" placeholder="MIT">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Graduation date</label>
                                <input type="date" name="academic_history[__INDEX__][graduation_date]" class="{{ $inputBaseClass }}">
                            </div>
                        </div>
                    </div>
                </template>
            </section>

            <section class="space-y-3 border-t border-border pt-4" data-repeatable data-next-index="{{ count($professionalExperience) }}" aria-labelledby="professional-experience-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="professional-experience-heading" class="text-lg font-bold text-foreground">Professional Experience</h2>
                    <x-ui.button type="button" variant="secondary" size="sm" data-repeatable-add>Add entry</x-ui.button>
                </div>
                @error('professional_experience')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror

                <div data-repeatable-empty @class(['rounded border border-dashed border-border bg-secondary p-3 text-sm text-muted-foreground', 'hidden' => $professionalExperience !== []])>
                    No professional experience entries added yet.
                </div>

                <div class="space-y-3" data-repeatable-list>
                    @foreach($professionalExperience as $index => $entry)
                        <div data-repeatable-item class="rounded border border-border bg-background p-4">
                            <div class="mb-3 flex items-center justify-end">
                                <x-ui.button type="button" variant="ghost" size="sm" data-repeatable-remove>Remove</x-ui.button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <x-ui.form-input
                                    :name="'professional_experience['.$index.'][title]'"
                                    label="Title"
                                    :value="$entry['title'] ?? ''"
                                    placeholder="Senior Developer"
                                />

                                <x-ui.form-input
                                    :name="'professional_experience['.$index.'][company]'"
                                    label="Company"
                                    :value="$entry['company'] ?? ''"
                                    placeholder="Tech Corp"
                                />

                                <x-ui.form-input
                                    :name="'professional_experience['.$index.'][start_date]'"
                                    type="date"
                                    label="Start date"
                                    :value="$entry['start_date'] ?? ''"
                                />

                                <x-ui.form-input
                                    :name="'professional_experience['.$index.'][end_date]'"
                                    type="date"
                                    label="End date"
                                    :value="$entry['end_date'] ?? ''"
                                />
                            </div>

                            <div class="mt-3">
                                <x-ui.form-textarea
                                    :name="'professional_experience['.$index.'][description]'"
                                    label="Description"
                                    :value="$entry['description'] ?? ''"
                                    rows="3"
                                    placeholder="Summarize responsibilities and achievements."
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <template data-repeatable-template>
                    <div data-repeatable-item class="rounded border border-border bg-background p-4">
                        <div class="mb-3 flex items-center justify-end">
                            <button type="button" data-repeatable-remove class="inline-flex items-center rounded border border-transparent bg-transparent px-3 py-2 text-xs font-bold text-foreground transition-opacity duration-150 hover:bg-secondary">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Title</label>
                                <input type="text" name="professional_experience[__INDEX__][title]" class="{{ $inputBaseClass }}" placeholder="Senior Developer">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Company</label>
                                <input type="text" name="professional_experience[__INDEX__][company]" class="{{ $inputBaseClass }}" placeholder="Tech Corp">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Start date</label>
                                <input type="date" name="professional_experience[__INDEX__][start_date]" class="{{ $inputBaseClass }}">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">End date</label>
                                <input type="date" name="professional_experience[__INDEX__][end_date]" class="{{ $inputBaseClass }}">
                            </div>
                        </div>

                        <div class="mt-3 space-y-2">
                            <label class="block text-sm font-bold text-foreground">Description</label>
                            <textarea name="professional_experience[__INDEX__][description]" rows="3" class="{{ $textareaBaseClass }}" placeholder="Summarize responsibilities and achievements."></textarea>
                        </div>
                    </div>
                </template>
            </section>

            <section class="space-y-3 border-t border-border pt-4" data-repeatable data-next-index="{{ count($certifications) }}" aria-labelledby="certifications-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="certifications-heading" class="text-lg font-bold text-foreground">Certifications</h2>
                    <x-ui.button type="button" variant="secondary" size="sm" data-repeatable-add>Add certification</x-ui.button>
                </div>
                @error('certifications')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror

                <div data-repeatable-empty @class(['rounded border border-dashed border-border bg-secondary p-3 text-sm text-muted-foreground', 'hidden' => $certifications !== []])>
                    No certifications added yet.
                </div>

                <div class="space-y-3" data-repeatable-list>
                    @foreach($certifications as $index => $entry)
                        <div data-repeatable-item class="rounded border border-border bg-background p-4">
                            <div class="mb-3 flex items-center justify-end">
                                <x-ui.button type="button" variant="ghost" size="sm" data-repeatable-remove>Remove</x-ui.button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <x-ui.form-input
                                    :name="'certifications['.$index.'][name]'"
                                    label="Name"
                                    :value="$entry['name'] ?? ''"
                                    placeholder="AWS Certified Developer"
                                />

                                <x-ui.form-input
                                    :name="'certifications['.$index.'][issuer]'"
                                    label="Issuer"
                                    :value="$entry['issuer'] ?? ''"
                                    placeholder="Amazon"
                                />

                                <x-ui.form-input
                                    :name="'certifications['.$index.'][awarded_on]'"
                                    type="date"
                                    label="Awarded on"
                                    :value="$entry['awarded_on'] ?? ''"
                                />

                                <x-ui.photo-selector-single
                                    :name="'certifications['.$index.'][photo_id]'"
                                    label="Related photo"
                                    :availablePhotos="$availableProfilePhotos"
                                    :selectedPhotoId="$entry['photo_id'] ?? null"
                                    help="Choose an existing photo or upload a new one (optional)."
                                    :allowClear="true"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <template data-repeatable-template>
                    <div data-repeatable-item class="rounded border border-border bg-background p-4">
                        <div class="mb-3 flex items-center justify-end">
                            <button type="button" data-repeatable-remove class="inline-flex items-center rounded border border-transparent bg-transparent px-3 py-2 text-xs font-bold text-foreground transition-opacity duration-150 hover:bg-secondary">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Name</label>
                                <input type="text" name="certifications[__INDEX__][name]" class="{{ $inputBaseClass }}" placeholder="AWS Certified Developer">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Issuer</label>
                                <input type="text" name="certifications[__INDEX__][issuer]" class="{{ $inputBaseClass }}" placeholder="Amazon">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Awarded on</label>
                                <input type="date" name="certifications[__INDEX__][awarded_on]" class="{{ $inputBaseClass }}">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Related photo</label>
                                <select name="certifications[__INDEX__][photo_id]" class="{{ $inputBaseClass }}">
                                    <option value="">No related photo</option>
                                    @foreach($availableProfilePhotos as $photo)
                                        @php
                                        $photoTitle = trim((string) ($photo->title ?? ''));
                                        if ($photoTitle === '') {
                                            $photoTitle = 'Photo #'.$photo->id;
                                        }
                                        @endphp
                                        <option value="{{ $photo->id }}">{{ $photoTitle }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </template>
            </section>

            <section class="space-y-3 border-t border-border pt-4" data-repeatable data-next-index="{{ count($otherLinks) }}" aria-labelledby="other-links-heading">
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <h2 id="other-links-heading" class="text-lg font-bold text-foreground">Other Links</h2>
                    <x-ui.button type="button" variant="secondary" size="sm" data-repeatable-add>Add link</x-ui.button>
                </div>
                @error('other_links')
                    <p class="text-sm text-destructive">{{ $message }}</p>
                @enderror

                <div data-repeatable-empty @class(['rounded border border-dashed border-border bg-secondary p-3 text-sm text-muted-foreground', 'hidden' => $otherLinks !== []])>
                    No additional links added yet.
                </div>

                <div class="space-y-3" data-repeatable-list>
                    @foreach($otherLinks as $index => $entry)
                        <div data-repeatable-item class="rounded border border-border bg-background p-4">
                            <div class="mb-3 flex items-center justify-end">
                                <x-ui.button type="button" variant="ghost" size="sm" data-repeatable-remove>Remove</x-ui.button>
                            </div>

                            <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                                <x-ui.form-input
                                    :name="'other_links['.$index.'][label]'"
                                    label="Label"
                                    :value="$entry['label'] ?? ''"
                                    placeholder="Portfolio"
                                />

                                <x-ui.form-input
                                    :name="'other_links['.$index.'][url]'"
                                    label="URL"
                                    :value="$entry['url'] ?? ''"
                                    placeholder="https://example.com"
                                />
                            </div>
                        </div>
                    @endforeach
                </div>

                <template data-repeatable-template>
                    <div data-repeatable-item class="rounded border border-border bg-background p-4">
                        <div class="mb-3 flex items-center justify-end">
                            <button type="button" data-repeatable-remove class="inline-flex items-center rounded border border-transparent bg-transparent px-3 py-2 text-xs font-bold text-foreground transition-opacity duration-150 hover:bg-secondary">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">Label</label>
                                <input type="text" name="other_links[__INDEX__][label]" class="{{ $inputBaseClass }}" placeholder="Portfolio">
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-foreground">URL</label>
                                <input type="url" name="other_links[__INDEX__][url]" class="{{ $inputBaseClass }}" placeholder="https://example.com">
                            </div>
                        </div>
                    </div>
                </template>
            </section>

            <div class="flex flex-wrap items-center gap-2 border-t border-border pt-4">
                <x-ui.button type="submit">Save profile</x-ui.button>

                <a
                    href="{{ route('profile.show') }}"
                    class="inline-flex items-center rounded border border-border bg-secondary px-4 py-2 text-sm font-bold text-secondary-foreground transition-opacity duration-150 hover:opacity-90"
                >
                    Cancel
                </a>
            </div>
        </form>
    </x-ui.card>
</div>

<script>
    (() => {
        const sections = document.querySelectorAll('[data-repeatable]');

        sections.forEach((section) => {
            const list = section.querySelector('[data-repeatable-list]');
            const template = section.querySelector('template[data-repeatable-template]');
            const addButton = section.querySelector('[data-repeatable-add]');
            const emptyState = section.querySelector('[data-repeatable-empty]');

            if (!(list instanceof HTMLElement) || !(template instanceof HTMLTemplateElement) || !(addButton instanceof HTMLElement)) {
                return;
            }

            let nextIndex = Number.parseInt(section.dataset.nextIndex ?? '', 10);
            if (Number.isNaN(nextIndex)) {
                nextIndex = list.querySelectorAll('[data-repeatable-item]').length;
            }

            const syncEmptyState = () => {
                if (!(emptyState instanceof HTMLElement)) {
                    return;
                }

                const hasRows = list.querySelector('[data-repeatable-item]') !== null;
                emptyState.classList.toggle('hidden', hasRows);
            };

            addButton.addEventListener('click', () => {
                const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
                nextIndex += 1;
                list.insertAdjacentHTML('beforeend', html);
                syncEmptyState();
            });

            section.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof Element)) {
                    return;
                }

                const removeButton = target.closest('[data-repeatable-remove]');
                if (!removeButton) {
                    return;
                }

                const row = removeButton.closest('[data-repeatable-item]');
                if (row instanceof HTMLElement) {
                    row.remove();
                    syncEmptyState();
                }
            });

            syncEmptyState();
        });
    })();
</script>
@endsection
