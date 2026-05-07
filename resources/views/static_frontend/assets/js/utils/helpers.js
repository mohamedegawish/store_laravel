/**
 * NDIA E-Commerce — Utility Helpers
 * DOM helpers, price formatting, etc.
 */

/** @param {string} selector - @returns {Element|null} */
export const get = (selector) => document.querySelector(selector);

/** @param {string} selector - @returns {NodeListOf<Element>} */
export const getAll = (selector) => document.querySelectorAll(selector);

/**
 * Format a number as Egyptian Pound currency.
 * @param {number|string} price
 * @returns {string}
 */
export const formatPrice = (price) => {
    const numericPrice = Number(price) || 0;
    return numericPrice.toLocaleString('ar-EG', {
        style: 'currency',
        currency: 'EGP',
        minimumFractionDigits: 2,
    });
};

/**
 * Check if mobile viewport.
 * @returns {boolean}
 */
export const isMobileView = () => window.innerWidth <= 767;

/**
 * Simple debounce utility.
 * @param {Function} fn
 * @param {number} delay
 * @returns {Function}
 */
export const debounce = (fn, delay = 300) => {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => fn(...args), delay);
    };
};
