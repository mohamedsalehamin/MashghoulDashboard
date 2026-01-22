<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium">{{ __('forms.fields.profile_url') }}</h3>
            </div>
            
            <div class="flex items-center space-x-2 rtl:space-x-reverse" x-data="{ copied: false }">
                <input type="text" 
                       x-ref="urlInput"
                       class="w-full block rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 disabled:opacity-70 dark:text-white sm:text-sm" 
                       value="{{ $this->getProfileUrl() }}" 
                       readonly>
                <button x-on:click="
                    $refs.urlInput.select();
                    document.execCommand('copy');
                    copied = true;
                    setTimeout(() => copied = false, 2000);
                " 
                class="inline-flex items-center justify-center py-2 px-3 rounded-lg font-medium tracking-tight focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors outline-none bg-primary-600 text-white hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700">
                    <span x-text="copied ? '{{ __('forms.fields.copied') }}' : '{{ __('forms.fields.copy') }}'"></span>
                </button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>