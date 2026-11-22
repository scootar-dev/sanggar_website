// ============================================
//  SANGGAR MULYA BHAKTI — app.js
// ============================================

document.addEventListener('DOMContentLoaded', () => {

    // ---- Navbar scroll effect ----
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });

    // ---- Hamburger / mobile menu ----
    const hamburger  = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    const backdrop = document.getElementById('mobileMenuBackdrop');
    const setMenuOpen = (open) => {
        if (!mobileMenu || !hamburger) return;
        mobileMenu.classList.toggle('open', open);
        mobileMenu.inert = !open;
        backdrop?.classList.toggle('open', open);
        hamburger.setAttribute('aria-expanded', String(open));
        hamburger.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        document.body.style.overflow = open ? 'hidden' : '';

        // animate hamburger → X
        const spans = hamburger.querySelectorAll('span');
        if (open) {
            spans[0].style.transform = 'translateY(7px) rotate(45deg)';
            spans[1].style.opacity   = '0';
            spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
        } else {
            spans.forEach(s => { s.style.transform = ''; s.style.opacity = ''; });
        }
    };

    hamburger?.addEventListener('click', () => setMenuOpen(!mobileMenu?.classList.contains('open')));
    backdrop?.addEventListener('click', () => setMenuOpen(false));
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && mobileMenu?.classList.contains('open')) {
            setMenuOpen(false);
            hamburger?.focus();
        }
    });
    document.addEventListener('focusin', (event) => {
        if (mobileMenu?.classList.contains('open') && !mobileMenu.contains(event.target) && !hamburger?.contains(event.target)) {
            setMenuOpen(false);
        }
    });
    window.matchMedia('(min-width: 1024px)').addEventListener('change', (event) => {
        if (event.matches) setMenuOpen(false);
    });

    // close mobile menu on link click
    mobileMenu?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            setMenuOpen(false);
        });
    });

    // Explicit touch control keeps hover-only card content reachable on phones.
    document.querySelectorAll('.topeng-card-3d').forEach((card, index) => {
        const inner = card.querySelector('.topeng-card-inner');
        if (!inner) return;
        inner.id = `topeng-detail-${index}`;
        const mobile = window.matchMedia('(max-width: 767px)');
        const syncCard = () => {
            card.tabIndex = mobile.matches ? 0 : -1;
            if (mobile.matches) {
                card.setAttribute('role', 'button');
                card.setAttribute('aria-label', 'Balik kartu topeng untuk melihat penjelasan');
                card.setAttribute('aria-expanded', String(card.classList.contains('is-flipped')));
            } else {
                card.removeAttribute('role');
                card.removeAttribute('aria-label');
                card.removeAttribute('aria-expanded');
                card.classList.remove('is-flipped');
            }
        };
        const flip = () => {
            if (!mobile.matches) return;
            const open = card.classList.toggle('is-flipped');
            card.setAttribute('aria-expanded', String(open));
            card.setAttribute('aria-label', open ? 'Balik kartu untuk melihat foto topeng' : 'Balik kartu topeng untuk melihat penjelasan');
        };
        card.addEventListener('click', flip);
        card.addEventListener('keydown', event => {
            if (mobile.matches && (event.key === 'Enter' || event.key === ' ')) {
                event.preventDefault();
                flip();
            }
        });
        mobile.addEventListener('change', syncCard);
        syncCard();
    });

    // ---- Intersection Observer — fade-up on scroll ----
    const fadeEls = document.querySelectorAll(
        '.stat-item, .about-inner, .archive-card, .dok-card'
    );

    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.animation = 'fadeUp .6s ease both';
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        fadeEls.forEach(el => {
            el.style.opacity = '0';
            io.observe(el);
        });
    }

});
