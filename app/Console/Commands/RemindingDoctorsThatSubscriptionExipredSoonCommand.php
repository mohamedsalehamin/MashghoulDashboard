<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Subscription;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\Notifications\RemindingDoctorToRenewPlanNotification;
use Illuminate\Console\Command;

class RemindingDoctorsThatSubscriptionExipredSoonCommand extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reminding-doctors-that-subscription-exipred-soon-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle() {
        $count = 0;
        Subscription::where('status', SubscriptionsStatusEnum::PROCESSING->value)
            ->where('end_date', '<', now()->addDays(7))
            ->get()
            ->each(function (Subscription $subscription) use (&$count) {
                $count++;
                $subscription->subscriber->notify(new RemindingDoctorToRenewPlanNotification);
            });
        $this->info($count . ' doctors have been reminded of their upcoming subscription expiration.');

    }
}
