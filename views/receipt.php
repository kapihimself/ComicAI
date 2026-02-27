<?php
/** @var array<string, mixed> $order */
/** @var array<int, array<string, mixed>> $orderLines */
/** @var array<string, mixed> $settings */

use Siappos\Shared\Money;

$title = 'Resi Pembayaran #' . $order['order_number'];
?>
<div class="panel" style="max-width: 480px; margin: 0 auto; padding: 30px;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="margin: 0;"><?= htmlspecialchars((string) ($settings['business_name'] ?? 'SiapPOS')) ?></h2>
        <p class="muted" style="margin: 5px 0;">
            <?= htmlspecialchars((string) ($settings['outlet_name'] ?? 'Outlet Utama')) ?><br>
            <?= htmlspecialchars((string) ($settings['address'] ?? '')) ?>
        </p>
    </div>

    <div style="border-bottom: 2px dashed var(--line); margin-bottom: 15px;"></div>

    <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 15px;">
        <div>
            <strong>No:</strong> #<?= htmlspecialchars((string) $order['order_number']) ?><br>
            <strong>Tgl:</strong> <?= htmlspecialchars(date('d/m/Y H:i', strtotime((string) $order['created_at']))) ?>
        </div>
        <div style="text-align: right;">
            <strong>Kasir:</strong> <?= htmlspecialchars((string) ($order['cashier_name'] ?? 'Staff')) ?>
        </div>
    </div>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 14px;">
        <thead>
            <tr style="border-bottom: 1px solid var(--line);">
                <th style="text-align: left; padding: 5px 0;">Item</th>
                <th style="text-align: center; padding: 5px 0;">Qty</th>
                <th style="text-align: right; padding: 5px 0;">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($orderLines as $line): ?>
                <tr>
                    <td style="padding: 5px 0;"><?= htmlspecialchars((string) $line['product_name']) ?></td>
                    <td style="text-align: center; padding: 5px 0;"><?= (float) $line['qty'] ?></td>
                    <td style="text-align: right; padding: 5px 0;">
                        <?= htmlspecialchars(Money::formatCents((int) $line['line_total_cents'])) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="border-top: 2px dashed var(--line); margin-bottom: 15px;"></div>

    <div style="display: grid; grid-template-columns: 1fr auto; gap: 5px; font-size: 14px; margin-bottom: 20px;">
        <div>Subtotal</div>
        <div style="text-align: right;"><?= htmlspecialchars(Money::formatCents((int) $order['subtotal_cents'])) ?></div>

        <?php if ((int) $order['discount_cents'] > 0): ?>
            <div>Diskon</div>
            <div style="text-align: right;">-<?= htmlspecialchars(Money::formatCents((int) $order['discount_cents'])) ?></div>
        <?php endif; ?>

        <div>Pajak (<?= (float) $order['tax_rate'] ?>%)</div>
        <div style="text-align: right;"><?= htmlspecialchars(Money::formatCents((int) $order['tax_cents'])) ?></div>

        <div style="font-weight: 800; font-size: 16px; margin-top: 5px;">Total</div>
        <div style="text-align: right; font-weight: 800; font-size: 16px; margin-top: 5px;">
            <?= htmlspecialchars(Money::formatCents((int) $order['total_cents'])) ?>
        </div>
    </div>

    <div style="background: #f6f7fa; padding: 10px; border: 1px solid var(--line); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between;">
            <span>Bayar (<?= strtoupper((string) $order['payment_method']) ?>)</span>
            <strong><?= htmlspecialchars(Money::formatCents((int) $order['cash_received_cents'])) ?></strong>
        </div>
        <div style="display: flex; justify-content: space-between; margin-top: 5px;">
            <span>Kembali</span>
            <strong><?= htmlspecialchars(Money::formatCents((int) $order['change_cents'])) ?></strong>
        </div>
    </div>

    <div class="no-print" style="text-align: center; display: flex; gap: 10px; justify-content: center;">
        <button onclick="window.print()" class="btn btn-primary">Cetak Resi</button>
        <a href="/?page=pos" class="btn">Transaksi Baru</a>
    </div>
</div>
