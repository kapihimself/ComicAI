<?php
/** @var array<int, array<string, mixed>> $products */
/** @var array<string, mixed> $settings */

$title = 'POS Terminal';
?>
<div class="grid">
    <div class="col-8">
        <div class="panel">
            <input type="text" id="product-search" placeholder="Cari produk (nama atau SKU)..." autocomplete="off">
        </div>

        <div id="product-list" class="grid" style="grid-template-columns: repeat(3, 1fr);">
            <!-- Products will be rendered here by JS -->
        </div>
    </div>

    <div class="col-4">
        <div class="panel cart-panel">
            <h3>Keranjang</h3>
            <div id="cart-items" class="cart-items">
                <div class="empty-state">Keranjang kosong</div>
            </div>

            <hr style="border: 0; border-top: 2px solid var(--line); margin: 15px 0;">

            <div class="cart-summary">
                <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <span>Subtotal</span>
                    <span id="cart-subtotal">Rp 0</span>
                </div>
                <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span>Pajak (<?= $settings['pb1_rate'] ?? 10 ?>%)</span>
                    <span id="cart-tax">Rp 0</span>
                </div>
                <div class="summary-row total" style="display: flex; justify-content: space-between; font-size: 1.2em;">
                    <strong>Total</strong>
                    <strong id="cart-total">Rp 0</strong>
                </div>
            </div>

            <form id="checkout-form" method="POST" action="/?page=checkout" style="margin-top: 20px;">
                <input type="hidden" name="_csrf" value="<?= \Siappos\Shared\Csrf::token() ?>">
                <input type="hidden" name="cart_items" id="input-cart-items">

                <div class="form-group" style="margin-bottom: 15px;">
                    <label>Metode Pembayaran</label>
                    <div class="template-grid">
                        <label class="template-card selected" data-payment-card>
                            <input type="radio" name="payment_method" value="cash" checked> Tunai
                        </label>
                        <label class="template-card" data-payment-card>
                            <input type="radio" name="payment_method" value="qris"> QRIS
                        </label>
                    </div>
                </div>

                <div class="form-group" id="cash-input-group" style="margin-bottom: 15px;">
                    <label>Uang Diterima</label>
                    <input type="text" name="cash_received" id="cash-received" placeholder="0" data-pin-input>
                </div>

                <div class="form-group" style="margin-bottom: 15px;">
                     <label>Kembalian</label>
                     <input type="text" id="change-display" readonly value="Rp 0" style="background: #f6f7fa;">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;" id="btn-checkout" disabled>
                    Bayar Sekarang
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    window.initialProducts = <?= json_encode($products) ?>;
    window.taxRate = <?= $settings['pb1_rate'] ?? 10 ?>;
</script>
