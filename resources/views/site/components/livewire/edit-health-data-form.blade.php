<div class="account-body">
    <h2 class="account-title">@lang('site.heading.health_data')</h2>
    <div class="accout-form">
        <form wire:submit.prevent="handle">
            <div class="nav account-tabs">
                <button
                    type="button"
                    @if($active_tab =='health_data') class="active" @endif

                    data-bs-toggle="tab"
                    data-bs-target="#tab-1"
                    wire:click="active_tab='health_data'"
                >
                    @lang('site.heading.basic_info')
                </button>
                <button
                        type="button"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-2"
                        wire:click="active_tab='diseases'"
                        @if($active_tab =='diseases') class="active" @endif
                >
                    @lang('site.heading.chronic_diseases')
                </button>
                <button
                        type="button"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-3"
                        wire:click="active_tab='analysis'"
                        @if($active_tab =='analysis') class="active" @endif

                >
                    @lang('site.heading.attached_tests')

                </button>
            </div>

            <div class="tab-content">
                <div class="tab-pane fade  @if($active_tab =='health_data')show active @endif" id="tab-1">
                    <div class="account-tab">
                        <div class="form-grid-2">
                            <div class="form-group">
                                <label class="form-label"> @lang("site.fields.length") </label>
                                <input type="text" class="form-control" wire:model="health_data.length"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label"> @lang("site.fields.blood_type") </label>
                                <input type="text" class="form-control" wire:model="health_data.blood_type"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label"> @lang("site.fields.sugar") </label>
                                <input type="text" class="form-control" wire:model="health_data.blood_sugar_rate"/>
                            </div>
                            <div class="form-group">
                                <label class="form-label"> @lang("site.fields.blood_pressure_rate") </label>
                                <input type="text" class="form-control" wire:model="health_data.blood_pressure_rate"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if($active_tab =='diseases') show  active @endif" id="tab-2">
                    <div class="account-tab">
                        <div class="checkboxes">
                            @foreach($diseases as $disease)
                                <div class="form-group">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox"

                                                   wire:model="chronic_diseases_ids"
                                                   value="{{$disease->id}}"
                                                   @if( in_array($disease->id,$chronic_diseases_ids))checked @endif/>
                                            <span class="mark"><i class="fa-regular fa-check"></i></span>
                                            <span class="text"> {{$disease->name}} </span>
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade @if($active_tab =='analysis')show  active @endif" id="tab-3">
                    <div class="account-tab">
                        <div class="form-group">
                            <label class="file-content">
                                <input type="file" wire:model="analysis_file"/>
                                <div class="file-placeholder">
                                    <div class="placeholder-icon">
                                        <img src="images/icons/upload.svg" class="svg"/>
                                    </div>
                                    <h5 class="placeholder-text">
                                        @lang('site.fields.file_text')
                                        <span>@lang('site.fields.download_file')</span>
                                    </h5>
                                </div>
                            </label>
                        </div>
                        <div class="files-list">
                            @foreach($analysis as $file)
                                <div class="file-item">
                                    <div class="file-info">
                                        <h5 class="file-name">
                                            <a href="{{$file->getFirstMediaUrl()}}" download>
                                                {{$file->name}}
                                            </a>
                                        </h5>
                                        <span class="file-date">
                                    @lang('site.fields.created_at') {{$file->created_at->format('Y-m-d')}}
                              </span>
                                    </div>
                                    <div class="file-tools">
                                        <a href="{{$file->getFirstMediaUrl()}}" download class="file-btn">
                                            <i class="fa-light fa-download"></i>
                                        </a>
                                        <button
                                                type="button"
                                                class="file-btn"
                                                wire:click="removeAnalysis({{$file->id}})"
                                        >
                                            <i class="fa-light fa-trash-can"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <button class="submit-btn main-btn">
                @lang('site.buttons.save_data')
            </button>
        </form>
    </div>
</div>
