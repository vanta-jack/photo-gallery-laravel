<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Photo Gallery')</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: system-ui, -apple-system, sans-serif; line-height: 1.6; color: #333; background: #f5f5f5; }
        
        /* Navigation */
        nav { background: #2c3e50; color: white; padding: 1rem 2rem; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        nav .container { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        nav h1 { font-size: 1.5rem; }
        nav ul { list-style: none; display: flex; gap: 1.5rem; }
        nav a { color: white; text-decoration: none; transition: opacity 0.2s; }
        nav a:hover { opacity: 0.8; }
        
        /* Flash messages */
        .flash { max-width: 1200px; margin: 1rem auto; padding: 1rem; border-radius: 4px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        /* Main container */
        .container { max-width: 1200px; margin: 2rem auto; padding: 0 2rem; }
        
        /* Cards */
        .card { background: white; padding: 2rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        
        /* Grid layouts */
        .grid { display: grid; gap: 2rem; }
        .grid-2 { grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); }
        .grid-3 { grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); }
        .grid-4 { grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); }
        
        /* Buttons */
        .btn { display: inline-block; padding: 0.5rem 1rem; background: #3498db; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; transition: background 0.2s; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-secondary { background: #95a5a6; }
        .btn-secondary:hover { background: #7f8c8d; }
        
        /* Forms */
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        input, textarea, select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-size: 1rem; }
        textarea { min-height: 100px; font-family: inherit; }
        
        /* Utility */
        .text-muted { color: #6c757d; font-size: 0.9rem; }
        .mb-2 { margin-bottom: 2rem; }
        .flex { display: flex; gap: 1rem; align-items: center; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <nav>
        <div class="container">
            <h1>📸 Photo Gallery</h1>
            <ul>
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('photos.index') }}">Photos</a></li>
                <li><a href="{{ route('albums.index') }}">Albums</a></li>
                <li><a href="{{ route('posts.index') }}">Posts</a></li>
                <li><a href="{{ route('guestbook.index') }}">Guestbook</a></li>
                @auth
                    <li><a href="{{ route('milestones.index') }}">Milestones</a></li>
                    <li><a href="{{ route('profile.edit') }}">Profile</a></li>
                @endauth
            </ul>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('status'))
        <div class="flash success">✓ {{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="flash error">✗ {{ session('error') }}</div>
    @endif

    {{-- Main content --}}
    <div class="container">
        @yield('content')
    </div>
</body>
</html>
