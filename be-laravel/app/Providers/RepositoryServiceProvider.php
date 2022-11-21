<?php

namespace App\Providers;

use App\Repositories\ItemsRepository;
use App\Repositories\ItemsRepositoryInterface;
use App\Repositories\ReceiptsRepository;
use App\Repositories\ReceiptsRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ReceiptsRepositoryInterface::class, ReceiptsRepository::class);
        $this->app->bind(ItemsRepositoryInterface::class, ItemsRepository::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
