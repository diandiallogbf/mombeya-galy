import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';

window.Alpine = Alpine;
Alpine.plugin(collapse);

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;
const meta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

const postJson = async (url, body = {}) => {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrf() },
        body: JSON.stringify(body),
    });
    const data = await response.json().catch(() => ({}));
    return { ok: response.ok, data };
};

/* ------------------------------------------------------------------ */
/* Global stores                                                       */
/* ------------------------------------------------------------------ */
Alpine.store('cart', {
    count: Number(meta('cart-count') || 0),
    total: meta('cart-total') || '',
});

Alpine.store('wishlist', {
    count: Number(meta('wishlist-count') || 0),
});

Alpine.store('toast', {
    items: [],
    show(message, link = null) {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, link });
        setTimeout(() => this.dismiss(id), 3500);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

/* ------------------------------------------------------------------ */
/* Add to cart (product card quick-add and product page)               */
/* ------------------------------------------------------------------ */
Alpine.data('addToCart', (config = {}) => ({
    size: config.size ?? '',
    color: config.color ?? '',
    quantity: 1,
    open: false,
    loading: false,
    error: '',

    choose(size) {
        this.size = size;
        if (config.instant) this.submit();
    },

    async submit() {
        this.error = '';
        if (config.needsSize && !this.size) {
            this.error = 'Veuillez choisir une taille.';
            this.open = true;
            return;
        }
        if (config.needsColor && !this.color) {
            this.error = 'Veuillez choisir une couleur.';
            return;
        }
        this.loading = true;
        try {
            const { ok, data } = await postJson(config.url, {
                product_id: config.productId,
                size: this.size || null,
                color: this.color || null,
                quantity: this.quantity,
            });
            if (!ok) {
                this.error = data.message || 'Une erreur est survenue.';
                return;
            }
            Alpine.store('cart').count = data.count;
            Alpine.store('cart').total = data.total;
            Alpine.store('toast').show(data.message || 'Produit ajouté au panier', { label: 'Voir le panier', url: config.cartUrl });
            this.open = false;
            window.dispatchEvent(new CustomEvent('cart-updated'));
        } catch (e) {
            this.error = 'Connexion impossible, veuillez réessayer.';
        } finally {
            this.loading = false;
        }
    },
}));

/* ------------------------------------------------------------------ */
/* Favourites                                                          */
/* ------------------------------------------------------------------ */
Alpine.data('wishlistButton', (url, active = false) => ({
    active,
    busy: false,
    async toggle() {
        if (this.busy) return;
        this.busy = true;
        const { ok, data } = await postJson(url);
        this.busy = false;
        if (!ok) return;
        this.active = data.added;
        Alpine.store('wishlist').count = data.count;
        Alpine.store('toast').show(data.message);
    },
}));

/* ------------------------------------------------------------------ */
/* Product gallery + fullscreen viewer                                 */
/* ------------------------------------------------------------------ */
Alpine.data('gallery', (images = []) => ({
    images,
    active: 0,
    viewer: false,
    open(index) {
        this.active = index;
        this.viewer = true;
        document.body.classList.add('overflow-hidden');
    },
    close() {
        this.viewer = false;
        document.body.classList.remove('overflow-hidden');
    },
    next() {
        this.active = (this.active + 1) % this.images.length;
    },
    prev() {
        this.active = (this.active - 1 + this.images.length) % this.images.length;
    },
}));

/* ------------------------------------------------------------------ */
/* Carousels & reveal-on-scroll                                        */
/* ------------------------------------------------------------------ */
function initCarousels(root = document) {
    root.querySelectorAll('[data-hero]').forEach((el) => {
        if (el.swiper) return;
        const counter = el.querySelector('[data-counter]');
        new Swiper(el, {
            modules: [Autoplay, EffectFade, Navigation, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: el.querySelectorAll('.swiper-slide').length > 1,
            speed: 900,
            autoplay: { delay: 6000, disableOnInteraction: false },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            navigation: { nextEl: el.querySelector('[data-next]'), prevEl: el.querySelector('[data-prev]') },
            on: {
                slideChange(swiper) {
                    if (counter) counter.textContent = String(swiper.realIndex + 1).padStart(2, '0');
                },
            },
        });
    });

    root.querySelectorAll('[data-carousel]').forEach((el) => {
        if (el.swiper) return;
        const wrapper = el.closest('[data-carousel-wrapper]') || el;
        new Swiper(el, {
            modules: [Navigation, Pagination],
            speed: 600,
            spaceBetween: 16,
            slidesPerView: 1.6,
            breakpoints: {
                480: { slidesPerView: 2.2, spaceBetween: 18 },
                768: { slidesPerView: 3.2, spaceBetween: 22 },
                1100: { slidesPerView: 4, spaceBetween: 24 },
            },
            navigation: { nextEl: wrapper.querySelector('[data-next]'), prevEl: wrapper.querySelector('[data-prev]') },
            pagination: { el: wrapper.querySelector('.swiper-pagination'), clickable: true },
        });
    });

    root.querySelectorAll('[data-testimonials]').forEach((el) => {
        if (el.swiper) return;
        new Swiper(el, {
            modules: [Autoplay, Pagination],
            spaceBetween: 24,
            slidesPerView: 1,
            autoplay: { delay: 6000, disableOnInteraction: false },
            breakpoints: { 768: { slidesPerView: 2 }, 1100: { slidesPerView: 3 } },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
        });
    });
}

function initReveal() {
    const items = document.querySelectorAll('[data-reveal]');
    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { rootMargin: '0px 0px -60px 0px' },
    );
    items.forEach((el) => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', () => {
    initCarousels();
    initReveal();
});

Alpine.start();
