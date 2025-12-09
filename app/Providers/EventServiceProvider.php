<?php

namespace App\Providers;

use App\Events\AppointmentCreated;
use App\Events\AppointmentStatusUpdated;
use App\Events\MessageSent;
use App\Listeners\SendAppointmentCreatedNotification;
use App\Listeners\SendAppointmentStatusUpdatedNotification;
use App\Listeners\SendMessageSentNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        AppointmentCreated::class => [
            SendAppointmentCreatedNotification::class,
        ],
        AppointmentStatusUpdated::class => [
            SendAppointmentStatusUpdatedNotification::class,
        ],
        MessageSent::class => [
            SendMessageSentNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
