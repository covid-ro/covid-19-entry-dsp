@extends('layouts.app')

@if ( Auth::user()->username !== env('ADMIN_USER') )
@section('js_scripts')
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/document-font-bold.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document-font-normal.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document-trans.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document.js' )}}"></script>
@endsection
@endif

@section('content')
<div class="container">
    @if (session('message'))
    <div class="alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
        {{ session('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif
    @if ( Auth::user()->username !== env('ADMIN_USER') )
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card alert ajax-msg alert-dismissible fade show">
                <span id="ajax-text-message"></span>
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="top-title float-left">
                    {{ __('app.Declaration header') }}
                    @if ($declaration) <strong>{{ $declaration['code'] }}</strong> @endif
                    </h5>
                    @if (!empty($signature))
                        <img src="/icons/check.svg" alt="" width="20px" height="20px">
                    @else
                        <img src="/icons/attention.svg" alt="" width="20px" height="20px">
                    @endif
                    <div class="float-right">
                        <form method="POST" action="{{ route('change-lang') }}">
                            @csrf
                            <div class="form-group row" id="change-language">
                                <select id="lang" name="lang" class="form-control form-control-sm" onchange="this.form.submit()">
                                    <option value="ro"{{ ( app()->getLocale()== 'ro') ? ' selected' : ''
                                    }}>{{ __('app.romanian') }}</option>
                                    <option value="en"{{ ( app()->getLocale()== 'en') ? ' selected' : ''
                                    }}>{{ __('app.english') }}</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="float-right">
                        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm btn-top" role="button"
                           aria-pressed="true">
                            {{ __('app.Declarations list') }}
                        </a>
                    </div>
                    @if ( Auth::user()->username !== env('ADMIN_USER') )
                    <div class="float-right">
                        <a href="javascript:void(0);" id="print-declaration" class="btn btn-danger btn-sm btn-top"
                           role="button"
                           aria-pressed="true">
                            {{ __('app.Print') }}
                        </a>
                    </div>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($declaration)
                    <section id="declaration-view">
                        <div class="row border border-dark" id="header-declaration">
                            <div class="col-md-4 offset-4 text-center">
                                <h4 class="text-uppercase">{{ __('app.Declaration') }}</h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <h4 class="text-uppercase">{{ ( app()->getLocale()== 'ro') ? 'RO/EN' : 'EN/RO' }}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <table class="table table-bordered border border-dark">
                                    <tbody>
                                        <tr>
                                            <td width="70%">
                                                <h5>{{ __('app.DSP measure') }}:</h5>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="dsp-measure"
                                                           id="hospitalisation" value="hospital"
                                                            {{ ($declaration['dsp_measure'] &&
                                                            $declaration['dsp_measure'] === 'hospital') ?
                                                            'checked' : ''}}>
                                                    <label class="form-check-label" for="hospitalisation">
                                                        <strong>{{ __('app.Hospitalisation') }}</strong>;</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="dsp-measure"
                                                           id="quarantine" value="quarantine"
                                                        {{ ($declaration['dsp_measure'] &&
                                                        $declaration['dsp_measure'] === 'quarantine') ?
                                                        'checked' : ''}}>
                                                    <label class="form-check-label" for="quarantine">
                                                        <strong>{{ __('app.Quarantine') }}</strong>;</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="dsp-measure"
                                                           id="isolation" value="isolation"
                                                        {{ ($declaration['dsp_measure'] &&
                                                        $declaration['dsp_measure'] === 'isolation') ?
                                                        'checked' : ''}}>
                                                    <label class="form-check-label" for="isolation">
                                                        <strong>{{ __('app.Isolation') }}</strong>;</label>
                                                </div>
                                            </td>
                                            <td>
                                                <h5>{{ __('app.DSP signature') }}</h5>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row border border-dark" id="private-data">
                            <div class="col-md-9 text-left">
                                <table class="table table-sm table-borderless">
                                    @if ( app()->getLocale() == 'ro' )
                                        <tr>
                                            <td>
                                                {{ __('app.Name in declaration') }}: <strong class="text-uppercase">{{ $declaration['name'] }}</strong>
                                                &nbsp;&nbsp;
                                                {{ __('app.Surname') }}: <strong class="text-uppercase">{{ $declaration['surname'] }}</strong>
                                            </td>
                                        </tr>
                                    @else
                                        <tr>
                                            <td>
                                                {{ __('app.Surname') }}: <strong class="text-uppercase">{{ $declaration['surname'] }}</strong>
                                                &nbsp;&nbsp;
                                                {{ __('app.Name in declaration') }}: <strong class="text-uppercase">{{ $declaration['name'] }}</strong>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>
                                            {{ __('app.CNP') }}:
                                            @if( $declaration['cnp'] )
                                                &nbsp;<strong>{{ $declaration['cnp'] }}</strong>
                                            @else
                                                &nbsp;___________________________________________
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('app.Date of birth v1') }}:
                                            &nbsp;{{ __('app.Year') }} <strong>{{ $declaration['birth_date_year'] }}</strong>,
                                            &nbsp;{{ __('app.Month') }} <strong>{{ $declaration['birth_date_month'] }}</strong>,
                                            &nbsp;{{ __('app.Day') }} <strong>{{ $declaration['birth_date_day'] }}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ __('app.Main address') }}:<br />
                                            @if( $declaration['home_address'] )
                                                <strong>{{ $declaration['home_address'] }}</strong>
                                            @else
                                                ____________________________________________________________________
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-3 text-right">
                                <img src="{{ $qrCode }}" alt="" title="" />
                            </div>
                        </div>
                        <div class="row border border-dark" id="transit-data">
                            <div class="col-md-12 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>
                                            {{ __('app.Travelling from and') }}:&nbsp;
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_country'] }}
                                            </strong>
                                            @if ( strlen($declaration['itinerary']) > 0 )
                                            <br />
                                            {{ __('app.Transited') }} {!! $declaration['itinerary'] !!}
                                            @endif
                                        </td>
                                    </tr>
{{--                                    <tr>--}}
{{--                                        <td>--}}
{{--                                            {{ __('app.City from and') }}:&nbsp;--}}
{{--                                            <strong class="text-uppercase">--}}
{{--                                                {{ $declaration['travelling_from_city'] }}--}}
{{--                                            </strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
{{--                                    <tr>--}}
{{--                                        <td>--}}
{{--                                            {{ __('app.Date travelling') }}:--}}
{{--                                            &nbsp;{{ __('app.Year') }} <strong>{{ $declaration['travelling_date_year'] }}</strong>,--}}
{{--                                            &nbsp;{{ __('app.Month') }} <strong>{{ $declaration['travelling_date_month'] }}</strong>,--}}
{{--                                            &nbsp;{{ __('app.Day') }} <strong>{{ $declaration['travelling_date_day'] }}</strong>--}}
{{--                                        </td>--}}
{{--                                    </tr>--}}
                                </table>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom">
                                    {!! __('app.Self responsibility') !!}:<br />
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I have taken note of the fact that') !!}
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.for the implementation of the isolation measure') !!}
                                    {{ __('app.Other address') }}:
                                    @if ( strlen($declaration['isolation_address']) > 0 )
                                        &nbsp;<strong>{!! $declaration['isolation_address'] !!}</strong>,
                                    @else
                                        ____________________________________________________________________,
                                    @endif
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I will travel by') !!}:&nbsp;
                                    @if (strlen($declaration['vehicle_registration_no']) > 0)
                                        {{ __('app.' . $declaration['vehicle_type']) }}
                                        <strong>{{ $declaration['vehicle_registration_no'] }}</strong>
                                    @else
                                        _______________________________
                                    @endif
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I agree to the use of my personal data') !!}:
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I acknowledge the provisions') !!}.
                                </p>
                                <p class="no-margin-bottom">
                                    {!! __('app.During my stay in Romania I can be reached at') !!}:<br />
                                    {{ __('app.Table Phone') }}: <strong>{{ $declaration['phone'] }}</strong><br />
                                    {{ __('app.Table E-mail') }}: <strong>{{ $declaration['email'] }}</strong>
                                </p>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>{{ __('app.Signature') }}</strong>:</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            @if (strlen($signature) > 0)
                                                <img src="{{ $signature }}" alt="" title="" />
                                            @else
                                                ________________________________
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td><strong>{{ __('app.Date and place') }}</strong>:</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {{ $declaration['current_date'] }},&nbsp;
                                            @if (strlen($declaration['border']) > 0)
                                                {{ $declaration['border'] }}
                                            @else
                                                ________________________________
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </section>
                    @if ( Auth::user()->username !== env('ADMIN_USER') )
                    <script type="text/javascript">
                        $(document).ready( function () {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $('#print-declaration').click( function (e) {
                                let declarationCode     = "{{ $declaration['code'] }}";
                                let isDspBeforeBorder   = {{ ($declaration['is_dsp_before_border']) }};
                                let signature           = '{{ $signature }}';
                                let qrcode              = '{{ $qrCode }}';
                                let dataPdf             = {!! $pdfData !!};
                                let doc                 = new Document();

                                e.preventDefault();
                                $.ajax({
                                    type:'POST',
                                    url:"{{ route('register-declaration') }}",
                                    data:{
                                        code:declarationCode,
                                        measure:$("input[name='dsp-measure']:checked").val(),
                                        is_dsp: isDspBeforeBorder
                                    },
                                    success:function(data){
                                        let dspMeasure = data.measure;
                                        switch(dspMeasure) {
                                            case 'hospital':
                                                dataPdf.measure.hospital = true;
                                                break;
                                            case 'isolation':
                                                dataPdf.measure.isolation = true;
                                                break;
                                            case 'quarantine':
                                                dataPdf.measure.quarantine = true;
                                                break;
                                        }
                                        if($.isEmptyObject(data.error)){
                                            $.ajax({
                                                type:'POST',
                                                url:"{{ route('refresh-list') }}",
                                                data:{refresh:true},
                                                success:function(data){
                                                    doc.preview(dataPdf, signature, qrcode);
                                                }
                                            });
                                        }else{
                                            printAlertMsg(data.error, 'danger');
                                        }
                                        setTimeout(function () {
                                            $('.ajax-msg').removeClass('alert-danger alert-success');
                                            if ($('.ajax-msg').is(':visible')){
                                                $('.ajax-msg').fadeOut();
                                            }
                                        }, {{ env('MESSAGE_VIEW_TIME') * 1000 }})
                                    }
                                });
                            });

                            function printAlertMsg (msg, type) {
                                $('.ajax-msg').find('span#ajax-text-message').html(msg);
                                $('.ajax-msg').addClass('alert-'+type);
                                $('.ajax-msg').show();
                            }

                            $('.alert button').click(function(e){
                                e.preventDefault();
                                $(this).parent().hide().removeClass('alert-danger alert-success');
                                return false;
                            });
                        });
                    </script>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
