<header class="bg-card border-b border-border">
    <nav class="max-w-5xl mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div class="flex gap-6">
                <a href="{{ route('home') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Home</a>
                <a href="{{ route('photos.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Photos</a>
                <a href="{{ route('albums.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Albums</a>
                <a href="{{ route('posts.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Posts</a>
                <a href="{{ route('guestbook.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Guestbook</a>
                @auth
                    <a href="{{ route('milestones.index') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Milestones</a>
                @endauth
            </div>
            <div class="flex gap-4 items-center">
                <form action="{{ route('theme.toggle') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm text-muted-foreground hover:text-foreground transition-colors duration-150">
                        Toggle Theme
                    </button>
                </form>
                @auth
                    <a href="{{ route('profile.edit') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Profile</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-foreground hover:opacity-80 transition-opacity duration-150">Login</a>
                    <a href="{{ route('register') }}" class="bg-primary text-primary-foreground font-bold text-sm px-4 py-2 rounded border border-primary hover:opacity-90 transition-opacity duration-150">Register</a>
                @endauth
            </div>
        </div>
    </nav>
</header>
