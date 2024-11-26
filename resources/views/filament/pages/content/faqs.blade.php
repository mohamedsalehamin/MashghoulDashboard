<x-filament-panels::page>
    @foreach($faqs as $faq)
        <div >
            {{$faq->question}}
        </div>
        {{$faq->answer}}
        <div class="border border-1"></div>
    @endforeach
</x-filament-panels::page>
