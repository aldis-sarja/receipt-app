<?php

namespace App\Service;

use Illuminate\Database\Eloquent\Collection;

class GetReceiptsByFilterService extends ReceiptsService
{
    public function execute(array $request): Collection
    {
        $filters = [];

        if (isset($request['items'])) {
            $filters['items'] = explode(',', $request['items']);
        }

        if (isset($request['date'])) {
            $filters['date'] = explode(',', $request['date']);
        }

        return $this->receiptsRepository->getReceiptsByFilter($filters);
    }
}
