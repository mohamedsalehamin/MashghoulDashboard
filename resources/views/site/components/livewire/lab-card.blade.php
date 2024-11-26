<div class="swiper-slide">
    <div class="lab-item">
        <a
            href="{{route('labs.show',$lab->id)}}"
            class="lab-img "
        >

            <img src="{{$lab->image}}" class="laozy-img" alt="{{$lab->title}}"/>
        </a>
        <div class="lab-information">
            <div class="lab-head">
                <h3 class="lab-name">
                    <a href="{{route('labs.show',$lab->id)}}"> {{$lab->title}} </a>
                </h3>
                <span
                        class="lab-status {{$lab->isAvailableToday()?'available':'unavailable'}}"> {{$lab->isAvailableToday()?__('site.enum.available'):__('site.enum.unavailable')}}</span>
            </div>
            <div class="lab-rate">
                @foreach(range(1,5) as $star)
                    <i class="fa-solid fa-star @if($lab->avgRate()>= $star) active  @endif "></i>
                @endforeach
            </div>
            <p class="lab-summary">
                {{$lab->workingDaysList()}}
            </p>
            <div class="lab-tags">
                <a href="labsSearch.html" class="lab-tag">
                    {{$lab?->city?->name}}
                </a>
            </div>
            <div class="lab-footer">
                <a href="{{route('labs.show',$lab->id)}}" class="lab-btn">
                    <i class="fa-regular fa-calendar"></i>
                    @lang('site.heading.reserve')
                </a>
            </div>
            <button type="button" class="lab-wishlist {{$this->isFavorite() ?'active':''}}" wire:click="toggleFavorite">
                <i class="fa-regular fa-heart"></i>
            </button>
        </div>
    </div>
</div>
