<div class="archive-sidebar">
    <div class="search-filters">
        <h3 class="filters-title">@lang("site.heading.filters")</h3>
        <div class="filters-list ">
            <div class="filter-item">
                <h4 class="filter-title @if(in_array('titles',$activeFilters)) active @endif"
                    wire:click="updateFilter('titles')"
                >
                    <i class="fa-light fa-graduation-cap"></i>
                    @lang('site.fields.title')
                </h4>
                <div class="filter-values" @if(in_array('titles',$activeFilters)) style="display: block" @endif>
                    @foreach($titles as $id=>$title)
                        <label class="filter-value">
                            <input type="checkbox"
                                   value="{{$id}}"
                                   wire:model.live.debounce="$parent.filters.titles"
                            />
                            <span class="check-mark">
                            <i class="fa-regular fa-check"></i>
                          </span>
                            <span class="text">{{$title}}</span>
                        </label>
                    @endforeach


                </div>
            </div>

            <template x-if="$wire.$parent.filters.specialty_id">
                <div class="filter-item">
                    <h4 class="filter-title @if(in_array('specializations',$activeFilters)) active @endif"
                        wire:click="updateFilter('specializations')">
                        <i class="fa-light fa-stethoscope"></i>
                        @lang('site.fields.secondary_specialization')
                    </h4>

                    <div class="filter-values"
                         @if(in_array('specializations',$activeFilters)) style="display: block" @endif
                    >
                        @foreach($specializations as $id=>$specialization)
                            <label class="filter-value">
                                <input type="checkbox"
                                       value="{{$id}}"
                                       wire:model.live.debounce="$parent.filters.specialization_ids"

                                />


                                <span class="check-mark">
                            <i class="fa-regular fa-check"></i>
                          </span>
                                <span class="text">{{$specialization}}</span>
                            </label>
                        @endforeach


                    </div>
                </div>
            </template>

            <div class="filter-item">
                <h4 class="filter-title" @if(in_array('gender',$activeFilters)) active @endif
                wire:click="updateFilter('gender')">
                    <i class="fa-light fa-venus-mars"></i>
                    @lang('site.fields.gender')
                </h4>
                <div class="filter-values" @if(in_array('gender',$activeFilters)) style="display: block" @endif>
                    @foreach(\App\DefaultPanel\Enum\GenderEnum::cases() as $case)
                        <label class="filter-value">
                            <input type="checkbox"
                                   wire:model.live.debounce="$parent.filters.gender"
                                   value="{{$case->value}}"
                            />
                            <span class="check-mark">
                            <i class="fa-regular fa-check"></i>
                          </span>
                            <span class="text">{{$case->getLabel()}}</span>
                        </label>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
    <button class="filter-trigger">
        <i class="fa-light fa-filter-list"></i>
    </button>
</div>
