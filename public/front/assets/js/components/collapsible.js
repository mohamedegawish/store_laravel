/**
 * NDIA E-Commerce — Collapsible Sections
 */
import { getAll } from '../utils/helpers.js';

export function initCollapsibles() {
    getAll('[data-toggle="collapse"]').forEach(header => {
        const targetId = header.getAttribute('aria-controls');
        const items = document.getElementById(targetId);

        if (!items) {
            console.warn(`Collapsible target not found for: ${header.textContent.trim()}`);
            return;
        }

        const isInitiallyCollapsed = items.classList.contains('collapsed');
        header.setAttribute('aria-expanded', !isInitiallyCollapsed);

        if (!isInitiallyCollapsed) {
            setTimeout(() => {
                items.style.maxHeight = items.scrollHeight + 'px';
            }, 0);
        }

        header.addEventListener('click', () => {
            const isCollapsed = items.classList.contains('collapsed');

            header.classList.toggle('collapsed', !isCollapsed);
            items.classList.toggle('collapsed', !isCollapsed);
            header.setAttribute('aria-expanded', isCollapsed);

            if (isCollapsed) {
                items.style.maxHeight = items.scrollHeight + 'px';
            } else {
                items.style.maxHeight = null;
            }
        });
    });
}
