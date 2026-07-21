import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

export function initPageMotion() {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (prefersReducedMotion) {
        return () => {};
    }

    const animations = [];

    document.querySelectorAll('[data-reveal]').forEach((element) => {
        const animation = gsap.from(element, {
            y: 32,
            opacity: 0,
            duration: 0.9,
            ease: 'power3.out',
            scrollTrigger: {
                trigger: element,
                start: 'top 88%',
                once: true,
            },
        });

        animations.push(animation);
    });

    return () => {
        animations.forEach((animation) => {
            animation.scrollTrigger?.kill();
            animation.kill();
        });
    };
}
