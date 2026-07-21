@props(['portfolio'])

<section id="about" class="chapter chapter--about section-observer" aria-labelledby="about-title" data-scene-chapter="about">
    <div class="section-shell">
        <div class="section-heading" data-reveal>
            <p class="eyebrow"><span>02</span>Tentang saya</p>
            <h2 id="about-title">Menjembatani perangkat,<br><em>infrastruktur, dan aplikasi.</em></h2>
        </div>

        <div class="about-grid">
            <article class="profile-card" data-reveal>
                <div class="profile-card__visual">
                    @if ($portfolio['profile']['has_photo'])
                        <img src="{{ asset($portfolio['profile']['photo']) }}" alt="Foto profil {{ $portfolio['profile']['name'] }}">
                    @else
                        <div class="profile-placeholder" aria-label="Foto profil dapat ditambahkan kemudian">
                            <span>{{ $portfolio['profile']['initials'] }}</span>
                            <small>Photo ready</small>
                        </div>
                    @endif
                    <span class="profile-card__index">D/01</span>
                </div>

                <div class="profile-card__content">
                    <p>{{ $portfolio['profile']['about'] }}</p>

                    <dl class="profile-facts">
                        <div><dt>Peran</dt><dd>{{ $portfolio['profile']['title'] }}</dd></div>
                        <div><dt>Lokasi</dt><dd>Medan, Indonesia</dd></div>
                    </dl>
                </div>
            </article>

            <div class="about-details">
                <div class="stats-grid" data-reveal>
                    @foreach ($portfolio['stats'] as $stat)
                        <div class="stat-card">
                            <strong>{{ $stat['value'] }}</strong>
                            <span>{{ $stat['label'] }}</span>
                        </div>
                    @endforeach
                </div>

                <article class="timeline-card" data-reveal>
                    <div class="card-kicker">Pengalaman</div>
                    @foreach ($portfolio['experience'] as $experience)
                        <div class="timeline-item">
                            <span class="timeline-item__dot" aria-hidden="true"></span>
                            <div>
                                <p>{{ $experience['period'] }}</p>
                                <h3>{{ $experience['role'] }}</h3>
                                <strong>{{ $experience['organization'] }}</strong>
                                <span>{{ $experience['description'] }}</span>
                            </div>
                        </div>
                    @endforeach

                    <div class="education-item">
                        <span>Pendidikan</span>
                        <div>
                            <h3>{{ $portfolio['education']['degree'] }}</h3>
                            <p>{{ $portfolio['education']['institution'] }} · {{ $portfolio['education']['period'] }}</p>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>

<section id="skills" class="chapter chapter--skills section-observer" aria-labelledby="skills-title" data-scene-chapter="skills">
    <div class="section-shell">
        <div class="section-heading section-heading--split" data-reveal>
            <div>
                <p class="eyebrow"><span>03</span>Kapabilitas</p>
                <h2 id="skills-title">Dari meja bantuan<br><em>hingga server.</em></h2>
            </div>
            <p>Satu pola kerja yang sama: pahami masalahnya, sederhanakan solusinya, lalu pastikan dapat dipelihara.</p>
        </div>

        <div class="skills-list">
            @foreach ($portfolio['skill_groups'] as $group)
                <article class="skill-row" data-reveal>
                    <span class="skill-row__number">{{ $group['number'] }}</span>
                    <div class="skill-row__title">
                        <h3>{{ $group['title'] }}</h3>
                        <p>{{ $group['description'] }}</p>
                    </div>
                    <ul aria-label="Teknologi {{ $group['title'] }}">
                        @foreach ($group['skills'] as $skill)
                            <li>{{ $skill }}</li>
                        @endforeach
                    </ul>
                    <span class="skill-row__arrow" aria-hidden="true">↗</span>
                </article>
            @endforeach
        </div>
    </div>
</section>
