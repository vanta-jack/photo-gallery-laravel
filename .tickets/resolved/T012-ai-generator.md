# T012: AI Image Generator (Experimental)

**Priority:** Medium  
**Type:** Feature  
**Estimated Effort:** Large  
**Depends on:** T001 (Brand Kit), T003 (Image Processor)

## Summary

Create an experimental AI image generator feature using the Google Gemini API. Users provide their own API key (stored in session only), select from presets or enter custom prompts, and generated images are saved to their photo library.

## Current State

- No AI image generation functionality exists
- No Gemini API integration
- `App\Services\ImageProcessor` will be available from T003

## Requirements

From `.tickets/active/004-site-implementations.md`:
> TODO: Innovate by adding an AI image generator that can accept any API key from Google AI studios/Gemini API key. It must handle requests that can last up to 30 seconds to 1 minute to account for generation and latency. It must have presets. Make prompts for little boy, little girl, groom, bride, man selfie in big ben, woman selfie in big ben. It must also accept custom prompts. Make sure the feature in the UI includes "experimental" tag and has a disclaimer for being unstable.

**UI Requirements from Brand Kit:**
```html
<!-- Experimental badge -->
<span class="bg-secondary text-muted-foreground font-bold text-xs px-2 py-0.5 rounded tracking-wide">
  EXPERIMENTAL
</span>

<!-- Disclaimer block -->
<div class="border border-border rounded p-3 text-xs text-muted-foreground space-y-1">
  <p class="font-bold text-foreground">Notice</p>
  <p>
    This feature is unstable and may produce unexpected results.
    VNT GmbH assumes no liability for outputs generated through
    experimental functionality.
  </p>
</div>
```

## Implementation Steps

### 1. Create `app/Services/GeminiImageService.php`

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service for generating images using Google Gemini API.
 * 
 * Note: This uses Gemini's image generation capabilities.
 * API documentation: https://ai.google.dev/api/generate-content
 */
class GeminiImageService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    protected string $model = 'gemini-2.0-flash-exp'; // Supports image generation
    
    /**
     * Available presets with optimized prompts.
     */
    public const PRESETS = [
        'little_boy' => [
            'label' => 'Little Boy',
            'prompt' => 'Portrait photograph of a cheerful little boy, approximately 5 years old, bright natural lighting, soft background, professional studio quality, warm smile, casual clothing',
        ],
        'little_girl' => [
            'label' => 'Little Girl', 
            'prompt' => 'Portrait photograph of a happy little girl, approximately 5 years old, natural lighting, soft pastel background, professional studio quality, gentle smile, casual dress',
        ],
        'groom' => [
            'label' => 'Groom',
            'prompt' => 'Portrait photograph of a groom in elegant black wedding suit, white dress shirt, formal tie, professional wedding photography, studio lighting, confident pose, celebration atmosphere',
        ],
        'bride' => [
            'label' => 'Bride',
            'prompt' => 'Portrait photograph of a bride in beautiful white wedding dress, delicate veil, professional wedding photography, soft romantic lighting, elegant pose, celebration atmosphere',
        ],
        'man_bigben' => [
            'label' => 'Man at Big Ben',
            'prompt' => 'Selfie photograph of a man tourist in front of Big Ben clock tower, London, daylight, travel photography style, casual clothing, happy expression, landmark clearly visible in background',
        ],
        'woman_bigben' => [
            'label' => 'Woman at Big Ben',
            'prompt' => 'Selfie photograph of a woman tourist in front of Big Ben clock tower, London, daylight, travel photography style, casual clothing, happy expression, landmark clearly visible in background',
        ],
    ];

    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Generate an image from a prompt.
     * 
     * @param string $prompt The image generation prompt
     * @return string|null Base64-encoded image data or null on failure
     * @throws \Exception On API errors
     */
    public function generate(string $prompt): ?string
    {
        try {
            $response = Http::timeout(90) // Allow up to 90 seconds
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => "Generate a photorealistic image: {$prompt}"]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'responseModalities' => ['image', 'text'],
                        'responseMimeType' => 'image/jpeg',
                    ],
                ]);

            if (!$response->successful()) {
                Log::warning('Gemini API error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            $data = $response->json();
            
            // Extract image data from response
            // Response structure varies - check for inline_data or other formats
            $candidates = $data['candidates'] ?? [];
            foreach ($candidates as $candidate) {
                $parts = $candidate['content']['parts'] ?? [];
                foreach ($parts as $part) {
                    if (isset($part['inlineData']['data'])) {
                        $mimeType = $part['inlineData']['mimeType'] ?? 'image/jpeg';
                        $base64 = $part['inlineData']['data'];
                        return "data:{$mimeType};base64,{$base64}";
                    }
                }
            }
            
            Log::warning('No image data in Gemini response', ['response' => $data]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Gemini API exception', [
                'message' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Get available presets.
     */
    public static function getPresets(): array
    {
        return self::PRESETS;
    }
}
```

### 2. Create `app/Http/Controllers/AiImageController.php`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Services\GeminiImageService;
use App\Services\ImageProcessor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiImageController extends Controller
{
    /**
     * Show the AI image generator form.
     */
    public function create(Request $request): View
    {
        $presets = GeminiImageService::getPresets();
        $hasApiKey = $request->session()->has('gemini_api_key');
        
        return view('ai-generator.create', compact('presets', 'hasApiKey'));
    }

    /**
     * Store the Gemini API key in session.
     */
    public function setApiKey(Request $request): RedirectResponse
    {
        $request->validate([
            'api_key' => ['required', 'string', 'min:10'],
        ]);

        $request->session()->put('gemini_api_key', $request->input('api_key'));

        return back()->with('status', 'API key configured.');
    }

    /**
     * Remove the stored API key.
     */
    public function clearApiKey(Request $request): RedirectResponse
    {
        $request->session()->forget('gemini_api_key');

        return back()->with('status', 'API key removed.');
    }

    /**
     * Generate an AI image.
     */
    public function store(Request $request, ImageProcessor $imageProcessor): RedirectResponse
    {
        $apiKey = $request->session()->get('gemini_api_key');
        
        if (!$apiKey) {
            return back()->with('error', 'API key not configured. Enter your Gemini API key first.');
        }

        $request->validate([
            'preset' => ['nullable', 'string'],
            'custom_prompt' => ['nullable', 'string', 'max:1000'],
        ]);

        // Determine prompt to use
        $presets = GeminiImageService::getPresets();
        $presetKey = $request->input('preset');
        $customPrompt = $request->input('custom_prompt');
        
        if ($customPrompt) {
            $prompt = $customPrompt;
            $title = 'AI Generated: Custom';
        } elseif ($presetKey && isset($presets[$presetKey])) {
            $prompt = $presets[$presetKey]['prompt'];
            $title = 'AI Generated: ' . $presets[$presetKey]['label'];
        } else {
            return back()->with('error', 'Select a preset or enter a custom prompt.');
        }

        try {
            $service = new GeminiImageService($apiKey);
            $imageData = $service->generate($prompt);
            
            if (!$imageData) {
                return back()->with('error', 'Image generation failed. An error was encountered.');
            }

            // Process the generated image (convert to WebP, etc.)
            $path = $imageProcessor->process($imageData);

            // Save as photo
            Photo::create([
                'user_id' => auth()->id(),
                'path' => $path,
                'title' => $title,
                'description' => "Generated with prompt: {$prompt}",
            ]);

            return redirect()
                ->route('photos.index')
                ->with('status', 'Image generated and saved to photos.');
                
        } catch (\Exception $e) {
            return back()->with('error', 'Generation error: ' . $e->getMessage());
        }
    }
}
```

### 3. Add Routes in `routes/web.php`

```php
use App\Http\Controllers\AiImageController;

Route::middleware(['auth'])->prefix('ai-generator')->name('ai-generator.')->group(function () {
    Route::get('/', [AiImageController::class, 'create'])->name('create');
    Route::post('/', [AiImageController::class, 'store'])->name('store');
    Route::post('/api-key', [AiImageController::class, 'setApiKey'])->name('api-key');
    Route::delete('/api-key', [AiImageController::class, 'clearApiKey'])->name('api-key.clear');
});
```

### 4. Create `resources/views/ai-generator/create.blade.php`

```blade
@extends('layouts.app')

@section('title', 'AI Image Generator')

@section('content')
<div class="space-y-6">
    {{-- Header with Experimental Badge --}}
    <div class="flex items-center gap-4">
        <h1 class="text-2xl font-bold">AI Image Generator</h1>
        <span class="bg-secondary text-muted-foreground font-bold text-xs px-2 py-0.5 rounded tracking-wide">
            EXPERIMENTAL
        </span>
    </div>

    {{-- Disclaimer Block --}}
    <div class="border border-border rounded p-3 text-xs text-muted-foreground space-y-1">
        <p class="font-bold text-foreground">Notice</p>
        <p>
            This feature is unstable and may produce unexpected results.
            VNT GmbH assumes no liability for outputs generated through
            experimental functionality.
        </p>
    </div>

    {{-- API Key Configuration --}}
    <div class="bg-card border border-border rounded p-6">
        <h2 class="text-xl font-bold mb-4">API Configuration</h2>
        
        @if($hasApiKey)
            <div class="flex items-center justify-between">
                <p class="text-sm text-muted-foreground">
                    API key configured. Key is stored in session only.
                </p>
                <form action="{{ route('ai-generator.api-key.clear') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-destructive hover:opacity-80">
                        Remove Key
                    </button>
                </form>
            </div>
        @else
            <form action="{{ route('ai-generator.api-key') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="api_key" class="block text-sm font-bold mb-2">Gemini API Key</label>
                    <input 
                        type="password" 
                        id="api_key" 
                        name="api_key"
                        placeholder="Enter your Google AI Studio API key"
                        required
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    >
                    <p class="text-xs text-muted-foreground mt-1">
                        Get your API key from <a href="https://aistudio.google.com/apikey" target="_blank" class="underline">Google AI Studio</a>.
                        Key is stored in session only, not in database.
                    </p>
                </div>
                <button type="submit" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">
                    Save API Key
                </button>
            </form>
        @endif
    </div>

    {{-- Generation Form --}}
    @if($hasApiKey)
        <div class="bg-card border border-border rounded p-6">
            <h2 class="text-xl font-bold mb-4">Generate Image</h2>
            
            <form action="{{ route('ai-generator.store') }}" method="POST" id="generate-form" class="space-y-6">
                @csrf
                
                {{-- Presets --}}
                <div>
                    <label class="block text-sm font-bold mb-2">Select Preset</label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach($presets as $key => $preset)
                            <label class="flex items-center gap-2 bg-secondary border border-border rounded p-3 cursor-pointer hover:bg-muted transition-colors">
                                <input type="radio" name="preset" value="{{ $key }}" class="accent-primary">
                                <span class="text-sm">{{ $preset['label'] }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Divider --}}
                <div class="flex items-center gap-4">
                    <div class="flex-1 border-t border-border"></div>
                    <span class="text-sm text-muted-foreground">or</span>
                    <div class="flex-1 border-t border-border"></div>
                </div>

                {{-- Custom Prompt --}}
                <div>
                    <label for="custom_prompt" class="block text-sm font-bold mb-2">Custom Prompt</label>
                    <textarea 
                        id="custom_prompt" 
                        name="custom_prompt"
                        rows="3"
                        placeholder="Describe the image you want to generate..."
                        class="w-full bg-background text-foreground text-sm border border-input rounded px-3 py-2 focus:outline-none focus:ring-1 focus:ring-ring"
                    ></textarea>
                    <p class="text-xs text-muted-foreground mt-1">
                        Be specific about subject, style, lighting, and composition.
                    </p>
                </div>

                {{-- Submit --}}
                <div class="flex items-center gap-4">
                    <button 
                        type="submit" 
                        id="generate-btn"
                        class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150"
                    >
                        Generate Image
                    </button>
                    <span id="loading-indicator" class="hidden text-sm text-muted-foreground">
                        Generating... This may take up to 60 seconds.
                    </span>
                </div>
            </form>
        </div>
    @endif
</div>

<script>
document.getElementById('generate-form')?.addEventListener('submit', function() {
    document.getElementById('generate-btn').disabled = true;
    document.getElementById('generate-btn').textContent = 'Generating...';
    document.getElementById('loading-indicator').classList.remove('hidden');
});
</script>
@endsection
```

### 5. Add Navigation Link

Update `resources/views/layouts/partials/header.blade.php` to include AI Generator link:

```blade
@auth
    <a href="{{ route('ai-generator.create') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">
        AI Generator
        <span class="text-xs text-muted-foreground">(exp)</span>
    </a>
@endauth
```

## Files to Create/Modify

| File | Action |
|------|--------|
| `app/Services/GeminiImageService.php` | Create |
| `app/Http/Controllers/AiImageController.php` | Create |
| `routes/web.php` | Modify |
| `resources/views/ai-generator/create.blade.php` | Create |
| `resources/views/layouts/partials/header.blade.php` | Modify |

## Acceptance Criteria

- [ ] EXPERIMENTAL badge displayed prominently
- [ ] Disclaimer block shown per brand kit spec
- [ ] User can enter their own Gemini API key
- [ ] API key stored in session only (not database)
- [ ] User can remove stored API key
- [ ] All 6 presets available and working
- [ ] Custom prompt input works
- [ ] Loading state shown during generation (up to 60s)
- [ ] Generated images saved to user's photos
- [ ] Error messages use institutional voice
- [ ] Only authenticated users can access

## Dependencies

- T001 (Brand Kit) - for styling, experimental badge, disclaimer block
- T003 (Image Processor) - for processing generated images to WebP

## Notes

- API key is NEVER stored in database - session only for security
- HTTP timeout set to 90 seconds to handle slow generation
- Uses Gemini 2.0 Flash which supports image generation
- The actual API response format may vary - check Gemini documentation
- Error handling is graceful - shows user-friendly messages
- Generated images go through ImageProcessor for consistent format
