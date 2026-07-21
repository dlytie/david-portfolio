@props(['portfolio'])

<section id="contact" class="chapter chapter--contact section-observer" aria-labelledby="contact-title" data-scene-chapter="contact">
    <div class="section-shell contact-grid">
        <div class="contact-copy" data-reveal>
            <p class="eyebrow"><span>05</span>Kontak</p>
            <h2 id="contact-title">Mari membuat sistem<br><em>yang benar-benar berguna.</em></h2>
            <p>Terbuka untuk peluang IT Support, dukungan infrastruktur, dan pengembangan aplikasi web yang berangkat dari kebutuhan nyata.</p>

            <div class="contact-links" aria-label="Tautan kontak">
                @if ($portfolio['contact']['email'])
                    <a href="mailto:{{ $portfolio['contact']['email'] }}">Email <span>↗</span></a>
                @endif
                @if ($portfolio['contact']['whatsapp'])
                    <a href="{{ $portfolio['contact']['whatsapp'] }}" target="_blank" rel="noreferrer">WhatsApp <span>↗</span></a>
                @endif
                @if ($portfolio['contact']['github'])
                    <a href="{{ $portfolio['contact']['github'] }}" target="_blank" rel="noreferrer">GitHub <span>↗</span></a>
                @endif
                @if ($portfolio['contact']['linkedin'])
                    <a href="{{ $portfolio['contact']['linkedin'] }}" target="_blank" rel="noreferrer">LinkedIn <span>↗</span></a>
                @endif
            </div>
        </div>

        <div class="contact-panel" data-reveal>
            @if ($portfolio['contact']['enabled'] && $portfolio['contact']['email'])
                @if (session('contact_success'))
                    <div class="form-alert form-alert--success" role="status">{{ session('contact_success') }}</div>
                @endif

                @if ($errors->has('contact'))
                    <div class="form-alert form-alert--error" role="alert">{{ $errors->first('contact') }}</div>
                @endif

                <form
                    action="{{ route('contact.store') }}"
                    method="POST"
                    class="contact-form"
                    @submit="contactSubmitting = true"
                    :aria-busy="contactSubmitting.toString()"
                >
                    @csrf
                    <div class="honeypot" aria-hidden="true">
                        <label for="website">Website</label>
                        <input id="website" name="website" type="text" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="form-row">
                        <label>
                            <span>Nama</span>
                            <input name="name" value="{{ old('name') }}" required maxlength="80" autocomplete="name">
                            @error('name')<small>{{ $message }}</small>@enderror
                        </label>
                        <label>
                            <span>Email</span>
                            <input name="email" type="email" value="{{ old('email') }}" required maxlength="160" autocomplete="email">
                            @error('email')<small>{{ $message }}</small>@enderror
                        </label>
                    </div>

                    <label>
                        <span>Subjek</span>
                        <input name="subject" value="{{ old('subject') }}" required maxlength="120">
                        @error('subject')<small>{{ $message }}</small>@enderror
                    </label>

                    <label>
                        <span>Pesan</span>
                        <textarea name="message" required minlength="20" maxlength="3000" rows="5">{{ old('message') }}</textarea>
                        @error('message')<small>{{ $message }}</small>@enderror
                    </label>

                    <button class="button button--primary" type="submit" :disabled="contactSubmitting">
                        <span x-show="!contactSubmitting">Kirim pesan</span>
                        <span x-show="contactSubmitting" x-cloak>Mengirim...</span>
                        <svg x-show="!contactSubmitting" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        <span class="button-spinner" x-show="contactSubmitting" x-cloak aria-hidden="true"></span>
                    </button>
                </form>
            @else
                <div class="contact-ready">
                    <span class="contact-ready__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 5h16v14H4zM4 7l8 6 8-6"/></svg>
                    </span>
                    <p>Saluran kontak sedang disiapkan.</p>
                    <h3>Strukturnya sudah siap—detail kontak dapat ditambahkan kapan saja.</h3>
                    <a class="button button--ghost" href="#projects">Lihat pekerjaan saya</a>

                    @if (app()->environment('local'))
                        <small class="setup-note">Tambahkan email dan tautan sosial melalui file <code>.env</code> sebelum website dipublikasikan.</small>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>
