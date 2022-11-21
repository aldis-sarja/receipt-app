<?php

namespace App\Service;

use App\Repositories\ReceiptsRepositoryInterface;

abstract class ReceiptsService
{
    protected ReceiptsRepositoryInterface $receiptsRepository;

    public function __construct(ReceiptsRepositoryInterface $receiptsRepository)
    {
        $this->receiptsRepository = $receiptsRepository;
    }
}
