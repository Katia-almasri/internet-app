<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\fileUploadedEvent;
use App\Listeners\fileUploadedListenr;

use App\Events\fileCheckedInEvent;
use App\Listeners\fileCheckedInListener;


use App\Events\fileUpdateEvent;
use App\Listeners\fileUpdateListener;

use App\Events\fileCheckedOutEvent;
use App\Listeners\fileCheckedOutListener;





use App\Models\File;
use App\Observers\FileObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,

        ],

        fileUploadedEvent::class => [
            fileUploadedListenr::class,

        ],

        fileCheckedInEvent::class => [

            fileCheckedInListener::class,

        ],

        fileUpdateEvent::class => [

            fileUpdateListener::class,

        ],

        fileCheckedOutEvent::class => [

            fileCheckedOutListener::class,

        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

    }
}
