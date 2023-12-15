<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\AuthUser;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('transaction-statistics', function (?AuthUser $user) {
            return $user != null && $user->user_type == 'A';
        });
        Gate::define('vcards-statistics', function (?AuthUser $user) {
            return $user != null && $user->user_type == 'A';
        });
    }
}
