# T007: User Schema Update for About Me / CV

**Priority:** Medium  
**Type:** Feature  
**Estimated Effort:** Medium

## Summary

Add CV-related fields to the users table to support the About Me page functionality per the project requirements.

## Current State

Current `users` table schema:
```
id, role, email, first_name, last_name, password, profile_photo_id, created_at, updated_at
```

No fields exist for CV/About Me content such as bio, contact info, academic history, or skills.

## Requirements

From `.tickets/active/004-site-implementations.md`:

> An "About Me" page with a Curriculum Vitae (CV) is a personal website section...
> Key Components to Include:
> - Professional Summary: A brief, compelling introduction highlighting expertise and goals.
> - Contact Information: Name, phone number, email, and LinkedIn profile.
> - Academic History: Degrees, institution names, and graduation dates.
> - Professional Experience (if there are any): Detailed work history.
> - Skills & Qualifications: Key technical and soft skills relevant to the field.

## Implementation Steps

### 1. Create Migration

```bash
php artisan make:migration add_cv_fields_to_users_table
```

Migration content:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Professional summary/bio
            $table->text('bio')->nullable()->after('profile_photo_id');
            
            // Contact information
            $table->string('phone', 20)->nullable()->after('bio');
            $table->string('linkedin')->nullable()->after('phone');
            
            // CV sections stored as JSON
            // academic_history: [{degree: "BSc Computer Science", institution: "MIT", year: "2020"}, ...]
            $table->json('academic_history')->nullable()->after('linkedin');
            
            // professional_experience: [{title: "Developer", company: "Acme", years: "2020-2022", description: "..."}, ...]
            $table->json('professional_experience')->nullable()->after('academic_history');
            
            // skills: ["PHP", "Laravel", "JavaScript", ...]
            $table->json('skills')->nullable()->after('professional_experience');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bio',
                'phone',
                'linkedin',
                'academic_history',
                'professional_experience',
                'skills',
            ]);
        });
    }
};
```

### 2. Update `app/Models/User.php`

Add new fields to `#[Fillable]` attribute and JSON casts:

```php
<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'role',
    'email',
    'first_name',
    'last_name',
    'password',
    'profile_photo_id',
    'bio',
    'phone',
    'linkedin',
    'academic_history',
    'professional_experience',
    'skills',
])]
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'academic_history' => 'array',
            'professional_experience' => 'array',
            'skills' => 'array',
        ];
    }

    // ... existing relationships remain unchanged
}
```

### 3. Update `App\Http\Requests\UpdateUserRequest.php`

Add validation for new fields:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            
            // New CV fields
            'bio' => ['nullable', 'string', 'max:5000'],
            'phone' => ['nullable', 'string', 'max:20'],
            'linkedin' => ['nullable', 'url', 'max:255'],
            
            // JSON array fields
            'academic_history' => ['nullable', 'array'],
            'academic_history.*.degree' => ['required_with:academic_history', 'string', 'max:255'],
            'academic_history.*.institution' => ['required_with:academic_history', 'string', 'max:255'],
            'academic_history.*.year' => ['nullable', 'string', 'max:20'],
            
            'professional_experience' => ['nullable', 'array'],
            'professional_experience.*.title' => ['required_with:professional_experience', 'string', 'max:255'],
            'professional_experience.*.company' => ['required_with:professional_experience', 'string', 'max:255'],
            'professional_experience.*.years' => ['nullable', 'string', 'max:50'],
            'professional_experience.*.description' => ['nullable', 'string', 'max:2000'],
            
            'skills' => ['nullable', 'array'],
            'skills.*' => ['string', 'max:100'],
        ];
    }
}
```

### 4. Update `resources/views/users/edit.blade.php`

Add form sections for CV fields. The JSON fields use repeatable input groups:

```blade
@extends('layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-card border border-border rounded p-6">
    <h1 class="text-2xl font-bold mb-6">Edit Profile</h1>

    <form action="{{ route('profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            {{-- Basic Information --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Basic Information</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="first_name" class="block text-sm font-bold mb-2">First Name</label>
                        <input type="text" id="first_name" name="first_name" 
                            value="{{ old('first_name', $user->first_name) }}"
                            class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        @error('first_name')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="last_name" class="block text-sm font-bold mb-2">Last Name</label>
                        <input type="text" id="last_name" name="last_name" 
                            value="{{ old('last_name', $user->last_name) }}"
                            class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        @error('last_name')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label for="email" class="block text-sm font-bold mb-2">Email</label>
                    <input type="email" id="email" name="email" 
                        value="{{ old('email', $user->email) }}"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                    @error('email')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                </div>
            </section>

            {{-- Professional Summary --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Professional Summary</h2>
                
                <div>
                    <label for="bio" class="block text-sm font-bold mb-2">Bio</label>
                    <textarea id="bio" name="bio" rows="4"
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    >{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                </div>
            </section>

            {{-- Contact Information --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Contact Information</h2>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="phone" class="block text-sm font-bold mb-2">Phone</label>
                        <input type="tel" id="phone" name="phone" 
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        @error('phone')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                    </div>

                    <div>
                        <label for="linkedin" class="block text-sm font-bold mb-2">LinkedIn URL</label>
                        <input type="url" id="linkedin" name="linkedin" 
                            value="{{ old('linkedin', $user->linkedin) }}"
                            placeholder="https://linkedin.com/in/username"
                            class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                        @error('linkedin')<span class="text-destructive text-sm">{{ $message }}</span>@enderror
                    </div>
                </div>
            </section>

            {{-- Academic History --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Academic History</h2>
                <p class="text-sm text-muted-foreground mb-4">Enter each degree on a separate line in format: Degree | Institution | Year</p>
                
                <textarea id="academic_history_text" name="academic_history_text" rows="4"
                    placeholder="BSc Computer Science | MIT | 2020&#10;MSc Data Science | Stanford | 2022"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring font-mono"
                >@if($user->academic_history)@foreach($user->academic_history as $item){{ $item['degree'] ?? '' }} | {{ $item['institution'] ?? '' }} | {{ $item['year'] ?? '' }}
@endforeach @endif</textarea>
                <input type="hidden" name="academic_history" id="academic_history_json">
            </section>

            {{-- Professional Experience --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Professional Experience</h2>
                <p class="text-sm text-muted-foreground mb-4">Enter each position. Use blank lines to separate entries.</p>
                
                <textarea id="professional_experience_text" name="professional_experience_text" rows="8"
                    placeholder="Software Developer | Acme Corp | 2020-2022&#10;Developed web applications using Laravel and Vue.js&#10;&#10;Senior Developer | Tech Inc | 2022-Present&#10;Leading development team"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring font-mono"
                >@if($user->professional_experience)@foreach($user->professional_experience as $item){{ $item['title'] ?? '' }} | {{ $item['company'] ?? '' }} | {{ $item['years'] ?? '' }}
{{ $item['description'] ?? '' }}

@endforeach @endif</textarea>
                <input type="hidden" name="professional_experience" id="professional_experience_json">
            </section>

            {{-- Skills --}}
            <section>
                <h2 class="text-xl font-bold mb-4">Skills</h2>
                <p class="text-sm text-muted-foreground mb-4">Enter skills separated by commas.</p>
                
                <input type="text" name="skills_text" 
                    value="{{ old('skills_text', $user->skills ? implode(', ', $user->skills) : '') }}"
                    placeholder="PHP, Laravel, JavaScript, Vue.js, MySQL"
                    class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring">
                <input type="hidden" name="skills" id="skills_json">
            </section>

            <div class="pt-4">
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                    Update Profile
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Convert text inputs to JSON before form submission
document.querySelector('form').addEventListener('submit', function(e) {
    // Parse academic history
    const academicText = document.getElementById('academic_history_text').value;
    const academicHistory = academicText.split('\n')
        .map(line => line.trim())
        .filter(line => line)
        .map(line => {
            const parts = line.split('|').map(p => p.trim());
            return { degree: parts[0] || '', institution: parts[1] || '', year: parts[2] || '' };
        });
    document.getElementById('academic_history_json').value = JSON.stringify(academicHistory);
    
    // Parse professional experience
    const expText = document.getElementById('professional_experience_text').value;
    const expBlocks = expText.split('\n\n').filter(block => block.trim());
    const experience = expBlocks.map(block => {
        const lines = block.split('\n').map(l => l.trim()).filter(l => l);
        const headerParts = (lines[0] || '').split('|').map(p => p.trim());
        return {
            title: headerParts[0] || '',
            company: headerParts[1] || '',
            years: headerParts[2] || '',
            description: lines.slice(1).join(' ')
        };
    });
    document.getElementById('professional_experience_json').value = JSON.stringify(experience);
    
    // Parse skills
    const skillsText = document.querySelector('[name="skills_text"]').value;
    const skills = skillsText.split(',').map(s => s.trim()).filter(s => s);
    document.getElementById('skills_json').value = JSON.stringify(skills);
});
</script>
@endsection
```

### 5. Update UserController to Handle JSON Parsing

Update the controller to handle the JSON fields:

```php
public function update(UpdateUserRequest $request, User $user = null): RedirectResponse
{
    $user = $user ?? auth()->user();
    
    $this->authorize('update', $user);

    $data = $request->validated();
    
    // Parse JSON fields from form
    if ($request->has('academic_history') && is_string($request->academic_history)) {
        $data['academic_history'] = json_decode($request->academic_history, true);
    }
    if ($request->has('professional_experience') && is_string($request->professional_experience)) {
        $data['professional_experience'] = json_decode($request->professional_experience, true);
    }
    if ($request->has('skills') && is_string($request->skills)) {
        $data['skills'] = json_decode($request->skills, true);
    }
    
    // Remove text fields used for form display
    unset($data['academic_history_text'], $data['professional_experience_text'], $data['skills_text']);

    $user->update($data);

    return redirect()
        ->route('profile.edit')
        ->with('status', 'Profile updated.');
}
```

## Files to Create/Modify

| File | Action |
|------|--------|
| `database/migrations/xxxx_add_cv_fields_to_users_table.php` | Create |
| `app/Models/User.php` | Modify |
| `app/Http/Requests/UpdateUserRequest.php` | Modify |
| `app/Http/Controllers/UserController.php` | Modify |
| `resources/views/users/edit.blade.php` | Modify |

## Acceptance Criteria

- [ ] Migration runs without errors: `php artisan migrate`
- [ ] All new fields are present in users table
- [ ] User can edit bio, phone, linkedin fields
- [ ] Academic history saves and displays correctly as structured data
- [ ] Professional experience saves and displays correctly
- [ ] Skills save as array and display as comma-separated list
- [ ] JSON casts work correctly (data retrieved as arrays, not strings)

## Dependencies

None - this is a schema update ticket.

## Blocks

- T008 (About Me Public Page) - needs these fields to display

## Notes

- JSON fields are used for flexibility - no additional tables needed
- SQLite supports JSON column type and json_extract() functions
- The form uses plain text inputs with JavaScript parsing to avoid complex JS UI
- Professional experience uses double newlines to separate entries
