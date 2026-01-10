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
use App\Services\ReportService; // ✅ نضيف الـ Service هنا

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // تسجيل الـ Repository
        $this->app->bind(
            ComplaintRepositoryInterface::class,
            ComplaintRepository::class
        );

        // تسجيل الـ Service (اختياري لكن يضمن الحقن)
        $this->app->singleton(ReportService::class, function ($app) {
            return new ReportService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Observer للشكاوي
        Complaint::observe(ComplaintObserver::class);

        // Listener للحدث
        Event::listen(
            ComplaintUpdated::class,
            [SendComplaintNotification::class, 'handle']
        );
    }
}
