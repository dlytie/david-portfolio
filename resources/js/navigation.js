export function initNavigation() {
    const header = document.querySelector('[data-site-header]');
    const links = [...document.querySelectorAll('[data-nav-link]')];
    const sections = [...document.querySelectorAll('.section-observer[id]')];

    if (!header) {
        return () => {};
    }

    const updateHeader = () => {
        header.classList.toggle('is-scrolled', window.scrollY > 28);
    };

    const observer = new IntersectionObserver(
        (entries) => {
            const visible = entries
                .filter((entry) => entry.isIntersecting)
                .sort((a, b) => b.intersectionRatio - a.intersectionRatio)[0];

            if (!visible) {
                return;
            }

            links.forEach((link) => {
                const isActive = link.getAttribute('href') === `#${visible.target.id}`;
                link.classList.toggle('is-active', isActive);

                if (isActive) {
                    link.setAttribute('aria-current', 'location');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        },
        {
            rootMargin: '-32% 0px -52% 0px',
            threshold: [0.05, 0.2, 0.5],
        },
    );

    sections.forEach((section) => observer.observe(section));
    window.addEventListener('scroll', updateHeader, { passive: true });
    updateHeader();

    return () => {
        observer.disconnect();
        window.removeEventListener('scroll', updateHeader);
    };
}
