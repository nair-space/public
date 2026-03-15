<?php

namespace App\Providers;

use App\Repositories\Interfaces\ClientBioRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;
use App\Repositories\Interfaces\WheelchairRepositoryInterface;
use App\Repositories\Interfaces\ClientAssessmentRepositoryInterface;
use App\Repositories\ClientBioRepository;
use App\Repositories\UserRepository;
use App\Repositories\WheelchairRepository;
use App\Repositories\ClientAssessmentRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind Repository Interfaces to Implementations
        $this->app->bind(ClientBioRepositoryInterface::class, ClientBioRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(WheelchairRepositoryInterface::class, WheelchairRepository::class);
        $this->app->bind(ClientAssessmentRepositoryInterface::class, ClientAssessmentRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
