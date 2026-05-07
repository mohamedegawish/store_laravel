/**
 * NDIA E-Commerce — Main Application Entry Point
 * Initializes all modules on DOMContentLoaded.
 */
import { initSidebar } from './components/sidebar.js';
import { initMiniCart } from './components/mini-cart.js';
import { initNotifications } from './components/notifications.js';
import { initCollapsibles } from './components/collapsible.js';
import { initCartApi } from './services/cart-api.js';
import { get, getAll } from './utils/helpers.js';

document.addEventListener('DOMContentLoaded', () => {
    // ── Initialize shared components ──
    initSidebar();
    initNotifications();

    // ── Initialize cart-related (only if cart elements exist) ──
    if (get('#cart-icon-button') || get('#miniCartPopup')) {
        initMiniCart();
        initCartApi();
    }

    // ── Initialize collapsible sections (sidebar filters) ──
    if (getAll('[data-toggle="collapse"]').length > 0) {
        initCollapsibles();
    }

    // ── Smooth scrolling for anchor links ──
    getAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href && href.length > 1 && href.startsWith('#')) {
                try {
                    const targetElement = document.querySelector(href);
                    if (targetElement) {
                        e.preventDefault();
                        const headerOffset = get('.top-bar')?.offsetHeight || 60;
                        const elementPosition = targetElement.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - headerOffset - 20;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth',
                        });
                    }
                } catch (error) {
                    console.warn('Smooth scroll target not found:', href);
                }
            }
        });
    });

    console.log('🚀 NDIA App initialized');
});
