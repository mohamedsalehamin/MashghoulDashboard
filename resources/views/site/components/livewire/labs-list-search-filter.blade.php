<div class="archive-head">
    <h2 class="search-title" >
        @lang('site.heading.data_result')
        (<span class="text-black" x-text="$wire.$parent.count"></span>)
    </h2>
    <div class="search-tools">
        <div class="search-input">
                    <span class="search-icon">
                      <i class="fa-regular fa-magnifying-glass"></i>
                    </span>
            <input type="search"
                   placeholder="@lang('site.heading.search_with_lab_name')"
                   wire:model.live.debounce="$parent.filters.q"
            />

        </div>
        <div class="search-sort">
            <label class="sort-label">@lang('site.heading.order_by')</label>
            <select class="search-select"
                    wire:model.live.debounce="$parent.filters.order_by"
            >
                <option selected value="topRated">@lang('site.fields.top_rated')</option>
                <option value="available">@lang('site.fields.available_today')</option>
            </select>
        </div>
    </div>
</div>
@assets
<style>
    .select2-container {
        width: 200px !important;
    }
</style>
@endassets
@script
<script>
    Livewire.hook('morph.updating', ({el, component}) => {
        $(".search-select").select2('destroy');
        $('.search-select').select2({
            minimumResultsForSearch: Infinity,
        });
    })
    $(document).ready(function () {
        $('.search-select').select2({
            minimumResultsForSearch: Infinity,
        }).on('change', function (e) {
            console.log('change');
            $wire.$parent.$set('filters.order_by', $('.search-select').select2('val'));
        });
    });


</script>
@endscript
