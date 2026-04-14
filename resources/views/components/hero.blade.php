@props(['title' => 'Welcome', 'subtitle' => ''])

<section class="hero">
    <h1 class="hero-title">{{ $title }}</h1>
    <p class="hero- subtitle">{{ $subtitle }}</p>

    <div class="hero-actions">
        @guest
            <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary">Register</a>
        @endguest
        @auth
            @if(Auth::user()->is_admin)
                <a href="{{ route('admin.dashboard') }}" class="btn btn-primary">Admin Dashboard</a>
            @else
                <a href="{{ route('dashboard') }}" class="btn btn-primary">User</a>
            @endif
        @endauth

    </div>
</section>
