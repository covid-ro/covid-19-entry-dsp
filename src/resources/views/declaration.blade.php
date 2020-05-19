@extends('layouts.app')

@section('js_scripts')
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.min.js"></script>
    <script type="text/javascript" src="{{ asset('js/document-font-bold.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document-font-normal.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document-trans.js' )}}"></script>
    <script type="text/javascript" src="{{ asset('js/document.js' )}}"></script>
@endsection

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
                    <div class="float-right">
                        <a href="javascript:void(0);" id="print-declaration" class="btn btn-danger btn-sm btn-top"
                           role="button"
                           aria-pressed="true">
                            {{ __('app.Print') }}
                        </a>
                    </div>
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
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    @if ( app()->getLocale() == 'ro' )
                                    <tr>
                                        <td width="20%">{{ __('app.Name in declaration') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['name'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%">{{ __('app.Surname') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['surname'] }}</strong></td>
                                    </tr>
                                    @else
                                    <tr>
                                        <td width="20%">{{ __('app.Surname') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['surname'] }}</strong></td>
                                    </tr>
                                    <tr>
                                        <td width="20%">{{ __('app.Name in declaration') }}:</td>
                                        <td><strong class="text-uppercase">{{ $declaration['name'] }}</strong></td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td width="20%">{{ __('app.Sex') }}:</td>
                                        <td>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-male"
                                                       onclick="return false;"{{ $declaration['sex'] === 'M' ? 'checked' : ''
                                                }}>
                                                <label class="form-check-label" for="sex-male"><strong>M</strong></label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="checkbox" id="sex-female"
                                                       onclick="return false;" {{ $declaration['sex'] === 'F' ? 'checked' : ''
                                                }}>
                                                <label class="form-check-label" for="sex-female"><strong>F</strong></label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">{{ __('app.Travelling from country') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_country'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.City') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_city'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date') }}:</td>
                                        <td>
                                            <strong class="text-uppercase">
                                                {{ $declaration['travelling_from_date'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 text-left">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td width="40%">
                                            @if ( $declaration['document_type'] == 'passport')
                                            {{ __('app.Passport') }} /
                                            <span style="text-decoration: line-through;">{{ __('app.ID') }}:</span>
                                            @else
                                            <span style="text-decoration: line-through;">
                                                {{ __('app.Passport')}}
                                            </span> / {{ __('app.ID') }}:
                                            @endif
                                        </td>
                                        <td>
                                            {{ __('app.Series')}}:
                                            <strong class="text-uppercase">
                                                {{ $declaration['document_series'] }}
                                            </strong>
                                            {{ __('app.No')}}:
                                            <strong class="text-uppercase">
                                                {{ $declaration['document_number'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date of birth') }}</td>
                                        <td>
                                            <strong>
                                                {{ $declaration['birth_date'] }}
                                            </strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td width="40%">{{ __('app.Date of arrival') }}</td>
                                        @if (is_null($declaration['border_validated_at']))
                                        <td>___________________</td>
                                        @else
                                        <td>
                                            <strong>
                                                {{ $declaration['border_validated_at'] }}
                                            </strong>
                                        </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6 text-right">
                                <img src="{{ $qrCode }}" alt="" title="" />
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom">
                                    <strong>{{ __('app.I estimate that I will be staying in Romania') }}:</strong>
                                </p>
                                <table class="table table-bordered border border-dark">
                                    <thead>
                                        <tr>
                                            <th class="text-center align-middle" width="50px" scope="col">
                                                {!! __('app.Table No') !!}
                                            </th>
                                            <th class="text-center align-middle" scope="col">{!! __('app.Table Location (town/city)') !!}</th>
                                            <th class="text-center align-middle" scope="col">{!! __('app.Table Date of arrival') !!}</th>
                                            <th class="text-center align-middle" scope="col">{!! __('app.Table Date of departure') !!}</th>
                                            <th class="text-center align-middle" scope="col">{{ __('app.Table Complete address') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if( count($declaration['isolation_addresses']) > 0)
                                        @foreach ($declaration['isolation_addresses'] as $address)
                                        <tr>
                                            <td class="text-center align-middle">{{ $loop->iteration }}</td>
                                            <td>{{ $address['city'] }}, {{ $address['county'] }}</td>
                                            <td>{{ $address['city_arrival_date'] }}</td>
                                            <td>{{ $address['city_departure_date'] }}</td>
                                            <td>{{ $address['city_full_address'] }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        @for ($i = 1; $i <= 3; $i++)
                                        <tr>
                                            @for ($j = 1; $j <= 5; $j++)
                                            <td style="height: 2.5rem;"></td>
                                            @endfor
                                        </tr>
                                        @endfor
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong>{{ __('app.During my stay') }}:</strong></p>
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td>
                                            {{ __('app.Table Phone') }}: <strong>{{ $declaration['phone'] }}</strong>
                                        </td>
                                        <td>
                                            {{ __('app.Table E-mail') }}: <strong>{{ $declaration['email'] }}</strong>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong>{{ __('app.Have you lived in') }}:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ $declaration['q_visited'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ !$declaration['q_visited'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer No') }}</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong>{{ __('app.Have you come in direct') }}:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ $declaration['q_contacted'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ !$declaration['q_contacted'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer No') }}</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong>{{ __('app.Have you been hospitalized') }}:</strong></p>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ $declaration['q_hospitalized'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox"
                                           onclick="return false;" {{ !$declaration['q_hospitalized'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        <strong>{{ __('app.Answer No') }}</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p class="no-margin-bottom"><strong>{{ __('app.Have you had one') }}:</strong></p>
                                <table class="table table-bordered border border-dark">
                                    <tbody>
                                        <tr>
                                            <td><strong class="table-padding-left">{{ __('app.Fever') }}</strong></td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ $declaration['fever'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ !$declaration['fever'] ? 'checked' : ''}}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer No') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    {{ __('app.Difficulty in swallowing') }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ $declaration['swallow'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ !$declaration['swallow'] ? 'checked' : ''}}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer No') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    {{ __('app.Difficulty in breathing') }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ $declaration['breath'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ !$declaration['breath'] ? 'checked' : ''}}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer No') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <strong class="table-padding-left">
                                                    {{ __('app.Intense coughing') }}
                                                </strong>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ $declaration['cough'] ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer Yes') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox"
                                                           onclick="return false;"
                                                        {{ !$declaration['cough'] ? 'checked' : ''}}>
                                                    <label class="form-check-label">
                                                        <strong>{{ __('app.Answer No') }}</strong>
                                                    </label>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
                            <div class="col-md-12 text-justify">
                                <p>
                                    {!! __('app.Important notice and agreement') !!}
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I am aware that the refusal') !!}
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.Acknowledging the provisions') !!}&nbsp;
                                    @if (strlen($declaration['itinerary']) > 0)
                                       {!! $declaration['itinerary'] !!}
                                    @else
                                        ____________________________________________________
                                    @endif
                                    &nbsp;{!! __('app.and that I will follow') !!}&nbsp;
                                    @if (strlen($declaration['border']) > 0)
                                        <strong>{{ $declaration['border'] }}</strong>.
                                    @else
                                        ____________________________________________________ {{ __('app.(name)') }}.
                                    @endif
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {!! __('app.I declare on my own responsibility') !!}:&nbsp;
                                    @if (strlen($declaration['travel_route']) > 0)
                                        <strong>{!! $declaration['travel_route'] !!}</strong>,
                                    @else
                                        ____________________________________________________________________,
                                    @endif
                                    &nbsp;{!! __('app.for self-isolation or quarantine') !!}:&nbsp;
                                    @if (strlen($declaration['vehicle_registration_no']) > 0)
                                        {{ __('app.' . $declaration['vehicle_type']) }}
                                        <strong>{{ $declaration['vehicle_registration_no'] }}</strong>
                                    @else
                                        _____________________ {{ __('app.indicate car or ambulance') }}
                                    @endif
                                    &nbsp;, {{ __('app.following the route') }}:<br />
                                    __________________________________________________________________________________ .
                                </p>
                                <p class="no-margin-bottom">
                                    <span class="bullet-padding-right">&#8226;</span>
                                    {{ __('app.I agree that the provided information') }}.
                                </p>
                            </div>
                        </div>
                        <hr class="sub-section">
                        <div class="row">
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
                                    <tr>
                                        <td class="legend-top-margin">
                                            <small>
                                                {!! __('app.Legend for DSP staff') !!}
                                            </small>
                                        </td>
                                    </tr>
                                </table>
                            </div>
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
                        </div>
                    </section>
                    <script type="text/javascript">
                        $(document).ready( function () {
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                }
                            });

                            $('#print-declaration').click( function (e) {
                                let declarationCode = "{{ $declaration['code'] }}";
                                let signature = '{{ $signature }}';
                                let qrcode = '{{ $qrCode }}';
                                let dataPdf = {!! $pdfData !!};
                                let doc = new Document();

                                e.preventDefault();
                                $.ajax({
                                    type:'POST',
                                    url:"{{ route('register-declaration') }}",
                                    data:{code:declarationCode},
                                    success:function(data){
                                        if($.isEmptyObject(data.error)){
                                            $.ajax({
                                                type:'POST',
                                                url:"{{ route('refresh-list') }}",
                                                data:{refresh:true},
                                                success:function(data){
                                                    doc.download(dataPdf, signature, qrcode);
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
                                        }, 5000)
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
