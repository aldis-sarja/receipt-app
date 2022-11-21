<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;

class GetFreeItemsService extends ItemsService
{
    public function execute(): Collection
    {
        return $this->itemsRepository->getFreeItems();
    }
}
