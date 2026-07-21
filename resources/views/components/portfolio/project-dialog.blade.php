@props(['projects'])

<dialog
    class="project-dialog"
    x-ref="projectDialog"
    aria-label="Detail proyek"
    @close="onDialogClosed"
    @click="closeDialogFromBackdrop($event)"
>
    <button class="dialog-close" type="button" @click="closeProject" aria-label="Tutup detail proyek">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
    </button>

    @foreach ($projects as $project)
        <article
            class="dialog-project"
            style="--project-accent: {{ $project['accent'] }}"
            x-show="activeProject === '{{ $project['slug'] }}'"
            x-cloak
        >
            <div class="dialog-project__visual">
                @if ($project['has_image'])
                    <img src="{{ asset($project['image']) }}" alt="Tampilan proyek {{ $project['title'] }}">
                @else
                    <div class="project-placeholder project-placeholder--large" aria-hidden="true">
                        <span class="project-placeholder__label">{{ strtoupper(substr($project['slug'], 0, 2)) }}</span>
                        <span class="project-placeholder__window"><i></i><i></i><i></i></span>
                        <span class="project-placeholder__grid"></span>
                    </div>
                @endif
            </div>

            <div class="dialog-project__content">
                <p class="eyebrow"><span>{{ $project['number'] }}</span>{{ $project['category'] }}</p>
                <h2>{{ $project['title'] }}</h2>
                <p class="dialog-project__summary">{{ $project['summary'] }}</p>

                <div class="case-grid">
                    <div><span>Masalah</span><p>{{ $project['problem'] }}</p></div>
                    <div><span>Solusi</span><p>{{ $project['solution'] }}</p></div>
                    <div><span>Hasil</span><p>{{ $project['impact'] }}</p></div>
                </div>

                <div class="project-evidence">
                    <div class="project-evidence__narrative">
                        <div>
                            <span>Kontribusi saya</span>
                            <p>{{ $project['contribution'] }}</p>
                        </div>
                        <div>
                            <span>Tantangan teknis</span>
                            <p>{{ $project['challenge'] }}</p>
                        </div>
                    </div>

                    <div class="project-feature-list">
                        <span>Fitur utama</span>
                        <ul>
                            @foreach ($project['features'] as $feature)
                                <li>{{ $feature }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="dialog-project__footer">
                    <ul>
                        @foreach ($project['technologies'] as $technology)
                            <li>{{ $technology }}</li>
                        @endforeach
                    </ul>

                    <div>
                        @if ($project['demo_url'])
                            <a class="button button--primary" href="{{ $project['demo_url'] }}" target="_blank" rel="noreferrer">Buka demo ↗</a>
                        @endif
                        @if ($project['source_url'])
                            <a class="button button--ghost" href="{{ $project['source_url'] }}" target="_blank" rel="noreferrer">Source code ↗</a>
                        @endif
                    </div>
                </div>
            </div>
        </article>
    @endforeach
</dialog>
