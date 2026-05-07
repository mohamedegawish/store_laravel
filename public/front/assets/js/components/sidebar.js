/**
 * NDIA E-Commerce — Mobile Sidebar Component
 */
import { get } from '../utils/helpers.js';

let initialized = false;

export function initSidebar() {
    if (initialized) return;
    initialized = true;

    const hamburgerBtn = get('#hamburger-btn');
    const sidebarMobile = get('#sidebar-mobile');
    const closeSidebarBtn = get('#close-sidebar-btn');
    const sidebarOverlay = get('#sidebar-overlay');
    const body = document.body;

    if (!sidebarMobile) return;

    function toggle(show) {
        if (!sidebarMobile || !sidebarOverlay || !hamburgerBtn) return;

        const isOpen = sidebarMobile.classList.contains('open');
        const newState = typeof show === 'boolean' ? show : !isOpen;

        if (newState) {
            sidebarMobile.classList.add('open');
            sidebarOverlay.classList.add('show');
            hamburgerBtn.setAttribute('aria-expanded', 'true');
            body.style.overflow = 'hidden';
        } else {
            sidebarMobile.classList.remove('open');
            sidebarOverlay.classList.remove('show');
            hamburgerBtn.setAttribute('aria-expanded', 'false');
            body.style.overflow = '';
        }
    }

    // Event listeners
    hamburgerBtn?.addEventListener('click', () => toggle());
    closeSidebarBtn?.addEventListener('click', () => toggle(false));

    // Close on overlay click
    sidebarOverlay?.addEventListener('click', (e) => {
        if (sidebarMobile.classList.contains('open') && e.target === sidebarOverlay) {
            toggle(false);
        }
    });

    // Close on link click inside sidebar
    sidebarMobile.addEventListener('click', (e) => {
        if (e.target.tagName === 'A' && !e.target.closest('[data-toggle="collapse"]')) {
            toggle(false);
        }
    });

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebarMobile.classList.contains('open')) {
            toggle(false);
        }
    });

    // Export toggle for external use
    window.__ndiaToggleSidebar = toggle;
}

export function toggleMobileSidebar(show) {
    if (window.__ndiaToggleSidebar) {
        window.__ndiaToggleSidebar(show);
    }
}
