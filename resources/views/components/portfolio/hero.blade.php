@props(['portfolio'])

<section id="home" class="hero section-observer" aria-labelledby="hero-title">
    @if ($portfolio['scene']['enabled'])
        <div
            class="scene-stage"
            data-scene-root
            data-asset-base="{{ $portfolio['scene']['asset_base_path'] }}"
            data-custom-model="{{ $portfolio['scene']['has_custom_model'] ? asset($portfolio['scene']['custom_model']) : '' }}"
            aria-hidden="true"
        >
            <canvas class="scene-canvas" data-scene-canvas></canvas>

            <div class="scene-loader" data-scene-loader>
                <span class="scene-loader__line"><i></i></span>
                <span class="scene-loader__text">Menyiapkan workspace</span>
            </div>

            <div class="scene-fallback" data-scene-fallback>
                <div class="fallback-monitor"><span>DAVID.OS</span></div>
                <div class="fallback-desk"></div>
            </div>

            <p class="scene-hint" data-scene-hint>
                Gerakkan pointer · Monitor dan router dapat dipilih
            </p>
        </div>
    @endif

    <div class="section-shell hero__layout">
        <div class="hero__copy" data-reveal>
            <p class="eyebrow"><span>01</span>{{ $portfolio['profile']['eyebrow'] }}</p>

            <h1 id="hero-title">
                Teknologi yang
                <span>tetap bekerja.</span>
            </h1>

            <p class="hero__lead">{{ $portfolio['profile']['summary'] }}</p>

            <div class="hero__actions">
                <a class="button button--primary" href="#projects">
                    Lihat proyek
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>

                @if ($portfolio['profile']['has_cv'])
                    <a class="button button--ghost" href="{{ asset($portfolio['profile']['cv']) }}" download>
                        Unduh CV
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"/></svg>
                    </a>
                @else
                    <a class="button button--ghost" href="#about">
                        Tentang saya
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14m0 0 5-5m-5 5-5-5"/></svg>
                    </a>
                @endif
            </div>

            <div class="hero__meta">
                <div>
                    <span>Berbasis di</span>
                    <strong>{{ $portfolio['profile']['location'] }}</strong>
                </div>
                <div>
                    <span>Fokus</span>
                    <strong>Support · Server · Web</strong>
                </div>
            </div>
        </div>

        <div class="hero__scene-space" aria-hidden="true"></div>
    </div>

    <a class="scroll-cue" href="#about">
        <span>Scroll untuk menjelajah</span>
        <i aria-hidden="true"></i>
    </a>
</section>
