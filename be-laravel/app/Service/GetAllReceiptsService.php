<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;

class GetAllReceiptsService extends ReceiptsService
{
    public function execute(): Collection
    {
        return $this->receiptsRepository->getAllReceipts();
    }
}
