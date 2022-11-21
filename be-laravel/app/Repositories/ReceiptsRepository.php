<?php

namespace App\Repositories;

use App\Models\Item;
use App\Models\Receipt;
use Illuminate\Database\Eloquent\Collection;

class ReceiptsRepository implements ReceiptsRepositoryInterface
{
    public function getAllReceipts(): Collection
    {
        return Receipt::all();
    }

    public function getReceiptById($id): Receipt
    {
        return Receipt::findOrFail($id);
    }

    public function getReceiptsByFilter(array $filters): Collection
    {
        $receiptsIds = false;
        if (isset($filters['items'])) {
            foreach ($filters['items'] as $name) {
                foreach (Item::where('name', 'like', "%$name%")->get() as $item) {
                    $receiptsIds[] = $item->receipt_id;
                }
            }
        }

        $dateRange = false;
        if (isset($filters['date'])) {
            $dateRange = $filters['date'];
            $dateRange[1] = date(
                "y-m-d",
                strtotime($dateRange[1]) + 60 * 60 * 24
            ); // Add 1 day to make 'whereBetween' work
        }

        if ($receiptsIds && $dateRange) {
            return Receipt
                ::whereIn('id', $receiptsIds)
                ->whereBetween('created_at', $dateRange)
                ->get();
        }

        if ($receiptsIds) {
            return Receipt::whereIn('id', $receiptsIds)->get();
        }

        if ($dateRange) {
//            $receipt = Receipt::all()->first();
//            return response()->json(['RANGE:' => $dateRange, 'CREATED_AT:' => $receipt->created_at]);
            return Receipt::whereBetween('created_at', $dateRange)->get();
        }

        return new Collection; // Return empty collection if everything fails
    }

    public function createReceipt(array $itemsIds): Receipt
    {
        $receipt = Receipt::create();
        foreach ($itemsIds as $itemId) {
            $item = Item::find($itemId);
            if ($item && !$item->receipt_id) {
                $item->receipt_id = $receipt->id;
                $item->update();
            }
        }

        return Receipt::find($receipt->id); // fetch with all items
    }

    public function updateReceipt(int $receiptId, array $itemsIds): Receipt
    {
        $receipt = Receipt::findOrFail($receiptId);

        // Check if there is new items in request
        foreach ($itemsIds as $itemId) {
            $item = Item::findOrFail($itemId);
            if ($item->receipt_id) {
                continue;
            }
            $this->addItem($receipt, $item);
        }

        // Check if some items are excluded
        foreach ($receipt->items as $item) {
            if (in_array($item->id, $itemsIds)) {
                continue;
            }
            $this->removeItem($item);
        }

        return Receipt::find($receipt->id); // fetch with all items
    }

    public function deleteReceipt(int $id): bool
    {
        $receipt = Receipt::findOrFail($id);

        foreach (Item::where('receipt_id', $id)->get() as $item) {
            $item->receipt_id = null;
            $item->update();
        }

        return $receipt->delete() > 0;
    }

    private function addItem(Receipt $receipt, Item $item): void
    {
        $item->receipt_id = $receipt->id;
        $item->update();
    }

    private function removeItem(Item $item): void
    {
        $item->receipt_id = null;
        $item->update();
    }
}
