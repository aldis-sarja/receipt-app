<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;

class DeleteReceiptService extends ReceiptsService
{
    public function execute(int $id): bool
    {
        return $this->receiptsRepository->DeleteReceipt($id);
    }
}
