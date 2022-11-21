<?php

namespace App\Service;

use App\Repositories\ItemsRepositoryInterface;

abstract class ItemsService
{
    protected ItemsRepositoryInterface $itemsRepository;

    public function __construct(ItemsRepositoryInterface $itemsRepository)
    {
        $this->itemsRepository = $itemsRepository;
    }
}
