<div class="lastSide">
    <button class="join_Us" wire:click="openModal">
        <div>
            @lang("site.heading.join_us_as_a_provider")
        </div>
    </button>
    @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)

        @continue($localeCode == LaravelLocalization::getCurrentLocale())
        <a rel="alternate"
           hreflang="{{ $localeCode }}"
           href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}"
           id="language-switcher"
           lang="{{ $properties['native'] }}">{{ Str::upper($localeCode) }}</a>
    @endforeach


    <div class="menu-icons open-me d-lg-none">
        <label for="check" class="">
            <input type="checkbox" id="check">
            <span></span>
            <span></span>
            <span></span>
        </label>
    </div>
</div>
