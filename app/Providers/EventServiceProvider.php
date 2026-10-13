<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\ProjectSubmitted::class => [
            \App\Listeners\LogAndNotifyProjectSubmitted::class,
        ],
        \App\Events\ProjectApproved::class => [
            \App\Listeners\LogAndNotifyProjectApproved::class,
        ],
        \App\Events\ProjectActivated::class => [
            \App\Listeners\LogAndNotifyProjectActivated::class,
        ],
        \App\Events\ProjectCompleted::class => [
            \App\Listeners\LogAndNotifyProjectCompleted::class,
        ],
        \App\Events\BudgetDisbursed::class => [
            \App\Listeners\LogAndNotifyBudgetDisbursed::class,
        ],
        \App\Events\EvaluationSubmitted::class => [
            \App\Listeners\LogAndNotifyEvaluationSubmitted::class,
        ],
        \App\Events\PaperApproved::class => [
            \App\Listeners\LogAndNotifyPaperApproved::class,
        ],
        \App\Events\ExtensionApproved::class => [
            \App\Listeners\LogAndNotifyExtensionApproved::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
