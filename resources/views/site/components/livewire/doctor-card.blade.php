<div class="doctor-item">
    <a
        href="{{route('doctors.show',$doctor->id)}}"
        class="doctor-img"
    >
        <img src="{{$doctor->image}}" />
    </a>
    <div class="doctor-information">
        <div class="doctor-head">
            <h3 class="doctor-name">
                <a href="{{route('doctors.show',$doctor->id)}}"> {{$doctor->name}} </a>
            </h3>

            <span class="doctor-status {{$doctor->isAvailableToday()?'available':'unavailable'}}"> {{$doctor->isAvailableToday()?__('site.enum.available'):__('site.enum.unavailable')}}</span>
        </div>
        <div class="doctor-rate">
            @foreach(range(1,5) as $star)
                <i class="fa-solid fa-star @if($doctor->avgRate()>= $star) active  @endif "></i>
            @endforeach
        </div>

        <a href="{{route('specialties.show',$doctor->specialization->id)}}" class="doctor-specialty">
            <img src="{{$doctor->specialization?->getFirstMediaUrl()}}" class="img-fluid"/>
            {{$doctor->specialization->name}}
        </a>
        <p class="doctor-summary">
            @foreach($doctor->specializations as $one)
                <a href="{{route('specialties.show',$one->id)}}">{{$one->name}}</a>
                @if(!$loop->last)
                    ,
                @endif
            @endforeach


        </p>
        <div class="doctor-tags">
            <a class="doctor-tag">
                {{$doctor->nationality->name}}
            </a>
            <a class="doctor-tag">
                {{$doctor->clinic?->city?->name}}
            </a>
        </div>
        <div class="doctor-footer">
            <a href="{{route('doctors.show',$doctor)}}" class="doctor-btn">
                <i class="fa-regular fa-calendar"></i>
                @lang('site.heading.reserve')
            </a>
            <span class="doctor-hint">  @lang('site.heading.reserve_via') {{$doctor->times_type?->getLabel()}} </span>


        </div>
        <button type="button" class="doctor-wishlist {{$this->isFavorite() ?'active':''}}" wire:click="toggleFavorite">
            <i class="fa-regular fa-heart"></i>
        </button>
    </div>
</div>

