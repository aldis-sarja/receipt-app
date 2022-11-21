<?php

namespace App\Repositories;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;

interface ReceiptsRepositoryInterface
{
    public function getAllReceipts(): Collection;
    public function getReceiptById(int $id): Receipt;
    public function getReceiptsByFilter(array $filters): Collection;
    public function createReceipt(array $items): Receipt;
    public function updateReceipt(int $receiptId, array $itemsIds): Receipt;
    public function deleteReceipt(int $id): bool;
}
