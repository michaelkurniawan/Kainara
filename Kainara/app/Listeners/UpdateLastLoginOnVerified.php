<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Verified;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon; // Import Carbon

class UpdateLastLoginOnVerified
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Verified $event): void
    {
        // Update the last_login timestamp for the verified user
        $event->user->forceFill([
            'last_login' => Carbon::now(),
        ])->save();
    }
}