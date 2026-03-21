<?php

namespace App\Livewire\Site;

use App\ContentModule\Models\Level;
use App\Models\PointsExchange;
use App\Models\PointsUsage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ProfileRewardsList extends Component
{
    use WithPagination;

    #[On('refresh-rewards-list')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    protected function findBestExchangeOption(int $userPoints, $plans): ?Level
    {
        $canExchange = $plans->filter(fn (Level $p) => $p->canExchangeByUser(site()->user()));

        if ($canExchange->isEmpty()) {
            return null;
        }

        $exact = $canExchange->first(fn (Level $p) => $p->value === $userPoints);
        if ($exact) {
            return $exact;
        }

        $closest = $canExchange->sortBy(fn (Level $p) => abs($p->value - $userPoints))->first();
        if ($closest) {
            return $closest;
        }

        return $canExchange->sortBy('value')->first();
    }

    public function render()
    {
        $user = site()->user();
        $totalPoints = $user->getTotalPoints();

        $plans = Level::enabled()->latest()->get();
        $bestOption = $this->findBestExchangeOption((int) $totalPoints, $plans);
        $canExchange = $bestOption !== null;

        $winningPoints = $user->points()->where('transferred', false)->latest()->paginate(15, ['*'], 'earned_page');
        $winningPoints->withPath(parse_url(route('site.account.rewards'), PHP_URL_PATH));

        $exchanges = PointsExchange::where('user_id', $user->id)->latest()->paginate(15, ['*'], 'exchange_page');
        $exchanges->withPath(parse_url(route('site.account.rewards'), PHP_URL_PATH));

        $usages = PointsUsage::where('user_id', $user->id)->latest()->paginate(15, ['*'], 'usage_page');
        $usages->withPath(parse_url(route('site.account.rewards'), PHP_URL_PATH));

        $hasEarned = $winningPoints->isNotEmpty();
        $hasExchanges = $exchanges->isNotEmpty();
        $hasUsages = $usages->isNotEmpty();

        $firstActiveTab = $hasEarned ? 'earned' : ($hasExchanges ? 'exchange' : ($hasUsages ? 'usage' : 'earned'));

        return view('livewire.site.profile-rewards-list', [
            'totalPoints' => $totalPoints,
            'bestOption' => $bestOption,
            'canExchange' => $canExchange,
            'plans' => $plans,
            'winningPoints' => $winningPoints,
            'exchanges' => $exchanges,
            'usages' => $usages,
            'hasEarned' => $hasEarned,
            'hasExchanges' => $hasExchanges,
            'hasUsages' => $hasUsages,
            'firstActiveTab' => $firstActiveTab,
        ]);
    }
}
