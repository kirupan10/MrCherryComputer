<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\ShopTypes\Tech\Models\TechProduct::class => \App\Policies\TechProductPolicy::class,
        \App\ShopTypes\Tech\Models\TechSerialNumber::class => \App\Policies\TechSerialNumberPolicy::class,
        \App\ShopTypes\Tech\Models\TechWarrantyClaim::class => \App\Policies\TechWarrantyClaimPolicy::class,
        \App\ShopTypes\Tech\Models\TechRepairJob::class => \App\Policies\TechRepairJobPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
