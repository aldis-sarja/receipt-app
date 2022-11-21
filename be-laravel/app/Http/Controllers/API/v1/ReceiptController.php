<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReceiptRequest;
use App\Service\CreateReceiptService;
use App\Service\DeleteReceiptService;
use App\Service\GetAllReceiptsService;
use App\Service\GetFreeItemsService;
use App\Service\GetReceiptByIdService;
use App\Service\GetReceiptsByFilterService;
use App\Service\UpdateReceiptService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class ReceiptController extends Controller
{
    private const CACHE_TIME = 180;
    private GetAllReceiptsService $getAllReceiptsService;
    private GetReceiptsByFilterService $getReceiptsByFilterService;
    private GetReceiptByIdService $getReceiptByIdService;
    private CreateReceiptService $createReceiptService;
    private UpdateReceiptService $updateReceiptService;
    private DeleteReceiptService $deleteReceiptService;
    private GetFreeItemsService $getFreeItemsService;

    public function __construct(
        GetAllReceiptsService      $getAllReceiptsService,
        GetReceiptsByFilterService $getReceiptsByFilterService,
        GetReceiptByIdService      $getReceiptByIdService,
        CreateReceiptService       $createReceiptService,
        UpdateReceiptService       $updateReceiptService,
        DeleteReceiptService       $deleteReceiptService,
        GetFreeItemsService        $getFreeItemsService
    )
    {
        $this->getAllReceiptsService = $getAllReceiptsService;
        $this->getReceiptsByFilterService = $getReceiptsByFilterService;
        $this->getReceiptByIdService = $getReceiptByIdService;
        $this->createReceiptService = $createReceiptService;
        $this->updateReceiptService = $updateReceiptService;
        $this->deleteReceiptService = $deleteReceiptService;
        $this->getFreeItemsService = $getFreeItemsService;
    }

    public function index(): JsonResponse
    {
        try {
            return cache()->remember("ReceiptsList", self::CACHE_TIME, function () {
                $response = $this->getAllReceiptsService->execute();
                return response()->json($response);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function indexFiltered(ReceiptRequest $request): JsonResponse
    {
        $filters = [];
        $filter = $request->get('items');
        if ($filter) {
            $filters['items'] = $filter;
        }

        $filter = $request->get('date');
        if ($filter) {
            $filters['date'] = $filter;
        }

        if ($filters) {
            $cacheKey = $request->get('items') . $request->get('date');

            try {
                return cache()->remember(
                    $cacheKey,
                    self::CACHE_TIME,
                    function () use ($filters) {
                        $response = $this->getReceiptsByFilterService->execute($filters);
                        return response()->json($response);
                    });
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        }

        return response()->json(['error' => 'Unknown filter'], 404);
    }

    public function getItems(): JsonResponse
    {
        try {
            return cache()->remember("ItemsList", self::CACHE_TIME, function () {
                $response = $this->getFreeItemsService->execute();
                return response()->json($response);
            });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function store(ReceiptRequest $request): JsonResponse
    {
        $itemsIds = json_decode($request->get('items'));

        $receipt = $this->createReceiptService->execute($itemsIds);

        Cache::flush();

        try {
            return cache()->remember(
                "receipt:{$receipt->id}",
                self::CACHE_TIME,
                function () use ($receipt) {
                    return response()->json($receipt);
                });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

    }

    public function show(int $id): JsonResponse
    {
        try {
            $receipt = $this->getReceiptByIdService->execute($id);
            return cache()->remember(
                "receipt:{$id}",
                self::CACHE_TIME,
                function () use ($receipt) {
                    return response()->json($receipt);
                });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function update(int $id, ReceiptRequest $request): JsonResponse
    {
        Cache::flush();

        $itemsIds = json_decode($request->get('items'));

        try {
            return cache()->remember(
                "receipt:{$id}",
                self::CACHE_TIME,
                function () use ($id, $itemsIds) {
                    $receipt = $this->updateReceiptService->execute($id, $itemsIds);

                    return response()->json($receipt);
                });
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $response = $this->deleteReceiptService->execute($id);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        if ($response) {
            Cache::flush();
            return response()->json(['message' => 'Successfully deleted']);
        }
        return response()->json(['error' => 'Not found'], 404);
    }
}
