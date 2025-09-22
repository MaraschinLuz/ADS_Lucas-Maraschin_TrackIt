<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate; // <- importa o Facade correto
use App\Models\User;                 // <- importa o seu User

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Admin passa em qualquer ability/policy
        Gate::before(function (User $user, string $ability) {
            return $user->isAdmin() ? true : null;
        });
    }
}
