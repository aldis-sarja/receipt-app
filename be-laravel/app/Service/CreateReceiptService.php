<?php

namespace App\Service;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;

class CreateReceiptService extends ReceiptsService
{
    public function execute(array $itemsIds): Receipt
    {
        return $this->receiptsRepository->createReceipt($itemsIds);
    }
}
