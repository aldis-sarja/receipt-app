<?php

namespace App\Repositories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Collection;

interface ItemsRepositoryInterface
{
    public function getFreeItems(): Collection;
}
