@extends('layouts.app')

@section('title', $album->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('albums.index') }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block">
        Back to Albums
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2">
        @if($album->photos->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($album->photos as $index => $photo)
                    <div class="bg-card text-card-foreground border border-border rounded p-4">
                        <button type="button" onclick="openLightbox({{ $index }})" class="w-full text-left">
                            <img src="{{ \Illuminate\Support\Facades\Storage::url($photo->path) }}" alt="{{ $photo->title }}" class="w-full h-48 object-cover rounded mb-3 hover:opacity-90 transition-opacity duration-150 cursor-pointer">
                        </button>
                        <h3 class="font-bold text-foreground mb-2 truncate">
                            <a href="{{ route('photos.show', $photo) }}" class="hover:opacity-75 transition-opacity duration-150">
                                {{ $photo->title }}
                            </a>
                        </h3>
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between text-muted-foreground">
                                <span>Average rating</span>
                                <span class="font-bold text-foreground">{{ number_format($photo->ratings->avg('rating') ?? 0, 1) }}/5</span>
                            </div>
                            <div class="flex justify-between text-muted-foreground">
                                <span>Ratings</span>
                                <span class="font-bold text-foreground">{{ $photo->ratings->count() }}</span>
                            </div>
                            <div class="flex justify-between text-muted-foreground">
                                <span>Comments</span>
                                <span class="font-bold text-foreground">{{ $photo->comments->count() }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-card text-card-foreground border border-border rounded p-8 text-center">
                <p class="text-muted-foreground text-lg">No photos in this album yet.</p>
            </div>
        @endif
    </div>

    <aside class="space-y-4">
        <div class="bg-card text-card-foreground border border-border rounded p-4">
            <h1 class="text-2xl font-bold text-foreground mb-3">{{ $album->title }}</h1>
            @if($album->description)
                <p class="text-foreground mb-4">{{ $album->description }}</p>
            @endif

            <dl class="space-y-2 text-sm">
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Owner</dt>
                    <dd class="text-foreground font-bold">{{ $album->user?->first_name }} {{ $album->user?->last_name }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Created</dt>
                    <dd class="text-foreground font-bold">{{ $album->created_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Updated</dt>
                    <dd class="text-foreground font-bold">{{ $album->updated_at->format('M d, Y') }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Photos</dt>
                    <dd class="text-foreground font-bold">{{ $album->photos->count() }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="text-muted-foreground">Privacy</dt>
                    <dd class="text-foreground font-bold">
                        @if($album->is_private)
                            <span class="inline-flex items-center gap-1">
                                🔒 Private
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1">
                                🌐 Public
                            </span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @can('update', $album)
            <div class="bg-card text-card-foreground border border-border rounded p-4 space-y-3">
                <a href="{{ route('albums.edit', $album) }}" class="w-full bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block text-center">
                    Edit Album
                </a>

                @can('delete', $album)
                    <button type="button" onclick="openDeleteModal()" class="w-full bg-destructive text-white font-bold text-sm px-4 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">
                        Delete Album
                    </button>
                @endcan
            </div>
        @endcan
    </aside>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="hidden fixed inset-0 bg-black bg-opacity-95 z-50 flex items-center justify-center">
    <!-- Close button -->
    <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-4xl font-bold hover:opacity-75 transition-opacity z-50 w-12 h-12 flex items-center justify-center">
        &times;
    </button>

    <!-- Navigation buttons -->
    <button onclick="navigateLightbox(-1)" class="absolute left-4 top-1/2 -translate-y-1/2 text-white text-5xl font-bold hover:opacity-75 transition-opacity z-50 w-16 h-16 flex items-center justify-center">
        &#8249;
    </button>
    <button onclick="navigateLightbox(1)" class="absolute right-4 top-1/2 -translate-y-1/2 text-white text-5xl font-bold hover:opacity-75 transition-opacity z-50 w-16 h-16 flex items-center justify-center">
        &#8250;
    </button>

    <!-- Photo container -->
    <div class="flex flex-col items-center justify-center max-w-7xl max-h-full p-8 w-full">
        <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[70vh] object-contain rounded">
        
        <!-- Photo info overlay -->
        <div class="mt-6 text-center text-white max-w-2xl">
            <h2 id="lightbox-title" class="text-2xl font-bold mb-2"></h2>
            <p id="lightbox-description" class="text-zinc-300 mb-4"></p>
            <div class="flex justify-center gap-6 text-sm text-zinc-400">
                <div>
                    <span class="font-bold text-white" id="lightbox-rating">0</span>/5 rating
                    <span class="text-zinc-500">(<span id="lightbox-rating-count">0</span> ratings)</span>
                </div>
                <div>
                    <span class="font-bold text-white" id="lightbox-comments">0</span> comments
                </div>
                <div>
                    Photo <span class="font-bold text-white" id="lightbox-position">1</span> of <span class="font-bold text-white" id="lightbox-total">1</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
@can('delete', $album)
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-card text-card-foreground border border-border rounded-lg max-w-md w-full p-6 shadow-xl">
        <h2 class="text-xl font-bold text-foreground mb-4">Delete Album</h2>
        
        <div class="mb-6">
            <p class="text-foreground mb-4">
                <strong class="text-destructive">Warning:</strong> This album cannot be retrieved again. Are you sure?
            </p>
            
            @if($album->photos->count() > 0)
            <div class="bg-muted border border-border rounded p-4 mb-4">
                <p class="text-sm text-muted-foreground mb-3">
                    This album contains <strong class="text-foreground">{{ $album->photos->count() }}</strong> photo(s).
                </p>
                
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" id="deletePhotos" name="delete_photos" value="1" class="mt-1 w-4 h-4 accent-destructive">
                    <span class="text-sm text-foreground">
                        <strong>Also delete all photos in this album</strong>
                        <br>
                        <span class="text-muted-foreground">Default: Only the album will be deleted. Photos will remain in the gallery.</span>
                    </span>
                </label>
            </div>
            @endif
        </div>
        
        <form id="deleteForm" action="{{ route('albums.destroy', $album) }}" method="POST">
            @csrf
            @method('DELETE')
            <input type="hidden" id="deletePhotosInput" name="delete_photos" value="0">
            
            <div class="flex gap-3">
                <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150">
                    Cancel
                </button>
                <button type="submit" class="flex-1 bg-destructive text-white font-bold text-sm px-4 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150">
                    Delete Album
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Lightbox functionality
    @php
        $lightboxPhotos = $album->photos->map(function ($photo) {
            return [
                'id' => $photo->id,
                'path' => \Illuminate\Support\Facades\Storage::url($photo->path),
                'title' => $photo->title,
                'description' => $photo->description,
                'rating' => number_format($photo->ratings->avg('rating') ?? 0, 1),
                'rating_count' => $photo->ratings->count(),
                'comments_count' => $photo->comments->count(),
                'photo_url' => route('photos.show', $photo),
            ];
        })->values();
    @endphp

    const photos = @json($lightboxPhotos);

    let currentPhotoIndex = 0;

    function openLightbox(index) {
        currentPhotoIndex = index;
        showLightboxPhoto();
        document.getElementById('lightbox').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').classList.add('hidden');
        document.body.style.overflow = '';
    }

    function navigateLightbox(direction) {
        currentPhotoIndex += direction;
        
        // Wrap around
        if (currentPhotoIndex < 0) {
            currentPhotoIndex = photos.length - 1;
        } else if (currentPhotoIndex >= photos.length) {
            currentPhotoIndex = 0;
        }
        
        showLightboxPhoto();
    }

    function showLightboxPhoto() {
        const photo = photos[currentPhotoIndex];
        
        document.getElementById('lightbox-image').src = photo.path;
        document.getElementById('lightbox-image').alt = photo.title;
        document.getElementById('lightbox-title').textContent = photo.title;
        document.getElementById('lightbox-description').textContent = photo.description || '';
        document.getElementById('lightbox-rating').textContent = photo.rating;
        document.getElementById('lightbox-rating-count').textContent = photo.rating_count;
        document.getElementById('lightbox-comments').textContent = photo.comments_count;
        document.getElementById('lightbox-position').textContent = currentPhotoIndex + 1;
        document.getElementById('lightbox-total').textContent = photos.length;
    }

    // Keyboard navigation for lightbox
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        if (!lightbox.classList.contains('hidden')) {
            if (e.key === 'ArrowLeft') {
                e.preventDefault();
                navigateLightbox(-1);
            } else if (e.key === 'ArrowRight') {
                e.preventDefault();
                navigateLightbox(1);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                closeLightbox();
            }
        }
    });

    // Close on background click
    document.getElementById('lightbox').addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });
</script>

<script>
    function openDeleteModal() {
        document.getElementById('deleteModal').classList.remove('hidden');
    }
    
    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }
    
    // Update hidden input when checkbox changes
    const checkbox = document.getElementById('deletePhotos');
    const hiddenInput = document.getElementById('deletePhotosInput');
    
    if (checkbox) {
        checkbox.addEventListener('change', function() {
            hiddenInput.value = this.checked ? '1' : '0';
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('deleteModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        const lightbox = document.getElementById('lightbox');
        const deleteModal = document.getElementById('deleteModal');
        
        // Only handle escape for delete modal if lightbox is not open
        if (e.key === 'Escape' && !lightbox.classList.contains('hidden')) {
            // Lightbox handles its own escape
            return;
        } else if (e.key === 'Escape') {
            closeDeleteModal();
        }
    });
</script>
@endcan
@endsection
