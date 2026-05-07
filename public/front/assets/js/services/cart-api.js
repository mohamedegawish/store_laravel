/**
 * NDIA E-Commerce — Cart API Service
 */
import { showNotification } from '../components/notifications.js';
import { updateCartCountDisplay, updateMiniCart, toggleMiniCart } from '../components/mini-cart.js';
import { get } from '../utils/helpers.js';

/**
 * Make a cart API request.
 * @param {string} action - 'get', 'add', 'remove', 'update'
 * @param {Object} data
 * @returns {Promise<Object>}
 */
async function cartApiRequest(action, data = {}) {
    const url = `index.php?ajax=cart&action=${action}`;
    const options = {
        method: action === 'get' ? 'GET' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: action !== 'get' ? JSON.stringify(data) : null,
    };

    let buttonToDisable = null;

    if (action === 'add' && data.buttonElement) {
        buttonToDisable = data.buttonElement;
        buttonToDisable.disabled = true;
        buttonToDisable.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جار الإضافة...';
        delete data.buttonElement;
        options.body = JSON.stringify(data);
    } else if (action === 'remove' && data.buttonElement) {
        buttonToDisable = data.buttonElement;
        buttonToDisable.disabled = true;
        buttonToDisable.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        delete data.buttonElement;
        options.body = JSON.stringify(data);
    }

    try {
        const response = await fetch(url, options);
        const responseData = await response.json();

        if (!response.ok) {
            throw new Error(responseData.message || `Request failed with status ${response.status}`);
        }

        if (buttonToDisable) {
            buttonToDisable.disabled = false;
            if (action === 'add') buttonToDisable.innerHTML = '<i class="fas fa-cart-plus"></i> إضافة للسلة';
            if (action === 'remove') buttonToDisable.innerHTML = '<i class="fas fa-trash-alt" aria-hidden="true"></i>';
        }

        return responseData;
    } catch (error) {
        console.error(`Cart API Error (${action}):`, error);

        if (buttonToDisable) {
            buttonToDisable.disabled = false;
            if (action === 'add') buttonToDisable.innerHTML = '<i class="fas fa-cart-plus"></i> إضافة للسلة';
            if (action === 'remove') buttonToDisable.innerHTML = '<i class="fas fa-trash-alt" aria-hidden="true"></i>';
        }

        showNotification(
            error.message || `فشل ${action === 'get' ? 'جلب' : 'تحديث'} السلة. حاول مرة أخرى.`,
            'error', 5000
        );

        return { success: false, message: error.message || 'API Request Failed' };
    }
}

/**
 * Load initial cart on page load.
 */
export async function loadInitialCart() {
    const cartData = await cartApiRequest('get');
    if (cartData && cartData.success) {
        updateCartCountDisplay(cartData.total_count);
        updateMiniCart(cartData);
    } else {
        console.error('Failed to load initial cart data.');
        showNotification('لم نتمكن من تحميل سلة التسوق حالياً.', 'error');
    }
}

/**
 * Handle add-to-cart button click.
 * @param {Event} event
 */
export async function handleAddToCart(event) {
    const button = event.target.closest('.add-to-cart-button');
    if (!button || button.disabled) return;

    event.preventDefault();
    const productId = button.dataset.productId;
    if (!productId) return;

    const result = await cartApiRequest('add', {
        product_id: productId,
        quantity: 1,
        buttonElement: button,
    });

    if (result.success) {
        updateCartCountDisplay(result.total_count);
        updateMiniCart(result);
        showNotification(result.message || 'تمت إضافة المنتج بنجاح!', 'success');
        toggleMiniCart(true);
    }
}

/**
 * Handle remove-from-cart in mini cart.
 * @param {string} productId
 * @param {HTMLElement} removeButton
 */
export async function removeFromCart(productId, removeButton) {
    const result = await cartApiRequest('remove', {
        product_id: productId,
        buttonElement: removeButton,
    });

    if (result.success) {
        updateCartCountDisplay(result.total_count);
        updateMiniCart(result);
    }
}

/**
 * Initialize cart API on the page.
 */
export function initCartApi() {
    const productGrid = get('#product-grid');
    if (productGrid) {
        productGrid.addEventListener('click', handleAddToCart);
    }

    // Expose for mini-cart remove buttons
    window.__ndiaCartApi = { removeFromCart };

    // Load cart data
    loadInitialCart();
}
