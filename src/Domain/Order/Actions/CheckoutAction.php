<?php
declare(strict_types=1);

namespace Siappos\Domain\Order\Actions;

use PDO;
use RuntimeException;
use Siappos\Domain\Order\DTO\CheckoutData;
use Siappos\Domain\Product\ProductRepository;

final class CheckoutAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PDO $pdo,
    ) {
    }

    public function execute(CheckoutData $data): int
    {
        $this->pdo->beginTransaction();

        try {
            // 1. Validate Items & Calculate Totals
            $subtotalCents = 0;
            $lineItems = [];

            // Get all product IDs to fetch in one go (optimization)
            $productIds = array_map(fn($item) => $item->productId, $data->items);
            $products = $this->productRepository->findManyByIds($productIds);

            foreach ($data->items as $item) {
                if (!isset($products[$item->productId])) {
                    throw new RuntimeException("Produk ID {$item->productId} tidak ditemukan atau sudah dihapus.");
                }

                $product = $products[$item->productId];

                // Stock check
                if ($product['stock_qty'] < $item->qty) {
                    throw new RuntimeException("Stok produk '{$product['name']}' tidak mencukupi (Sisa: {$product['stock_qty']}).");
                }

                $unitPriceCents = (int) $product['price_cents'];
                $lineTotalCents = (int) ($unitPriceCents * $item->qty);
                $subtotalCents += $lineTotalCents;

                $lineItems[] = [
                    'product_id' => $item->productId,
                    'product_name' => $product['name'],
                    'qty' => $item->qty,
                    'unit_price_cents' => $unitPriceCents,
                    'line_total_cents' => $lineTotalCents,
                ];
            }

            // 2. Calculate Final Totals
            $discountCents = 0;
            if ($data->discountType === 'fixed') {
                $discountCents = (int) ($data->discountValue * 100); // Assuming input is in major currency unit
            } elseif ($data->discountType === 'percent') {
                $discountCents = (int) round(($subtotalCents * $data->discountValue) / 100);
            }

            // Ensure discount doesn't exceed subtotal
            $discountCents = min($discountCents, $subtotalCents);

            $afterDiscountCents = $subtotalCents - $discountCents;

            $taxCents = (int) round($afterDiscountCents * ($data->taxRate / 100));
            $totalCents = $afterDiscountCents + $taxCents;

            // 3. Payment Validation
            if ($data->cashReceivedCents < $totalCents) {
                throw new RuntimeException('Uang yang diterima kurang dari total belanja.');
            }

            $changeCents = $data->cashReceivedCents - $totalCents;

            // 4. Create Order
            $orderNumber = 'ORD-' . date('YmdHis') . '-' . mt_rand(100, 999);

            $stmt = $this->pdo->prepare(
                'INSERT INTO orders (
                    order_number, status, subtotal_cents, discount_type, discount_value,
                    discount_cents, tax_rate, tax_cents, total_cents, payment_method,
                    cash_received_cents, change_cents, created_by, created_at
                ) VALUES (
                    :order_number, :status, :subtotal_cents, :discount_type, :discount_value,
                    :discount_cents, :tax_rate, :tax_cents, :total_cents, :payment_method,
                    :cash_received_cents, :change_cents, :created_by, CURRENT_TIMESTAMP
                )'
            );

            $stmt->execute([
                ':order_number' => $orderNumber,
                ':status' => 'checked_out',
                ':subtotal_cents' => $subtotalCents,
                ':discount_type' => $data->discountType,
                ':discount_value' => $data->discountValue,
                ':discount_cents' => $discountCents,
                ':tax_rate' => $data->taxRate,
                ':tax_cents' => $taxCents,
                ':total_cents' => $totalCents,
                ':payment_method' => $data->paymentMethod,
                ':cash_received_cents' => $data->cashReceivedCents,
                ':change_cents' => $changeCents,
                ':created_by' => $data->actorUserId,
            ]);

            $orderId = (int) $this->pdo->lastInsertId();

            // 5. Create Order Lines & Update Stock
            $lineStmt = $this->pdo->prepare(
                'INSERT INTO order_lines (
                    order_id, product_id, product_name, qty, unit_price_cents, line_total_cents, created_at
                ) VALUES (
                    :order_id, :product_id, :product_name, :qty, :unit_price_cents, :line_total_cents, CURRENT_TIMESTAMP
                )'
            );

            foreach ($lineItems as $line) {
                $lineStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $line['product_id'],
                    ':product_name' => $line['product_name'],
                    ':qty' => $line['qty'],
                    ':unit_price_cents' => $line['unit_price_cents'],
                    ':line_total_cents' => $line['line_total_cents'],
                ]);

                // Decrement stock
                $this->productRepository->decrementStock($line['product_id'], (float) $line['qty']);
            }

            $this->pdo->commit();

            return $orderId;

        } catch (Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
