import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import Swiper from 'swiper';
import { Autoplay, EffectFade, Navigation, Pagination } from 'swiper/modules';
import 'swiper/css';
import 'swiper/css/effect-fade';

window.Alpine = Alpine;
Alpine.plugin(collapse);

const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content;

/* ------------------------------------------------------------------ */
/* Global stores: cart badge + toasts                                  */
/* ------------------------------------------------------------------ */
Alpine.store('cart', {
    count: Number(document.querySelector('meta[name="cart-count"]')?.content || 0),
    total: document.querySelector('meta[name="cart-total"]')?.content || '',
});

Alpine.store('toast', {
    items: [],
    show(message, type = 'success') {
        const id = Date.now() + Math.random();
        this.items.push({ id, message, type });
        setTimeout(() => this.dismiss(id), 3500);
    },
    dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
    },
});

/* ------------------------------------------------------------------ */
/* Add to cart (used by product cards, quick view and product page)    */
/* ------------------------------------------------------------------ */
Alpine.data('addToCart', (config = {}) => ({
    productId: config.productId,
    size: config.size ?? '',
    color: config.color ?? '',
    quantity: 1,
    needsSize: !!config.needsSize,
    needsColor: !!config.needsColor,
    loading: false,
    error: '',

    async submit() {
        this.error = '';
        if (this.needsSize && !this.size) {
            this.error = 'Veuillez choisir une taille.';
            return;
        }
        if (this.needsColor && !this.color) {
            this.error = 'Veuillez choisir une couleur.';
            return;
        }
        this.loading = true;
        try {
            const response = await fetch(config.url || '/panier', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrf(),
                },
                body: JSON.stringify({
                    product_id: this.productId,
                    size: this.size || null,
                    color: this.color || null,
                    quantity: this.quantity,
                }),
            });
            const data = await response.json();
            if (!response.ok) {
                this.error = data.message || 'Une erreur est survenue.';
                return;
            }
            Alpine.store('cart').count = data.count;
            Alpine.store('cart').total = data.total;
            Alpine.store('toast').show(data.message || 'Produit ajouté au panier avec succès');
            window.dispatchEvent(new CustomEvent('cart-updated'));
        } catch (e) {
            this.error = 'Connexion impossible, veuillez réessayer.';
        } finally {
            this.loading = false;
        }
    },
}));

/* ------------------------------------------------------------------ */
/* Quick view modal: loads the product partial over AJAX               */
/* ------------------------------------------------------------------ */
Alpine.data('quickView', () => ({
    open: false,
    html: '',
    loading: false,
    async show(url) {
        this.open = true;
        this.loading = true;
        this.html = '';
        document.body.classList.add('overflow-hidden');
        try {
            const response = await fetch(url, { headers: { Accept: 'text/html' } });
            this.html = await response.text();
        } catch (e) {
            this.html = '<p class="p-6 text-center">Impossible de charger le produit.</p>';
        } finally {
            this.loading = false;
        }
    },
    close() {
        this.open = false;
        document.body.classList.remove('overflow-hidden');
    },
}));

/* ------------------------------------------------------------------ */
/* Product gallery with hover zoom                                     */
/* ------------------------------------------------------------------ */
Alpine.data('gallery', (images = []) => ({
    images,
    active: 0,
    zoom: false,
    origin: '50% 50%',
    move(event) {
        const rect = event.currentTarget.getBoundingClientRect();
        const x = ((event.clientX - rect.left) / rect.width) * 100;
        const y = ((event.clientY - rect.top) / rect.height) * 100;
        this.origin = `${x}% ${y}%`;
    },
}));

/* ------------------------------------------------------------------ */
/* Carousels                                                           */
/* ------------------------------------------------------------------ */
function initCarousels(root = document) {
    root.querySelectorAll('[data-hero]').forEach((el) => {
        if (el.swiper) return;
        new Swiper(el, {
            modules: [Autoplay, EffectFade, Navigation, Pagination],
            effect: 'fade',
            fadeEffect: { crossFade: true },
            loop: true,
            speed: 800,
            autoplay: { delay: 5000, disableOnInteraction: false },
            pagination: { el: el.querySelector('.swiper-pagination'), clickable: true },
            navigation: {
                nextEl: el.querySelector('[data-next]'),
                prevEl: el.querySelector('[data-prev]'),
            },
        });
    });

    root.querySelectorAll('[data-carousel]').forEach((el) => {
        if (el.swiper) return;
        const wrapper = el.closest('[data-carousel-wrapper]') || el;
        new Swiper(el, {
            modules: [Autoplay, Navigation, Pagination],
            loop: el.querySelectorAll('.swiper-slide').length > 4,
            speed: 600,
            spaceBetween: 16,
            autoplay: { delay: 4000, disableOnInteraction: false, pauseOnMouseEnter: true },
            slidesPerView: 1,
            breakpoints: {
                360: { slidesPerView: 2 },
                600: { slidesPerView: 3 },
                991: { slidesPerView: 4 },
            },
            pagination: { el: wrapper.querySelector('.swiper-pagination'), clickable: true },
            navigation: {
                nextEl: wrapper.querySelector('[data-next]'),
                prevEl: wrapper.querySelector('[data-prev]'),
            },
        });
    });
}

document.addEventListener('DOMContentLoaded', () => initCarousels());

Alpine.start();
