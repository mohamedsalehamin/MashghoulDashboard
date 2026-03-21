<?php

namespace App\Console\Commands;

use App\CatalogModule\Models\Subscription;
use App\DefaultPanel\Enum\SubscriptionsStatusEnum;
use App\Notifications\ProviderSubscriptionExpiredNotification;
use App\Notifications\ProviderSubscriptionExpiringSoonNotification;
use App\DefaultPanel\Enum\UserStatus;
use App\UsersModule\Models\Provider;
use Illuminate\Console\Command;

class ProviderSubscriptionNotificationsCommand extends Command
{
    protected $signature = 'app:provider-subscription-notifications';

    protected $description = 'Send subscription notifications: 3 days before expiry and 1 day after expiry. Disable providers when subscription ends.';

    public function handle(): int
    {
        $countExpiring = 0;
        $countExpired = 0;

        // 3 days before: send reminder
        $expiringSoon = Subscription::where('status', SubscriptionsStatusEnum::PROCESSING)
            ->whereDate('end_date', '>=', now())
            ->whereDate('end_date', '<=', now()->addDays(3))
            ->get();

        foreach ($expiringSoon as $subscription) {
            $subscription->subscriber->notify(new ProviderSubscriptionExpiringSoonNotification);
            $countExpiring++;
        }

        // 1 day after ended: send notification, mark expired, disable provider
        $justExpired = Subscription::where('status', SubscriptionsStatusEnum::PROCESSING)
            ->whereDate('end_date', now()->subDay())
            ->get();

        foreach ($justExpired as $subscription) {
            $subscription->subscriber->notify(new ProviderSubscriptionExpiredNotification);
            $subscription->update(['status' => SubscriptionsStatusEnum::EXIPRED]);
            $provider = Provider::where('user_id', $subscription->user_id)->first();
            if ($provider?->user) {
                $provider->user->update(['active' => UserStatus::IN_ACTIVE]);
            }
            $countExpired++;
        }

        // Also handle subscriptions that ended more than 1 day ago but weren't processed
        $oldExpired = Subscription::where('status', SubscriptionsStatusEnum::PROCESSING)
            ->where('end_date', '<', now()->subDay())
            ->get();

        foreach ($oldExpired as $subscription) {
            $subscription->update(['status' => SubscriptionsStatusEnum::EXIPRED]);
            $provider = Provider::where('user_id', $subscription->user_id)->first();
            if ($provider?->user) {
                $provider->user->update(['active' => UserStatus::IN_ACTIVE]);
            }
            $countExpired++;
        }

        $this->info("Sent {$countExpiring} expiring-soon notifications, processed {$countExpired} expired subscriptions.");

        return self::SUCCESS;
    }
}
