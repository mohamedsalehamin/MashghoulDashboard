<div class="account-body">
    <div class="reservation-content">
        <div class="reservation-head">
            <h2 class="account-title">@lang('site.buttons.doctor_recommendations')</h2>
            <div class="reservation-tool">
                <a
                    class="reservation-btn back-btn"
                    href="{{route('profile.reservations.show',$reservation->id)}}"
                >
                    @lang('site.buttons.back_to_reservation')
                </a>
            </div>
        </div>
        <div class="reservation-body">
            <h3 class="reservation-title">@lang('site.heading.doctor_diagnose')</h3>
            <div class="reservation-reason">
                <p class="reason-text">
                    {{$reservation->prescription->diagnosis??''}}
                </p>
            </div>
            <h3 class="reservation-title">@lang('site.heading.medicines_list')</h3>
            <div class="recommendations-table">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>@lang('site.fields.medicine_name')</th>
                        <th>@lang('site.fields.dose')</th>
                        <th>@lang('site.fields.other_notes')</th>
                    </tr>
                    @foreach($reservation->prescription->medicines as  $medicine)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$medicine->title}}</td>
                            <td>{{$medicine->description}}</td>
                            <td class="notes" >
                                {{$medicine->notes}}
                            </td>
                        </tr>
                    @endforeach


                </table>
            </div>
            <h3 class="reservation-title">@lang('site.heading.xrays_required')</h3>
            <div class="recommendations-table">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>@lang('site.fields.ray_name')</th>
                        <th>@lang('site.fields.other_notes')</th>
                    </tr>
                    <tr>
                    @foreach($reservation->prescription->rays as  $ray)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$ray->title}}</td>

                            <td class="notes" >
                                {{$ray->notes}}
                            </td>
                        </tr>
                    @endforeach

                </table>
            </div>
            <h3 class="reservation-title">@lang('site.heading.lab_tests_required')</h3>
            <div class="recommendations-table">
                <table class="table">
                    <tr>
                        <th>#</th>
                        <th>@lang('site.fields.analysis_name')</th>
                        <th>@lang('site.fields.other_notes')</th>
                    </tr>
                    @foreach($reservation->prescription->tests as  $analysis)
                        <tr>
                            <td>{{$loop->iteration}}</td>
                            <td>{{$analysis->title}}</td>

                            <td class="notes" >
                                {{$analysis->description}}
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>
