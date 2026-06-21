<?php

namespace App\Providers;

use App\Models\User;
use App\Repositories\FilmsRepositories\FilmsRepositoryInterface;
use App\Repositories\FilmsRepositories\OmdbFilmsRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestFactoryInterface;
use GuzzleHttp\Psr7\HttpFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(RequestFactoryInterface::class, HttpFactory::class);
        $this->app->bind(FilmsRepositoryInterface::class, function ($app) {
            return new OmdbFilmsRepository(
                httpClient: $app->make(Client::class),
                requestFactory: $app->make(RequestFactoryInterface::class),
                apiKey: config('services.omdb.key'),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('moderator', function (User $user) {
            return $user->hasRole('moderator');
        });
    }
}
