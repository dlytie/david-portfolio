@props(['projects'])

<section id="projects" class="chapter chapter--projects section-observer" aria-labelledby="projects-title" data-scene-chapter="projects">
    <div class="section-shell">
        <div class="section-heading section-heading--split" data-reveal>
            <div>
                <p class="eyebrow"><span>04</span>Proyek pilihan</p>
                <h2 id="projects-title">Masalah nyata.<br><em>Solusi yang digunakan.</em></h2>
            </div>
            <p>Setiap proyek berangkat dari kebutuhan praktis—mencatat data, merapikan proses, atau membangun layanan yang dapat diandalkan.</p>
        </div>

        <div class="projects-grid">
            @foreach ($projects as $project)
                <article class="project-card" style="--project-accent: {{ $project['accent'] }}" data-reveal>
                    <button
                        class="project-card__button"
                        type="button"
                        @click="openProject('{{ $project['slug'] }}', $event.currentTarget)"
                        aria-label="Lihat detail proyek {{ $project['title'] }}"
                    >
                        <div class="project-card__visual">
                            @if ($project['has_image'])
                                <img src="{{ asset($project['image']) }}" alt="Tampilan proyek {{ $project['title'] }}" loading="lazy">
                            @else
                                <div class="project-placeholder" aria-hidden="true">
                                    <span class="project-placeholder__label">{{ strtoupper(substr($project['slug'], 0, 2)) }}</span>
                                    <span class="project-placeholder__window">
                                        <i></i><i></i><i></i>
                                    </span>
                                    <span class="project-placeholder__grid"></span>
                                </div>
                            @endif

                            <span class="project-card__status">{{ $project['status'] }}</span>
                            <span class="project-card__open" aria-hidden="true">↗</span>
                        </div>

                        <div class="project-card__body">
                            <div class="project-card__meta">
                                <span>{{ $project['number'] }}</span>
                                <span>{{ $project['category'] }}</span>
                            </div>
                            <h3>{{ $project['title'] }}</h3>
                            <p>{{ $project['summary'] }}</p>
                            <ul aria-label="Teknologi yang digunakan">
                                @foreach ($project['technologies'] as $technology)
                                    <li>{{ $technology }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </button>
                </article>
            @endforeach
        </div>
    </div>
</section>
