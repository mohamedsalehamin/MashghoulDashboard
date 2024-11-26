<div class="subspecialties-filters">
    <ul class="subspecialties-list">
        <li>
            <button
                type="button"
                class="subspecialty-btn filter-btn "
                :class="!$wire.$parent.filters.specialty_id ?'active':''"

                wire:click="$dispatch('setFilter',{'id':'specialty_id','value':null})"
            >
                @lang("site.heading.show_all")
            </button>
        </li>
        @foreach($specialties as $specialty)
            <li>
                <button
                    type="button"
                    class="subspecialty-btn filter-btn  "
                    :class="$wire.$parent.filters.specialty_id =={{$specialty->id}} ?'active':''"
                    x-init="$wire.$parent.filters.specialty_id =={{$specialty->id}} ?$dispatch('selectedSpecializationUpdated',{'id':{{$specialty->id}}}):''"
                    wire:click="$dispatch('setFilter',{'id':'specialty_id','value':'{{$specialty->id}}'}) || $dispatch('selectedSpecializationUpdated',{'id':{{$specialty->id}}})"
                >
                    {{$specialty->name}}
                </button>
            </li>
        @endforeach
    </ul>
</div>
