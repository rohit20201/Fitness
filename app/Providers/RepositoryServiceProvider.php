<?php
namespace App\Providers;

use App\Repositories\Cache\MaintenanceCacheRepository;
use App\Repositories\Interfaces\ProductEquipmentRepositoryInterface;
use App\Repositories\Repository\MaintenanceRepository;
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
        $this->app->bind(
            'App\Repositories\Interfaces\BlogRepositoryInterface',
            'App\Repositories\Repository\BlogRepository'
        );
        $this->app->bind(
            'App\Repositories\Interfaces\FaqRepositoryInterface',
            'App\Repositories\Repository\FaqRepository'
        );
        $this->app->bind(
            'App\Repositories\Interfaces\BannerRepositoryInterface',
            'App\Repositories\Repository\BannerRepository'
        );
        $this->app->bind(
            'App\Repositories\Interfaces\TestimonialRepositoryInterface',
            'App\Repositories\Repository\TestimonialRepository'
        );
        $this->app->bind(
            'App\Repositories\Interfaces\HomeRepositoryInterface',
            'App\Repositories\Repository\HomeRepository'
        );
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
