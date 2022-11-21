<?php

namespace App\Service;

use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;

class GetReceiptByIdService extends ReceiptsService
{
    public function execute(int $id): Receipt
    {
        return $this->receiptsRepository->getReceiptById($id);
    }
}
