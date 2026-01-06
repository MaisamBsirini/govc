<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ComplaintRepositoryInterface;
use App\Repositories\ComplaintRepository;
use App\Models\Complaint;
use App\Observers\ComplaintObserver;
use App\Events\ComplaintUpdated;
use App\Listeners\SendComplaintNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */


public function register(): void
{
    $this->app->bind(
        ComplaintRepositoryInterface::class,
        ComplaintRepository::class
    );
}


    /**
     * Bootstrap any application services.
     */


public function boot(): void
{
    Complaint::observe(ComplaintObserver::class);

    Event::listen(
            ComplaintUpdated::class,
            [SendComplaintNotification::class, 'handle']
        );
}


}
