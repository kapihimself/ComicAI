<?php
declare(strict_types=1);

namespace Siappos\Domain\Product\Actions;

use InvalidArgumentException;
use Siappos\Domain\Product\ProductRepository;
use PDO;
use RuntimeException;

final class AdjustStockAction
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly PDO $pdo,
    ) {
    }

    /**
     * @param int $productId
     * @param float $qtyChange Positive for addition (purchase), Negative for reduction (adjustment/waste)
     * @param string $reason 'purchase', 'adjustment', 'waste', 'return'
     * @param string|null $reference
     * @param string|null $notes
     * @param int|null $actorId
     * @return float New stock quantity
     */
    public function execute(
        int $productId,
        float $qtyChange,
        string $reason,
        ?string $reference = null,
        ?string $notes = null,
        ?int $actorId = null
    ): float {
        if ($qtyChange == 0) {
            throw new InvalidArgumentException('Perubahan stok tidak boleh nol.');
        }

        $validReasons = ['purchase', 'adjustment', 'waste', 'return'];
        if (!in_array($reason, $validReasons)) {
            throw new InvalidArgumentException('Alasan stok tidak valid: ' . $reason);
        }

        $this->pdo->beginTransaction();

        try {
            $product = $this->productRepository->find($productId);
            if (!$product) {
                throw new RuntimeException('Produk tidak ditemukan.');
            }

            $currentStock = (float) $product['stock_qty'];
            $newStock = $currentStock + $qtyChange;

            if ($newStock < 0) {
                throw new RuntimeException("Stok tidak boleh minus (Stok saat ini: $currentStock).");
            }

            // 1. Update Product Table
            $updateStmt = $this->pdo->prepare(
                'UPDATE products SET stock_qty = :new_stock, updated_at = CURRENT_TIMESTAMP WHERE id = :id'
            );
            $updateStmt->execute([
                ':new_stock' => $newStock,
                ':id' => $productId,
            ]);

            // 2. Log Movement
            $logStmt = $this->pdo->prepare(
                'INSERT INTO stock_movements (
                    product_id, qty_change, final_stock, reason, reference_id, notes, created_by, created_at
                ) VALUES (
                    :product_id, :qty_change, :final_stock, :reason, :reference_id, :notes, :created_by, CURRENT_TIMESTAMP
                )'
            );
            $logStmt->execute([
                ':product_id' => $productId,
                ':qty_change' => $qtyChange,
                ':final_stock' => $newStock,
                ':reason' => $reason,
                ':reference_id' => $reference,
                ':notes' => $notes,
                ':created_by' => $actorId,
            ]);

            $this->pdo->commit();

            return $newStock;

        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
