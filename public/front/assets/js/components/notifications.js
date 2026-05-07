/**
 * NDIA E-Commerce — Toast Notifications
 */
import { get } from '../utils/helpers.js';

let notificationTimeout;

/**
 * Show a toast notification.
 * @param {string} message
 * @param {'success'|'error'|'info'} [type='success']
 * @param {number} [duration=3500]
 */
export function showNotification(message, type = 'success', duration = 3500) {
    const popup = get('#notificationPopup');
    const popupMessage = get('#popup-message');
    const popupIcon = get('#popup-icon');

    if (!popup || !popupMessage || !popupIcon) return;

    popupMessage.textContent = message;
    popup.classList.remove('success', 'error', 'info');

    let iconClass = 'fas fa-info-circle';
    if (type === 'success') {
        popup.classList.add('success');
        iconClass = 'fas fa-check-circle';
    } else if (type === 'error') {
        popup.classList.add('error');
        iconClass = 'fas fa-exclamation-triangle';
    } else {
        popup.classList.add('info');
    }

    popupIcon.innerHTML = `<i class="${iconClass}"></i>`;
    popup.classList.add('show');
    popup.setAttribute('aria-live', 'assertive');

    clearTimeout(notificationTimeout);
    notificationTimeout = setTimeout(() => hideNotification(), duration);
}

/**
 * Hide the current toast notification.
 */
export function hideNotification() {
    const popup = get('#notificationPopup');
    if (!popup) return;
    popup.classList.remove('show');
    popup.removeAttribute('aria-live');
}

/**
 * Initialize notification close button.
 */
export function initNotifications() {
    const popup = get('#notificationPopup');
    const closeButton = popup?.querySelector('.popup-close');
    closeButton?.addEventListener('click', hideNotification);
}
