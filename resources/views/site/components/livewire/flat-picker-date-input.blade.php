<div>

    @if($inputOnly)
        <div class="date-content" data-placeholder="YYYY/MM/DD">
            <input type="date"
                   placeholder="YYYY/MM/DD"
                   class="form-control"
                   id="{{$name}}"
            />
            <span class="date-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </span>
        </div>
    @else
        <div class="form-group">
            <label class="form-label">@lang('site.fields.dob') </label>

            <div class="date-content" data-placeholder="YYYY/MM/DD">
                <input type="date"
                       placeholder="YYYY/MM/DD"
                       class="form-control"
                       id="{{$name}}"
                />
                <span class="date-icon">
                                <i class="fa-regular fa-calendar"></i>
                            </span>
            </div>
            @error('value')
            <span class="text-danger"> {{$message}}</span>
            @enderror
        </div>
    @endif
</div>

@script
<script>
    const initialFlatPicker = (date) => {
        flatpickr('#{{$name}}', {
            locale: document.dir === "rtl" ? "ar" : "en",
            dateFormat: "Y-m-d",
            defaultDate: date,
            onChange: function (selectedDates, dateStr, instance) {
                $wire.$set('value', dateStr);
            },
        });
    };
    Livewire.hook('morph.updating', ({el, component}) => {
        initialFlatPicker(null);
    })
    $(document).ready(function () {
        initialFlatPicker("{{!is_null($value)?$value:today()}}");
    });

</script>

@endscript
