import './bootstrap';
import Alpine from 'alpinejs';

// GSAP Imports
import { gsap } from "gsap";
import { ScrollTrigger } from "gsap/ScrollTrigger";
import { TextPlugin } from "gsap/TextPlugin";
import { ScrollToPlugin } from "gsap/ScrollToPlugin";

gsap.registerPlugin(ScrollTrigger, TextPlugin, ScrollToPlugin);

window.Alpine = Alpine;
Alpine.start();

// Initialize Animations on Load
document.addEventListener('DOMContentLoaded', () => {

    // ----------------------------------------------------------------
    // 1. Hero Animation (Logo Split & Text)
    // ----------------------------------------------------------------
    const heroTl = gsap.timeline();

    // Initial States
    gsap.set("#hero-content", { autoAlpha: 1 });
    gsap.set("#hero-logo", { scale: 0.8, opacity: 0 });
    gsap.set(".hero-text", { y: 100, opacity: 0 });
    gsap.set(".hero-subtext", { y: 20, opacity: 0 });

    heroTl.to("#hero-logo", {
        scale: 1,
        opacity: 1,
        duration: 1.5,
        ease: "power3.out"
    })
        .to(".hero-text", {
            y: 0,
            opacity: 1,
            duration: 1,
            stagger: 0.2,
            ease: "power3.out"
        }, "-=0.5")
        .to(".hero-subtext", {
            y: 0,
            opacity: 1,
            duration: 1,
            ease: "power2.out"
        }, "-=0.5");


    // ----------------------------------------------------------------
    // 2. Pillars Reveal (Sobre Section)
    // ----------------------------------------------------------------

    // Pin "Sobre Nós" section for reading - DISABLED
    // ScrollTrigger.create({
    //     trigger: "#sobre",
    //     start: "top top",
    //     end: "+=10%", // Pin for 10% of viewport height (minimal lock)
    //     pin: true,
    //     scrub: true
    // });

    // Subtle entrance animation for Sobre Nós content
    gsap.from("#sobre .max-w-4xl", {
        scrollTrigger: {
            trigger: "#sobre",
            start: "top 70%",
            toggleActions: "play none none reverse"
        },
        y: 30,
        opacity: 0,
        duration: 1.2,
        ease: "power2.out"
    });

    const pillars = document.querySelectorAll('#sobre-pilares > div');

    if (pillars.length > 0) {
        pillars.forEach((pillar, index) => {
            gsap.from(pillar.children[0], { // Target the container inside
                scrollTrigger: {
                    trigger: pillar,
                    start: "top 80%",
                    toggleActions: "play none none reverse"
                },
                y: 50,
                opacity: 0,
                duration: 1,
                ease: "power2.out",
                delay: 0.1
            });
        });
    }

    // ----------------------------------------------------------------
    // 3. Dimensions Slider (Pinned Section + Rotation)
    // ----------------------------------------------------------------
    const sliderSection = document.querySelector('#dimensions-slider');

    if (sliderSection) {
        // Elements
        const rotator = document.querySelector('#dimensions-rotator');

        const slides = [
            { text: '#slide-intro', overlay: '#overlay-intro' },
            { text: '#slide-gestao', overlay: '#overlay-gestao' },
            { text: '#slide-metodologia', overlay: '#overlay-metodologia' },
            { text: '#slide-fundamentos', overlay: '#overlay-fundamentos' }
        ];

        // Master Timeline
        const tl = gsap.timeline({
            scrollTrigger: {
                trigger: sliderSection,
                pin: true,
                start: "top top",
                end: "+=300%", // Reduced scroll distance for faster unlock
                scrub: 1,
                onUpdate: (self) => {
                    // Update Active Dot based on progress
                    const progress = self.progress;
                    const dots = document.querySelectorAll('.slider-dot');
                    let activeIndex = 0;

                    // progress is 0-1. Total duration ~7.5s
                    // 0   -> Intro
                    // ~0.2 (1.5/7.5) -> Gestao
                    // ~0.53 (4.0/7.5) -> Metodologia
                    // ~0.86 (6.5/7.5) -> Fundamentos

                    if (progress < 0.15) activeIndex = 0;
                    else if (progress < 0.45) activeIndex = 1;
                    else if (progress < 0.75) activeIndex = 2;
                    else activeIndex = 3;

                    dots.forEach((dot, index) => {
                        if (index === activeIndex) {
                            dot.classList.add('active', 'bg-[#c9a66b]', 'scale-125');
                            dot.classList.remove('bg-white/20');
                        } else {
                            dot.classList.remove('active', 'bg-[#c9a66b]', 'scale-125');
                            dot.classList.add('bg-white/20');
                        }
                    });
                }
            }
        });

        // Labels for navigation
        tl.addLabel("intro", 0)

            // 0. Initial Hold
            .to({}, { duration: 0.5 })

            // 1. Intro Out
            .to([slides[0].text, slides[0].overlay], { autoAlpha: 0, duration: 1, ease: "power1.inOut" })

            .addLabel("gestao", ">") // At end of Intro Out

            // 2. Rotator & Gestão In
            .to(rotator, { autoAlpha: 1, duration: 1, ease: "power1.inOut" })
            .to([slides[1].text, slides[1].overlay], { autoAlpha: 1, duration: 1, ease: "power1.inOut" }, "<")

            // 2. Gestão Out -> Metodologia In
            .to([slides[1].text, slides[1].overlay], { autoAlpha: 0, duration: 1, ease: "power1.inOut" }, "+=0.5")
            .to([slides[2].text, slides[2].overlay], { autoAlpha: 1, duration: 1, ease: "power1.inOut" }, "<")
            .to(rotator, { rotation: 180, duration: 2, ease: "power1.inOut" }, "<") // 0 -> 180

            .addLabel("metodologia", ">") // At end of rotation

            // 3. Metodologia Out -> Fundamentos In
            .to([slides[2].text, slides[2].overlay], { autoAlpha: 0, duration: 1, ease: "power1.inOut" }, "+=0.5")
            .to([slides[3].text, slides[3].overlay], { autoAlpha: 1, duration: 1, ease: "power1.inOut" }, "<")
            .to(rotator, { rotation: 360, duration: 2, ease: "power1.inOut" }, "<") // 180 -> 360

            .addLabel("fundamentos", ">"); // At end of rotation

        // End buffer
        tl.to({}, { duration: 1 });

        // Navigation Click Logic
        const navDots = document.querySelectorAll('.slider-dot');
        navDots.forEach(dot => {
            dot.addEventListener('click', (e) => {
                const index = parseInt(e.target.dataset.index);
                let targetLabel = "intro";
                if (index === 1) targetLabel = "gestao";
                if (index === 2) targetLabel = "metodologia";
                if (index === 3) targetLabel = "fundamentos";

                // Use GSAP's native labelToScroll for accuracy
                const targetScroll = tl.scrollTrigger.labelToScroll(targetLabel);

                if (targetScroll !== undefined && targetScroll !== null) {
                    gsap.to(window, {
                        scrollTo: targetScroll,
                        duration: 1.5,
                        ease: "power2.out"
                    });
                }
            });
        });
    }

});
