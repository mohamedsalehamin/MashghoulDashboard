<div class="form-group">
    <label class="form-label"> {{$label}}</label>
    <select id="{{$name}}" class="form-control" wire:model.live="value" @if($multiple) multiple @endif data-placeholder="@lang('site.heading.select')">
    <option value=""></option>
        @foreach($options as $id=>$title)
            <option value="{{$id}}">{{$title}}</option>
        @endforeach
    </select>

    @error("$name")
    <span class="text-danger"> {{$message}}</span>
    @enderror
</div>

@assets
<style>
    .select2-container {
        width: 100% !important;
    }
</style>
@endassets
@script
<script>
    Livewire.hook('morph.updating', ({el, component}) => {
        $("#{{$name}}").select2('destroy');
        $('#{{$name}}').select2({
            minimumResultsForSearch: Infinity,
        });
    })
    $(document).ready(function () {
        $('#{{$name}}').select2({
            minimumResultsForSearch: Infinity,
        });
        $('#{{$name}}').on('change', function (e) {
            $wire.$set('value', $('#{{$name}}').select2('val'));
        });
    });


</script>
@endscript
