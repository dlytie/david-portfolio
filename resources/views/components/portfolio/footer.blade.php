@props(['portfolio'])

<footer class="site-footer">
    <div class="section-shell site-footer__inner">
        <a class="brand" href="#home" aria-label="Kembali ke atas">
            <span class="brand__mark" aria-hidden="true">D</span>
            <span class="brand__text"><strong>{{ $portfolio['profile']['name'] }}</strong><small>IT Support & Web Developer</small></span>
        </a>

        <p>© {{ date('Y') }} {{ $portfolio['profile']['name'] }}. Dirancang untuk tetap cepat, jelas, dan mudah dikembangkan.</p>

        <a class="back-to-top" href="#home">Kembali ke atas <span aria-hidden="true">↑</span></a>
    </div>
</footer>
