<?php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

class ItemsRepository implements ItemsRepositoryInterface
{
    public function getFreeItems(): Collection
    {
        return Item::where('receipt_id', null)->get();
    }
}
