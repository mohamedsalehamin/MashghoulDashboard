<x-filament-widgets::widget>
    <x-filament::section
        :heading="__('panel.profile_setup.title')"
        :description="__('panel.profile_setup.description')"
    >
        <ul class="list-inside list-disc space-y-2 text-sm text-gray-700 dark:text-gray-200">
            @foreach ($items as $item)
                <li>
                    <a
                        href="{{ $item['url'] }}"
                        class="font-medium text-primary-600 underline decoration-dotted hover:text-primary-500 dark:text-primary-400"
                    >
                        {{ $item['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </x-filament::section>
</x-filament-widgets::widget>
