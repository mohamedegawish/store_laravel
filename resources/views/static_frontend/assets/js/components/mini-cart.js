/**
 * NDIA E-Commerce — Mini Cart Popup Component
 */
import { get } from '../utils/helpers.js';
import { formatPrice } from '../utils/helpers.js';

let isMiniCartOpen = false;
let initialized = false;

const DEFAULT_PRODUCT_IMG = '/NDIA/images/default-product.jpg';

export function initMiniCart() {
    if (initialized) return;
    initialized = true;

    const cartIconButton = get('#cart-icon-button');
    const miniCartPopup = get('#miniCartPopup');
    const miniCartCloseButton = miniCartPopup?.querySelector('.mini-cart-close');
    const miniCartItemsContainer = get('#miniCartItems');

    if (!miniCartPopup) return;

    // Toggle on cart icon click
    cartIconButton?.addEventListener('click', (e) => {
        e.preventDefault();
        toggleMiniCart();
    });

    // Close button
    miniCartCloseButton?.addEventListener('click', () => toggleMiniCart(false));

    // Remove items via delegation
    miniCartItemsContainer?.addEventListener('click', (e) => {
        const removeButton = e.target.closest('.mini-cart-item-remove');
        if (removeButton && !removeButton.disabled) {
            const productId = removeButton.dataset.productId;
            if (productId && window.__ndiaCartApi) {
                window.__ndiaCartApi.removeFromCart(productId, removeButton);
            }
        }
    });

    // Close on outside click
    document.addEventListener('click', (event) => {
        if (isMiniCartOpen && miniCartPopup && cartIconButton) {
            const isClickInsideCart = miniCartPopup.contains(event.target);
            const isClickOnCartButton = cartIconButton.contains(event.target);
            const isClickInsideMobileCartButtons = get('.mobile-cart-buttons')?.contains(event.target);
            if (!isClickInsideCart && !isClickOnCartButton && !isClickInsideMobileCartButtons) {
                toggleMiniCart(false);
            }
        }
    });

    // Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && isMiniCartOpen) {
            toggleMiniCart(false);
        }
    });
}

/**
 * Toggle mini cart visibility.
 * @param {boolean} [show]
 */
export function toggleMiniCart(show) {
    const miniCartPopup = get('#miniCartPopup');
    const cartIconButton = get('#cart-icon-button');
    if (!miniCartPopup || !cartIconButton) return;

    const shouldShow = typeof show === 'boolean' ? show : !isMiniCartOpen;

    if (shouldShow) {
        miniCartPopup.classList.add('show');
        cartIconButton.setAttribute('aria-expanded', 'true');
        isMiniCartOpen = true;
    } else {
        miniCartPopup.classList.remove('show');
        cartIconButton.setAttribute('aria-expanded', 'false');
        isMiniCartOpen = false;
    }
}

/**
 * Update cart count badge.
 * @param {number} count
 */
export function updateCartCountDisplay(count) {
    const totalCount = Number(count) || 0;
    const cartCountElement = get('#cart-item-count');
    const mobileCartButton = get('#mobileCartButton');
    const mobileCartCount = get('#mobileCartCount');
    const mobileCartCount2 = get('#mobileCartCount2');
    const cartIconButton = get('#cart-icon-button');

    if (cartCountElement) {
        cartCountElement.textContent = totalCount;
        cartCountElement.style.display = totalCount > 0 ? 'inline-block' : 'none';
    }

    if (mobileCartCount && mobileCartButton) {
        mobileCartCount.textContent = totalCount;
        mobileCartButton.classList.toggle('show', totalCount > 0);
        mobileCartButton.setAttribute('aria-label',
            `عرض السلة (${totalCount} ${totalCount === 1 ? 'منتج' : 'منتجات'})`);
    }

    if (mobileCartCount2) {
        mobileCartCount2.textContent = totalCount;
        mobileCartCount2.style.display = totalCount > 0 ? 'inline-block' : 'none';
    }

    if (cartIconButton) {
        cartIconButton.setAttribute('aria-label',
            `سلة التسوق (${totalCount} ${totalCount === 1 ? 'منتج' : 'منتجات'})`);
    }
}

/**
 * Render mini cart items.
 * @param {Object} cartData
 */
export function updateMiniCart(cartData) {
    const miniCartItemsContainer = get('#miniCartItems');
    const miniCartSubtotalElement = get('#miniCartSubtotal');
    const miniCartCheckoutBtn = get('#miniCartCheckoutBtn');

    if (!miniCartItemsContainer || !miniCartSubtotalElement || !miniCartCheckoutBtn) return;

    const { cart_items = [], subtotal = '0.00' } = cartData;

    miniCartItemsContainer.innerHTML = '';

    if (cart_items.length === 0) {
        miniCartItemsContainer.innerHTML =
            '<p class="mini-cart-empty"><i class="fas fa-shopping-basket"></i>سلتك فارغة حالياً.</p>';
        miniCartCheckoutBtn.setAttribute('disabled', 'true');
        miniCartCheckoutBtn.classList.add('button-outline');
        miniCartCheckoutBtn.style.opacity = '0.6';
    } else {
        cart_items.forEach(item => {
            const itemElement = document.createElement('div');
            itemElement.classList.add('mini-cart-item');
            itemElement.innerHTML = `
                <a href="product_details.php?id=${item.id}" tabindex="-1">
                   <img src="" alt="" loading="lazy" width="65" height="65">
                </a>
                <div class="mini-cart-item-details">
                    <h5><a href="product_details.php?id=${item.id}"></a></h5>
                    <span class="price">${formatPrice(item.price)}</span>
                    <span class="quantity">الكمية: ${item.quantity || 1}</span>
                </div>
                <button type="button" class="mini-cart-item-remove" data-product-id="${item.id}"
                        title="إزالة ${item.name}" aria-label="إزالة ${item.name}">
                    <i class="fas fa-trash-alt" aria-hidden="true"></i>
                </button>
            `;
            const img = itemElement.querySelector('img');
            const titleLink = itemElement.querySelector('h5 a');
            img.src = item.image || DEFAULT_PRODUCT_IMG;
            img.alt = item.name || 'Product Image';
            titleLink.textContent = item.name || 'Unknown Product';
            titleLink.href = `product_details.php?id=${item.id}`;

            miniCartItemsContainer.appendChild(itemElement);
        });
        miniCartCheckoutBtn.removeAttribute('disabled');
        miniCartCheckoutBtn.classList.remove('button-outline');
        miniCartCheckoutBtn.style.opacity = '1';
    }

    miniCartSubtotalElement.textContent = formatPrice(subtotal);
}
