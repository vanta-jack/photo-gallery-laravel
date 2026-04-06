@extends('layouts.app')

@section('title', 'Albums')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-foreground">Albums</h1>
    @auth
        <a href="{{ route('albums.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Create Album</a>
    @endauth
</div>

<div class="bg-card border border-border rounded p-4 mb-6 space-y-4">
    <div class="flex flex-col md:flex-row gap-4 items-start md:items-center justify-between">
        <div class="flex-1 w-full md:w-auto">
            <input
                type="text"
                id="albumSearch"
                placeholder="Search albums..."
                class="w-full px-4 py-2 bg-background text-foreground border border-border rounded focus:outline-none focus:ring-2 focus:ring-primary"
            >
        </div>

        <div class="flex gap-3 items-center w-full md:w-auto justify-between md:justify-start">
            <select
                id="albumSort"
                class="px-4 py-2 bg-background text-foreground border border-border rounded focus:outline-none focus:ring-2 focus:ring-primary"
            >
                <option value="default">Default (Favorites First)</option>
                <option value="newest">Newest First</option>
                <option value="oldest">Oldest First</option>
            </select>

            <div class="flex gap-1 border border-border rounded overflow-hidden">
                <button
                    id="gridViewBtn"
                    class="px-3 py-2 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity"
                    title="Grid View"
                    type="button"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                </button>
                <button
                    id="listViewBtn"
                    class="px-3 py-2 bg-background text-foreground hover:bg-secondary transition-colors"
                    title="List View"
                    type="button"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            @auth
                <button
                    id="batchModeToggle"
                    type="button"
                    class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150"
                >
                    Batch Mode
                </button>
            @endauth
        </div>
    </div>

    @auth
        <div id="batchToolbar" class="hidden border border-border rounded p-3 bg-background space-y-3">
            <div class="text-sm text-foreground">
                <span class="font-bold" id="batchSelectedCount">0</span> album(s) selected
            </div>

            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('albums.batch.delete') }}" class="batch-action-form" data-confirm="Delete selected albums? This action cannot be undone.">
                    @csrf
                    <div class="batch-album-ids"></div>
                    <button type="submit" data-batch-action-btn class="bg-destructive text-destructive-foreground font-bold text-sm px-4 py-2 rounded border border-destructive hover:opacity-90 transition-opacity duration-150 disabled:opacity-50" disabled>
                        Delete Selected
                    </button>
                </form>

                <form method="POST" action="{{ route('albums.batch.visibility') }}" class="batch-action-form">
                    @csrf
                    <input type="hidden" name="is_private" value="1">
                    <div class="batch-album-ids"></div>
                    <button type="submit" data-batch-action-btn class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 disabled:opacity-50" disabled>
                        Set Private
                    </button>
                </form>

                <form method="POST" action="{{ route('albums.batch.visibility') }}" class="batch-action-form">
                    @csrf
                    <input type="hidden" name="is_private" value="0">
                    <div class="batch-album-ids"></div>
                    <button type="submit" data-batch-action-btn class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 disabled:opacity-50" disabled>
                        Set Public
                    </button>
                </form>

                <form method="POST" action="{{ route('albums.batch.favorite') }}" class="batch-action-form">
                    @csrf
                    <input type="hidden" name="is_favorite" value="1">
                    <div class="batch-album-ids"></div>
                    <button type="submit" data-batch-action-btn class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 disabled:opacity-50" disabled>
                        Favorite
                    </button>
                </form>

                <form method="POST" action="{{ route('albums.batch.favorite') }}" class="batch-action-form">
                    @csrf
                    <input type="hidden" name="is_favorite" value="0">
                    <div class="batch-album-ids"></div>
                    <button type="submit" data-batch-action-btn class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 disabled:opacity-50" disabled>
                        Unfavorite
                    </button>
                </form>
            </div>
        </div>
    @endauth
</div>

<div id="albumsContainer" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @forelse($albums as $album)
        @php
            $canBatchManage = auth()->check() && (auth()->id() === $album->user_id || auth()->user()->role === 'admin');
        @endphp

        <div class="album-item bg-card text-card-foreground border border-border rounded p-4"
             data-title="{{ strtolower($album->title) }}"
             data-created="{{ $album->created_at->timestamp }}"
             data-favorite="{{ $album->is_favorite ? '1' : '0' }}">
            @auth
                <div class="batch-checkbox-wrap hidden mb-3">
                    @if($canBatchManage)
                        <label class="inline-flex items-center gap-2 text-sm text-foreground cursor-pointer">
                            <input type="checkbox" class="batch-album-checkbox rounded border-input" value="{{ $album->id }}">
                            <span>Select</span>
                        </label>
                    @else
                        <span class="text-xs text-muted-foreground">View-only album</span>
                    @endif
                </div>
            @endauth

            @if($album->is_favorite)
                <div class="text-yellow-500 mb-2" title="Favorite">
                    <x-icon name="star" class="w-5 h-5" />
                </div>
            @endif

            @if($album->coverPhoto)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($album->coverPhoto->path) }}" alt="{{ $album->title }}" class="album-cover w-full h-48 object-cover rounded mb-3">
            @else
                <div class="album-cover w-full h-48 bg-secondary rounded mb-3 flex items-center justify-center text-muted-foreground gap-2">
                    <x-icon name="folder" class="w-5 h-5" />
                    <span>No cover</span>
                </div>
            @endif

            <h3 class="font-bold text-foreground mb-1 album-title">{{ $album->title }}</h3>
            <p class="text-muted-foreground text-sm mb-2 album-author">by {{ $album->user->first_name ?? 'Unknown' }}</p>

            @if($album->is_private)
                <span class="text-muted-foreground text-sm flex items-center gap-1">
                    <x-icon name="lock" class="w-4 h-4" />
                    Private
                </span>
            @endif

            <a href="{{ route('albums.show', $album) }}" class="bg-secondary text-secondary-foreground font-bold text-sm px-4 py-2 rounded border border-border hover:opacity-90 transition-opacity duration-150 inline-block mt-3">View</a>
        </div>
    @empty
        <div class="col-span-full bg-card text-card-foreground border border-border rounded p-12 text-center">
            <x-icon name="folder" class="w-24 h-24 mx-auto mb-4 text-muted-foreground" />
            <h3 class="text-xl font-bold text-foreground mb-2">No Albums Yet</h3>
            <p class="text-muted-foreground mb-6">Start organizing your photos by creating your first album!</p>
            @auth
                <a href="{{ route('albums.create') }}" class="bg-primary text-primary-foreground font-bold text-sm px-6 py-3 rounded border border-primary hover:opacity-90 transition-opacity duration-150 inline-block">Create Album Now</a>
            @endauth
        </div>
    @endforelse
</div>

<div id="emptySearchState" class="hidden col-span-full bg-card text-card-foreground border border-border rounded p-12 text-center">
    <x-icon name="search" class="w-24 h-24 mx-auto mb-4 text-muted-foreground" />
    <h3 class="text-xl font-bold text-foreground mb-2">No Albums Found</h3>
    <p class="text-muted-foreground">Try adjusting your search or filters</p>
</div>

<div class="mt-8">
    {{ $albums->links() }}
</div>

<script>
(function() {
    const searchInput = document.getElementById('albumSearch');
    const sortSelect = document.getElementById('albumSort');
    const gridViewBtn = document.getElementById('gridViewBtn');
    const listViewBtn = document.getElementById('listViewBtn');
    const albumsContainer = document.getElementById('albumsContainer');
    const albumItems = Array.from(document.querySelectorAll('.album-item'));
    const emptySearchState = document.getElementById('emptySearchState');

    const savedView = localStorage.getItem('albumsView') || 'grid';
    applyView(savedView);

    gridViewBtn.addEventListener('click', function() {
        applyView('grid');
        localStorage.setItem('albumsView', 'grid');
    });

    listViewBtn.addEventListener('click', function() {
        applyView('list');
        localStorage.setItem('albumsView', 'list');
    });

    function applyView(view) {
        if (view === 'grid') {
            albumsContainer.className = 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4';
            gridViewBtn.className = 'px-3 py-2 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity';
            listViewBtn.className = 'px-3 py-2 bg-background text-foreground hover:bg-secondary transition-colors';

            albumItems.forEach(item => {
                item.classList.remove('flex', 'flex-row', 'items-center');
                const cover = item.querySelector('.album-cover');
                if (cover) {
                    cover.classList.remove('!w-32', '!h-32', '!mb-0', '!mr-4');
                    cover.classList.add('w-full', 'h-48', 'mb-3');
                }
            });
        } else {
            albumsContainer.className = 'flex flex-col gap-4';
            gridViewBtn.className = 'px-3 py-2 bg-background text-foreground hover:bg-secondary transition-colors';
            listViewBtn.className = 'px-3 py-2 bg-primary text-primary-foreground font-bold hover:opacity-90 transition-opacity';

            albumItems.forEach(item => {
                item.classList.add('flex', 'flex-row', 'items-center');
                const cover = item.querySelector('.album-cover');
                if (cover) {
                    cover.classList.remove('w-full', 'h-48', 'mb-3');
                    cover.classList.add('!w-32', '!h-32', '!mb-0', '!mr-4');
                }
            });
        }
    }

    searchInput.addEventListener('input', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        const sortBy = sortSelect.value;

        let visibleItems = albumItems.filter(item => {
            const title = item.dataset.title;

            if (!searchTerm) return true;

            let searchIndex = 0;
            for (let i = 0; i < title.length && searchIndex < searchTerm.length; i++) {
                if (title[i] === searchTerm[searchIndex]) {
                    searchIndex++;
                }
            }
            return searchIndex === searchTerm.length;
        });

        if (sortBy === 'newest') {
            visibleItems.sort((a, b) => parseInt(b.dataset.created) - parseInt(a.dataset.created));
        } else if (sortBy === 'oldest') {
            visibleItems.sort((a, b) => parseInt(a.dataset.created) - parseInt(b.dataset.created));
        } else {
            visibleItems.sort((a, b) => {
                const favA = parseInt(a.dataset.favorite);
                const favB = parseInt(b.dataset.favorite);
                if (favA !== favB) {
                    return favB - favA;
                }
                return parseInt(b.dataset.created) - parseInt(a.dataset.created);
            });
        }

        albumItems.forEach(item => item.style.display = 'none');

        if (visibleItems.length > 0) {
            emptySearchState.classList.add('hidden');
            visibleItems.forEach(item => {
                item.style.display = '';
                albumsContainer.appendChild(item);
            });
        } else if (albumItems.length > 0) {
            emptySearchState.classList.remove('hidden');
            albumsContainer.appendChild(emptySearchState);
        }
    }

    const batchModeToggle = document.getElementById('batchModeToggle');
    const batchToolbar = document.getElementById('batchToolbar');
    const batchSelectedCount = document.getElementById('batchSelectedCount');
    const batchCheckboxWrappers = Array.from(document.querySelectorAll('.batch-checkbox-wrap'));
    const batchCheckboxes = Array.from(document.querySelectorAll('.batch-album-checkbox'));
    const batchActionButtons = Array.from(document.querySelectorAll('[data-batch-action-btn]'));
    const batchForms = Array.from(document.querySelectorAll('.batch-action-form'));
    let batchModeEnabled = false;

    const updateBatchSelection = () => {
        const selected = batchCheckboxes.filter((checkbox) => checkbox.checked);

        if (batchSelectedCount) {
            batchSelectedCount.textContent = String(selected.length);
        }

        batchActionButtons.forEach((button) => {
            button.disabled = selected.length === 0;
        });
    };

    if (batchModeToggle && batchToolbar) {
        batchModeToggle.addEventListener('click', function () {
            batchModeEnabled = !batchModeEnabled;
            batchToolbar.classList.toggle('hidden', !batchModeEnabled);
            batchModeToggle.textContent = batchModeEnabled ? 'Exit Batch Mode' : 'Batch Mode';
            batchCheckboxWrappers.forEach((wrapper) => {
                wrapper.classList.toggle('hidden', !batchModeEnabled);
            });

            if (!batchModeEnabled) {
                batchCheckboxes.forEach((checkbox) => {
                    checkbox.checked = false;
                });
            }

            updateBatchSelection();
        });

        batchCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener('change', updateBatchSelection);
        });

        batchForms.forEach((form) => {
            form.addEventListener('submit', function (event) {
                const selectedIds = batchCheckboxes
                    .filter((checkbox) => checkbox.checked)
                    .map((checkbox) => checkbox.value);

                if (selectedIds.length === 0) {
                    event.preventDefault();
                    return;
                }

                if (form.dataset.confirm && !window.confirm(form.dataset.confirm)) {
                    event.preventDefault();
                    return;
                }

                const idsContainer = form.querySelector('.batch-album-ids');
                if (!idsContainer) {
                    return;
                }

                idsContainer.innerHTML = '';
                selectedIds.forEach((albumId) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'album_ids[]';
                    input.value = albumId;
                    idsContainer.appendChild(input);
                });
            });
        });

        updateBatchSelection();
    }

    filterAndSort();
})();
</script>
@endsection
