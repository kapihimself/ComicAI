<?php
declare(strict_types=1);

namespace Siappos\Domain\Product;

use PDO;
use RuntimeException;

final class StockMovementRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    /**
     * @param int $productId
     * @param int $limit
     * @param int $offset
     * @return array<int, array<string, mixed>>
     */
    public function findByProductId(int $productId, int $limit = 20, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT sm.*, u.full_name as actor_name
             FROM stock_movements sm
             LEFT JOIN users u ON sm.created_by = u.id
             WHERE sm.product_id = :product_id
             ORDER BY sm.created_at DESC
             LIMIT :limit OFFSET :offset'
        );

        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
