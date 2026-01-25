@php
    use Filament\Support\Icons\Heroicon;
    use Filament\Notifications\View\NotificationsIconAlias;
    use Filament\Notifications\View\Components\NotificationComponent;
    use Filament\Notifications\View\Components\NotificationComponent\IconComponent;
    use Illuminate\Support\Js;
    use Illuminate\View\ComponentAttributeBag;

    $status = $getStatus();
    $color = $getColor() ?? 'gray';
    $isInline = $isInline();
    $title = $getTitle();
    $hasTitle = filled($title);
    $date = $getDate();
    $hasDate = filled($date);
    $body = $getBody();
    $hasBody = filled($body);

    $getRouteSafely = function($routeName, $params) {
        try {
            return route($routeName, $params);
        } catch (\Exception $e) {
            return null;
        }
    };

    $mapper = fn($entityType, $entityID) => match ($entityType) {
        'reservation' => provider()?->id ? \App\ProviderPanel\Filament\Resources\ReservationResource::getUrl('view', [$entityID]) : \App\CatalogModule\Resources\ReservationResource::getUrl('view', [$entityID]),
        'customer' => \App\UsersModule\Resources\CustomerResource::getUrl('edit', [$entityID]),
        'branch' => $getRouteSafely('filament.admin.resources.catalog.branches.edit', $entityID),
        'product' => $getRouteSafely('filament.admin.resources.catalog.products.edit', $entityID),
        default => null,
    };

    $attributes = (new ComponentAttributeBag)
        ->merge([
            'wire:key' => "{$getId()}.notifications.{$getId()}",
            'x-on:close-notification.window' => "if (\$event.detail.id == '{$getId()}') close()",
        ], escape: false)
        ->color(NotificationComponent::class, $color)
        ->class([
            'fi-no-notification',
            'fi-inline' => $isInline,
            "fi-status-{$status}" => $status,
        ]);

    if (isset($getViewData()['entity_type'], $getViewData()['entity_id'])) {
        $entityUrl = $mapper($getViewData()['entity_type'], $getViewData()['entity_id']);
        if ($entityUrl) {
            $attributes->merge(['onclick' => "location.href='{$entityUrl}'"], escape: false);
        }
    }
@endphp

<div
    x-data="notificationComponent({ notification: {{ Js::from($toArray()) }} })"
    x-transition:enter-start="fi-transition-enter-start"
    x-transition:enter-end="fi-transition-enter-end"
    x-transition:leave-start="fi-transition-leave-start"
    x-transition:leave-end="fi-transition-leave-end"
    {!! $attributes !!}
>
    @if ($icon = $getIcon())
        {!! \Filament\Support\generate_icon_html(
            $icon,
            attributes: (new ComponentAttributeBag)->color(IconComponent::class, $getIconColor())->class(['fi-no-notification-icon']),
            size: $getIconSize(),
        )?->toHtml() !!}
    @endif

    <div class="fi-no-notification-main">
        @if ($hasTitle || $hasDate || $hasBody)
            <div class="fi-no-notification-text">
                @if ($hasTitle)
                    <h3 class="fi-no-notification-title">
                        {!! str($title)->sanitizeHtml() !!}
                    </h3>
                @endif

                @if ($hasDate)
                    <time class="fi-no-notification-date">
                        {{ $date }}
                    </time>
                @endif

                @if ($hasBody)
                    <div class="fi-no-notification-body">
                        {!! str($body)->sanitizeHtml() !!}
                    </div>
                @endif
            </div>
        @endif

        @if ($actions = $getActions())
            <div class="fi-ac fi-no-notification-actions">
                @foreach ($actions as $action)
                    {!! $action->toHtml() !!}
                @endforeach
            </div>
        @endif
    </div>

    <button
        type="button"
        x-on:click="close"
        class="fi-icon-btn fi-no-notification-close-btn"
    >
        {!! \Filament\Support\generate_icon_html(Heroicon::XMark, alias: NotificationsIconAlias::NOTIFICATION_CLOSE_BUTTON)->toHtml() !!}
    </button>
</div>
