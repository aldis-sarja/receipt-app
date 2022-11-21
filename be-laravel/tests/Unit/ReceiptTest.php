<?php

namespace Tests\Unit;

use App\Models\Item;
use App\Repositories\ReceiptsRepository;
use App\Service\CreateReceiptService;
use App\Service\DeleteReceiptService;
use App\Service\GetAllReceiptsService;
use App\Service\GetReceiptByIdService;
use App\Service\GetReceiptsByFilterService;
use App\Service\UpdateReceiptService;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReceiptTest extends TestCase
{
    use \Illuminate\Foundation\Testing\DatabaseMigrations;

    public function test_it_should_be_able_to_make_receipt(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);
        $this->assertEquals('TV', $receipt->items->first()->name);
        $this->assertEquals($item->id, $receipt->items->first()->id);
        $this->assertEquals(1, $receipt->items->count());
    }

    public function test_it_should_be_able_to_add_item_to_receipt(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $itemsIds[] = $item->id;

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        $itemsIds[] = $item->id;

        $receipt = (new UpdateReceiptService(new ReceiptsRepository))->execute($receipt->id, $itemsIds);

        $this->assertEquals('TV', $receipt->items->first()->name);
        $this->assertEquals('Car', $receipt->items->last()->name);
        $this->assertEquals(2, $receipt->items->count());
    }

    public function test_it_should_be_able_to_remove_item_from_receipt(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $itemsIds[] = $item->id;

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        $itemsIds[] = $item->id;

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute($itemsIds);

        $this->assertEquals('TV', $receipt->items->first()->name);
        $this->assertEquals('Car', $receipt->items->last()->name);
        $this->assertEquals(2, $receipt->items->count());

        $itemsIds = [$itemsIds[0]];

        $receipt = (new UpdateReceiptService(new ReceiptsRepository))->execute($receipt->id, $itemsIds);

        $this->assertEquals('TV', $receipt->items->first()->name);
        $this->assertEquals(1, $receipt->items->count());
    }

    public function test_it_should_be_able_to_add_and_remove_items_from_receipt(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $itemsIds[] = $item->id;

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        $itemsIds[] = $item->id;

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute($itemsIds);

        $this->assertEquals('TV', $receipt->items->first()->name);
        $this->assertEquals('Car', $receipt->items->last()->name);
        $this->assertEquals(2, $receipt->items->count());

        $item = Item::create(
            [
                'name' => 'Furniture'
            ]
        );

        $itemsIds = [$itemsIds[1], $item->id];

        $receipt = (new UpdateReceiptService(new ReceiptsRepository))->execute($receipt->id, $itemsIds);

        $this->assertEquals('Car', $receipt->items->first()->name);
        $this->assertEquals('Furniture', $receipt->items->last()->name);
        $this->assertEquals(2, $receipt->items->count());
    }

    public function test_it_should_be_able_to_get_list_of_receipts(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $receipts = (new GetAllReceiptsService(new ReceiptsRepository))->execute();
        $this->assertEquals(2, $receipts->count());
    }

    public function test_it_should_be_able_to_receipt_by_id(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $itemsIds[] = $item->id;

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        $itemsIds[] = $item->id;

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute($itemsIds);

        $receipt = (new GetReceiptByIdService(new ReceiptsRepository))->execute($receipt->id);
        $this->assertEquals('TV', $receipt->items->first()->name);
    }

    public function test_it_should_be_able_to_get_receipts_by_filter(): void
    {
        $item = Item::create(
            [
                'name' => 'Furniture'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $receipts = (new GetReceiptsByFilterService(new ReceiptsRepository))->execute(['items' => 'Furnit']);
        $this->assertEquals(1, $receipts->count());
        $this->assertEquals('Furniture', $receipts->first()->items->first()->name);

        $receipts = (new GetReceiptsByFilterService(new ReceiptsRepository))->execute(['items' => 'Furnit,Car']);
        $this->assertEquals(2, $receipts->count());
        $this->assertEquals('Furniture', $receipts->first()->items->first()->name);
        $this->assertEquals('Car', $receipts->last()->items->first()->name);
    }

    public function test_it_should_be_able_to_get_empty_result_by_filter(): void
    {
        $item = Item::create(
            [
                'name' => 'Furniture'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $item = Item::create(
            [
                'name' => 'Car'
            ]
        );

        (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $receipts = (new GetReceiptsByFilterService(new ReceiptsRepository))->execute(['items' => 'TV']);
        $this->assertEquals(0, $receipts->count());
    }

    public function test_it_should_be_able_to_delete_receipt(): void
    {
        $item = Item::create(
            [
                'name' => 'TV'
            ]
        );

        $receipt = (new CreateReceiptService(new ReceiptsRepository))->execute([$item->id]);

        $res = (new DeleteReceiptService(new ReceiptsRepository))->execute($receipt->id);

        $this->assertEquals(true, $res);

        $receipts = (new GetAllReceiptsService(new ReceiptsRepository))->execute();
        $this->assertEquals(0, $receipts->count());
    }
}
