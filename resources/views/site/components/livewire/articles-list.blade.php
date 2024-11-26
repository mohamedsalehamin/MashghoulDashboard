<div class="container">
    <div class="blog-head">
        <h1 class="page-title">@lang('site.heading.articles')</h1>
        <div class="search-input">
            <span class="search-icon">
              <i class="fa-regular fa-magnifying-glass"></i>
            </span>
            <input type="search" placeholder="@lang('site.heading.search_with_lab_name')"
                   wire:keyup.debounce="updateFilters('query',$event.target.value)"
            />
            <button
                class="clear-btn"
                type="button"
                onclick="$(this).prev().val('')"
            >
                <i class="fa-regular fa-xmark"></i>
            </button>
        </div>
    </div>
    <div class="categories-filters">
        <ul class="categories-list">
            <li>
                <button
                    type="button"
                    class="category-btn filter-btn @if(!$filters['category'])active @endif"
                    id="all"
                    wire:click="updateFilters('category',null)"
                >
                    @lang('site.heading.show_all')
                </button>
            </li>
            @foreach($categories as $category)
                <li>
                    <button
                        type="button"
                        class="category-btn filter-btn @if($filters['category'] == $category->id) active @endif"

                        wire:click="updateFilters('category',{{$category->id}})"
                    >
                        {{$category->name}}
                    </button>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="blog-grid">
        @foreach($articles as $article)
            <div class="blog-item">
                <a href="{{route('articles.show',$article->id)}}"
                   class="blog-img _loading-img _lazy-img-parent"
                >
                    <img src="{{$article->getFirstMediaUrl()}}"  alt="{{$article->title}}"
                         title="{{$article->title}}"/>
                </a>
                <div class="blog-info">
                    <span class="blog-date">{{$article->created_at->format("Y/m/d - H:i")}}</span>
                    <h3 class="blog-title">
                        <a href="{{route('articles.show',$article->id)}}"> {{$article->title}} </a>
                    </h3>
                    <p class="blog-summary">
                        {{Str::sanitizeHtml(strip_tags($article->description),200)}}
                    </p>
                    <a href="{{route('articles.show',$article->id)}}"
                       class="blog-link"> @lang('site.buttons.show_more') </a>
                    <a class="blog-category"> {{$article->category->name}}</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
