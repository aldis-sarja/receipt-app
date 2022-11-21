<?php

namespace App\Service;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;

class UpdateReceiptService extends ReceiptsService
{
    public function execute(int $receiptId, array $itemsIds): Receipt
    {
        return $this->receiptsRepository->updateReceipt($receiptId, $itemsIds);
    }
}
