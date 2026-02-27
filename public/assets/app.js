(function () {
    function initTemplatePicker() {
        var picker = document.querySelector('[data-template-picker]');
        if (!picker) return;

        var inputs = picker.querySelectorAll('[data-template-input]');

        function syncSelection() {
            var cards = picker.querySelectorAll('[data-template-card]');
            cards.forEach(function (card) {
                card.classList.remove('selected');
            });

            inputs.forEach(function (input) {
                if (input.checked) {
                    var card = input.closest('[data-template-card]');
                    if (card) card.classList.add('selected');
                }
            });
        }

        inputs.forEach(function (input) {
            input.addEventListener('change', syncSelection);
        });

        syncSelection();
    }

    function initLoadingButtons() {
        var forms = document.querySelectorAll('[data-loading-form]');

        forms.forEach(function (form) {
            form.addEventListener('submit', function () {
                var submit = form.querySelector('button[type="submit"]');
                if (!submit) return;

                submit.disabled = true;
                var loadingLabel = submit.getAttribute('data-submit-label');
                if (loadingLabel && loadingLabel.length > 0) {
                    submit.dataset.originalLabel = submit.textContent;
                    submit.textContent = loadingLabel;
                }
            });
        });
    }

    function initPinInput() {
        var pins = document.querySelectorAll('[data-pin-input]');
        if (!pins.length) return;

        pins.forEach(function(pin) {
            pin.addEventListener('input', function () {
                // Remove non-numeric chars
                var clean = pin.value.replace(/[^0-9]/g, '');

                // Allow up to 12 digits for cash input, 6 for PIN
                if (pin.id === 'cash-received') {
                    pin.value = clean.slice(0, 12);
                    // Format as currency if it's the cash input
                    if (window.POS) window.POS.updateChange();
                } else {
                    pin.value = clean.slice(0, 6);
                }
            });
        });
    }

    function initFlashAutoHide() {
        var flashes = document.querySelectorAll('.flash.success');
        flashes.forEach(function (flash) {
            setTimeout(function () {
                flash.style.opacity = '0';
                flash.style.transition = 'opacity 0.25s ease';
                setTimeout(function () {
                    if (flash.parentNode) flash.parentNode.removeChild(flash);
                }, 250);
            }, 4500);
        });
    }

    // --- POS Module ---
    var POS = {
        products: [],
        cart: [],
        taxRate: 10,

        init: function () {
            var productContainer = document.getElementById('product-list');
            if (!productContainer) return; // Not on POS page

            this.products = window.initialProducts || [];
            this.taxRate = window.taxRate || 10;
            this.renderProducts();
            this.renderCart();
            this.bindEvents();
        },

        bindEvents: function () {
            var search = document.getElementById('product-search');
            if (search) {
                search.addEventListener('input', function (e) {
                    POS.renderProducts(e.target.value);
                });
            }

            var paymentCards = document.querySelectorAll('[data-payment-card]');
            paymentCards.forEach(function (card) {
                card.addEventListener('click', function () {
                    // Update UI selection
                    paymentCards.forEach(c => c.classList.remove('selected'));
                    card.classList.add('selected');
                    // Check radio
                    var radio = card.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                        POS.updateChange(); // Recalculate if cash field needed
                    }
                });
            });

            var cashInput = document.getElementById('cash-received');
            if (cashInput) {
                cashInput.addEventListener('input', function () {
                    POS.updateChange();
                });
            }

            // Listen for form submit to populate hidden inputs
            var form = document.getElementById('checkout-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    var cartInput = document.getElementById('input-cart-items');
                    cartInput.value = JSON.stringify(POS.cart);
                });
            }
        },

        renderProducts: function (filter = '') {
            var container = document.getElementById('product-list');
            container.innerHTML = '';

            var filtered = this.products.filter(function (p) {
                if (!filter) return true;
                var term = filter.toLowerCase();
                return p.name.toLowerCase().includes(term) || p.sku.toLowerCase().includes(term);
            });

            if (filtered.length === 0) {
                container.innerHTML = '<div class="col-12 empty-state" style="grid-column: span 3;">Produk tidak ditemukan.</div>';
                return;
            }

            filtered.forEach(function (p) {
                var el = document.createElement('div');
                el.className = 'product-card';
                el.onclick = function () { POS.addToCart(p.id); };

                var priceFormatted = new Intl.NumberFormat('id-ID').format(p.price_cents / 100); // Very rough formatting

                el.innerHTML = `
                    <div class="p-name">${p.name}</div>
                    <div class="p-meta">
                        <span>${p.sku}</span>
                        <span>Stok: ${p.stock_qty}</span>
                    </div>
                    <div class="p-price">Rp ${priceFormatted}</div>
                `;
                container.appendChild(el);
            });
        },

        addToCart: function (productId) {
            var existing = this.cart.find(i => i.productId === productId);
            var product = this.products.find(p => p.id === productId);

            if (!product) return;
            if (product.stock_qty <= 0) {
                alert('Stok habis!');
                return;
            }

            if (existing) {
                if (existing.qty + 1 > product.stock_qty) {
                    alert('Stok tidak mencukupi!');
                    return;
                }
                existing.qty++;
            } else {
                this.cart.push({
                    productId: productId,
                    name: product.name,
                    price: parseInt(product.price_cents),
                    qty: 1
                });
            }
            this.renderCart();
        },

        removeFromCart: function (index) {
            this.cart.splice(index, 1);
            this.renderCart();
        },

        updateQty: function (index, delta) {
            var item = this.cart[index];
            var product = this.products.find(p => p.id === item.productId);

            var newQty = item.qty + delta;

            if (newQty <= 0) {
                this.removeFromCart(index);
                return;
            }

            if (newQty > product.stock_qty) {
                alert('Stok maksimal tercapai');
                return;
            }

            item.qty = newQty;
            this.renderCart();
        },

        renderCart: function () {
            var container = document.getElementById('cart-items');
            container.innerHTML = '';

            if (this.cart.length === 0) {
                container.innerHTML = '<div class="empty-state">Keranjang kosong</div>';
                this.updateTotals(0);
                return;
            }

            var subtotal = 0;

            this.cart.forEach(function (item, index) {
                var lineTotal = item.price * item.qty;
                subtotal += lineTotal;

                var el = document.createElement('div');
                el.className = 'cart-item';

                // Format helper
                var fmt = n => new Intl.NumberFormat('id-ID').format(n / 100);

                el.innerHTML = `
                    <div style="flex-grow: 1;">
                        <div style="font-weight: 600;">${item.name}</div>
                        <div class="muted">@ ${fmt(item.price)}</div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <button type="button" class="btn-qty" onclick="window.POS.updateQty(${index}, -1)">-</button>
                        <span style="width: 20px; text-align: center;">${item.qty}</span>
                        <button type="button" class="btn-qty" onclick="window.POS.updateQty(${index}, 1)">+</button>
                    </div>
                    <div style="min-width: 60px; text-align: right; font-weight: 600;">
                        ${fmt(lineTotal)}
                    </div>
                `;
                container.appendChild(el);
            });

            this.updateTotals(subtotal);
        },

        updateTotals: function (subtotal) {
            var tax = Math.round(subtotal * (this.taxRate / 100));
            var total = subtotal + tax;

            var fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n / 100);

            document.getElementById('cart-subtotal').textContent = fmt(subtotal);
            document.getElementById('cart-tax').textContent = fmt(tax);
            document.getElementById('cart-total').textContent = fmt(total);

            document.getElementById('btn-checkout').disabled = (this.cart.length === 0);

            // Store total for change calculation
            this.currentTotal = total;
            this.updateChange();
        },

        updateChange: function() {
            if (!this.currentTotal) return;

            var cashInput = document.getElementById('cash-received');
            var changeDisplay = document.getElementById('change-display');
            var btnCheckout = document.getElementById('btn-checkout');

            if (!cashInput || !changeDisplay) return;

            // Handle Indonesian locale manual parsing if needed, but for now simple numeric
            var cashRaw = cashInput.value.replace(/[^0-9]/g, '');
            // The input is in raw numbers, effectively cents if user types "10000" it means 10000.
            // BUT wait, our system stores prices in cents.
            // Display shows "Rp 10.000". User types "20000".
            // So if total is 1500000 (15k), user types 20000.
            // We need to treat user input as MAJOR units, then convert to cents.
            // 20000 => 2000000 cents.

            var cashVal = parseInt(cashRaw || '0');
            var cashCents = cashVal * 100; // Convert to cents

            var diff = cashCents - this.currentTotal;

            var fmt = n => 'Rp ' + new Intl.NumberFormat('id-ID').format(n / 100);

            if (diff >= 0) {
                changeDisplay.value = fmt(diff);
                if (this.cart.length > 0 && btnCheckout) btnCheckout.disabled = false;
            } else {
                changeDisplay.value = 'Kurang ' + fmt(Math.abs(diff));
                // Only disable if payment method is CASH. For QRIS, we assume exact payment.
                var isCashInput = document.querySelector('input[name="payment_method"][value="cash"]');
                var isCash = isCashInput ? isCashInput.checked : true;

                if (btnCheckout) {
                    if (isCash && this.cart.length > 0) {
                         btnCheckout.disabled = true;
                    } else if (!isCash && this.cart.length > 0) {
                         btnCheckout.disabled = false;
                    }
                }
            }
        }
    };

    // Expose to window for inline onclick handlers
    window.POS = POS;

    // Expose tab switcher to global scope (simple fix for inline onclicks)
    window.switchTab = function(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
        var target = document.getElementById('tab-' + tabId);
        if (target) target.style.display = 'block';

        // Simple active state for buttons
        document.querySelectorAll('.header .btn').forEach(btn => {
            if(btn.textContent.toLowerCase().includes(tabId)) {
                btn.classList.add('btn-primary');
            } else {
                btn.classList.remove('btn-primary');
            }
        });
    };

    // Expose closeCard
    window.closeCard = function() {
        var modal = document.getElementById('modal-card');
        if (modal) modal.style.display = 'none';
    };

    document.addEventListener('DOMContentLoaded', function () {
        initTemplatePicker();
        initLoadingButtons();
        initPinInput();
        initFlashAutoHide();

        // Initialize POS if element exists
        POS.init();
    });
})();
