<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Services\RetentionService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('retention:reminders', function (RetentionService $retention) {
    $this->info('Scheduling and processing reminders & thank you follow-ups...');
    $reminders = $retention->scheduleReminders();
    $thankYous = $retention->scheduleThankYou();
    $processed = $retention->processPendingMessages();

    $this->info("Reminders scheduled: {$reminders}");
    $this->info("Thank yous scheduled: {$thankYous}");
    $this->info("Messages processed and sent: {$processed}");
})->purpose('Schedule and send booking reminders & thank-you followups')
  ->hourly();

Artisan::command('retention:nudges', function (RetentionService $retention) {
    $this->info('Scheduling and processing rebooking nudges...');
    $nudges = $retention->scheduleRebookingNudges();
    $processed = $retention->processPendingMessages();

    $this->info("Rebooking nudges scheduled: {$nudges}");
    $this->info("Messages processed and sent: {$processed}");
})->purpose('Schedule and send re-booking nudges to churn-risk customers')
  ->daily();
