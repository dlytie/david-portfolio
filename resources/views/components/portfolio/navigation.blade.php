@props(['portfolio'])

<header class="site-header" data-site-header>
    <div class="nav-shell">
        <a href="#home" class="brand" aria-label="Kembali ke beranda">
            <span class="brand__mark" aria-hidden="true">D</span>
            <span class="brand__text">
                <strong>{{ $portfolio['profile']['name'] }}</strong>
                <small>Digital workspace</small>
            </span>
        </a>

        <nav class="nav-desktop" aria-label="Navigasi utama">
            <a href="#about" data-nav-link>Tentang</a>
            <a href="#skills" data-nav-link>Keahlian</a>
            <a href="#projects" data-nav-link>Proyek</a>
            <a href="#contact" data-nav-link>Kontak</a>
        </nav>

        <div class="nav-actions">
            @if ($portfolio['profile']['available'])
                <a class="availability" href="#contact">
                    <span aria-hidden="true"></span>
                    Terbuka untuk peluang
                </a>
            @endif

            <button
                class="menu-toggle"
                type="button"
                @click="menuOpen = !menuOpen"
                :aria-expanded="menuOpen.toString()"
                aria-controls="mobile-menu"
                aria-label="Buka atau tutup menu"
            >
                <span></span><span></span>
            </button>
        </div>
    </div>

    <nav
        id="mobile-menu"
        class="nav-mobile"
        aria-label="Navigasi ponsel"
        x-cloak
        x-show="menuOpen"
        x-transition.opacity.duration.180ms
        @click.outside="menuOpen = false"
    >
        <a href="#about" @click="menuOpen = false">Tentang <span>01</span></a>
        <a href="#skills" @click="menuOpen = false">Keahlian <span>02</span></a>
        <a href="#projects" @click="menuOpen = false">Proyek <span>03</span></a>
        <a href="#contact" @click="menuOpen = false">Kontak <span>04</span></a>
    </nav>
</header>
