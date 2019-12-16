<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\User;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Gate::define('admin-dashboard', function($user){
            return $user->user_type == 'Admin';
        });

        Gate::define('edit_users', function($user){
            return $user->user_type == 'Admin';
        });

        Gate::define('edit_settings', function($user){
            return $user->user_type == 'Admin';
        });
    }
}
