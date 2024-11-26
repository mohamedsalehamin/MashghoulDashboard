@if($paginator->hasMorePages())
<div class="pagination">
    @foreach ($elements as $element)
        {{-- "Three Dots" Separator --}}
        @if (is_string($element))
            <li class="disabled" aria-disabled="true"><span>{{ $element }}</span></li>
        @endif

        {{-- Array Of Links --}}
        @if (is_array($element))
            @foreach ($element as $page => $url)
                <a href="{{ $url }}" class="@if($page == $paginator->currentPage())active @endif" >{{ $page }}</a>

            @endforeach
        @endif
    @endforeach
</div>
@endif

