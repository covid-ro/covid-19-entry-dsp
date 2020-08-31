@extends('layouts.app')

@section('js_scripts')
    <script src="https://unpkg.com/jspdf@1.5.3/dist/jspdf.min.js"></script>
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
                <button type="button" class="close" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form>
                    @csrf
                    <div class="input-group input-group-lg" id="search-declaration">
                        <input id="code" name="code" type="text" class="form-control"
                               placeholder="{{ __('app.Declaration Code') }}"
                               aria-label="Declaration code"/>
                        <div class="input-group-append">
                            <button class="btn btn-outline-dark btn-top" type="submit">
                                {{ __('app.Search') }}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="card alert ajax-msg alert-dismissible fade show">
                    <span id="ajax-text-message"></span>
                    <button type="button" class="close" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card" id="search-results">
                    <div class="card-header">
                        {{ __('app.Search results') }}
                    </div>

                    <div class="card-body">
                        <table class="table table-striped table-bordered" id="search-results-table">
                            <thead>
                            <tr>
                                <th class="text-center">{{ __('app.Code') }}</th>
                                <th class="text-center">{{ __('app.Name') }}</th>
                                <th class="text-center">{{ __('app.CNP') }}</th>
                                <th class="text-center">{{ __('app.Border validated') }}</th>
                                <th class="text-center">{{ __('app.Dsp validated') }}</th>
                                <th class="text-center">{{ __('app.Phone') }}</th>
                                <th>{{ __('app.Details') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ( Auth::user()->username === env('ADMIN_USER'))
                @if (!empty($declarations) && count($declarations) > 0 )
                    <div class="col-md-12">
                        <div class="card" id="search-results-content">
                            <div class="card-header">
                                {{ __('app.Dashboard') }}
                                <div class="float-right">
                                    <a href="{{ url()->current() }}" id="refresh-list" class="btn btn-secondary btn-sm"
                                       role="button"
                                       aria-pressed="true">
                                        {{ __('app.Refresh declarations list') }}
                                    </a>
                                </div>
                            </div>

                            <div class="card-body">
                                @if (session('status'))
                                    <div class="alert alert-success" role="alert">
                                        {{ session('status') }}
                                    </div>
                                @endif
                                <div class="row">
                                    <div class="col-md-6">
                                        {{ $declarations->links() }}
                                    </div>
                                    <div class="col-md-6">
                                        <div class="float-right">
                                            <form method="POST" action="{{ route('change-pagination') }}">
                                                @csrf
                                                <div class="form-group row" id="change-elements-per-page">
                                                    <select id="per-page" name="per-page"
                                                            class="form-control form-control"
                                                            onchange="this.form.submit()">
                                                        @foreach( $perPageValues as $value )
                                                            <option value="{{ $value }}"
                                                                {{ $perPage == $value ? ' selected="selected"' : ''}}>
                                                                {{ $value }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <table class="table table-striped table-bordered" id="declaratii">
                                    <thead>
                                    <tr>
                                        <th class="text-center">{{ __('app.Code') }}</th>
                                        <th class="text-center">{{ __('app.Name') }}</th>
                                        <th class="text-center">{{ __('app.CNP') }}</th>
                                        <th class="text-center">{{ __('app.Border validated') }}</th>
                                        <th class="text-center">{{ __('app.Dsp validated') }}</th>
                                        <th class="text-center">{{ __('app.Phone') }}</th>
                                        <th>{{ __('app.Details') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($declarations as $declaration)
                                        <tr>
                                            <td>{{ $declaration['code'] }}</td>
                                            <td>{{ $declaration['name'] }}</td>
                                            <td>{{ $declaration['cnp'] }}</td>
                                            <td>{{ $declaration['border_validated_at'] ?? '-' }}</td>
                                            <td>{{ $declaration['dsp_validated_at'] ?? '-' }}</td>
                                            <td>{{ $declaration['phone'] }}</td>
                                            <td><a href="#" class="view-declaration-details"
                                                   data-declaration-code="{{ $declaration['code'] }}">{{ __('app.View Details') }}</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{ $declarations->links() }}
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <div class="col-md-12 d-none" id="declaration-iframe-pdf" style="height: 675px"></div>
            <script type="text/javascript">
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#search-declaration button').click(function (e) {
                    e.preventDefault();
                    $('#declaration-iframe-pdf').html('').addClass('d-none');

                    let code = $('#code').val();

                    if (0 === code.length) {
                        window.location.href = '/';
                        return;
                    }

                    let searchResultsCard = $('#search-results');
                    $('#search-results-table tbody').html('');
                    $.ajax({
                        type: 'POST',
                        url: "{{ route('search-declaration') }}",
                        data: {code: code},
                        success: function (data) {
                            if ($.isEmptyObject(data.error)) {
                                if (data.success.length > 1) {
                                    $('#search-results-content').hide();
                                    $('#search-results').hide();

                                    searchResultsCard.show();
                                    $.each(data.success, function (index, value) {
                                        let html = '<tr>';
                                        html += '<td>' + value.code + '</td>';
                                        html += '<td>' + value.name + ' ' + value.surname + '</td>';
                                        html += '<td>' + value.cnp + '</td>';
                                        html += '<td>' +
                                            ((value.border_validated_at == null) ? '-' : value.border_validated_at) +
                                            '</td>';
                                        html += '<td>' +
                                            ((value.dsp_validated_at == null) ? '-' : value.dsp_validated_at) +
                                            '</td>';
                                        html += '<td>' + value.phone + '</td>';
                                        html += '<td>' +
                                            '<a href="#" class="view-declaration-details" data-declaration-code="' + value.code + '">' +
                                            "{{ __('app.View Details') }}" +
                                            '</a>' +
                                            '</td>';
                                        html += '</tr>';
                                        $('#search-results-table tbody').append(html);
                                    });
                                } else {
                                    showiFramedDeclaration(data.success[0].code);
                                }
                            } else {
                                searchResultsCard.hide();
                                $('#search-results-table tbody').html('');
                                printAlertMsg(data.error, 'danger');
                                setTimeout(function () {
                                    $('.ajax-msg').removeClass('alert-danger alert-success');
                                    if ($('.ajax-msg').is(':visible')) {
                                        $('.ajax-msg').fadeOut();
                                    }
                                }, {{ env('MESSAGE_VIEW_TIME') * 1000 }})
                            }
                        }
                    });
                });

                $(document).on('click', '.view-declaration-details', function (e) {
                    e.preventDefault();
                    showiFramedDeclaration($(this).data('declaration-code'));
                });

                function showiFramedDeclaration(code) {
                    $('#search-results-content').hide();
                    $('#search-results').hide();

                    $('#code').focus();
                    $('#code').val('');

                    $.ajax({
                        type: 'GET',
                        url: "/declaratie/" + code,
                        data: {refresh: true},
                        success: function (data) {
                            new Document().pdfToIframe($.parseJSON(data.pdfData), data.signature, data.qrCode);

                            $('#declaration-iframe-pdf').removeClass('d-none');

                            $.ajax({
                                type: 'POST',
                                url: "{{ route('register-declaration') }}",
                                data: {
                                    code: data.declaration.code,
                                    // measure: 'isolation',
                                    is_dsp: data.declaration.is_dsp_before_border
                                }
                            });
                        }
                    });
                }

                function printAlertMsg(msg, type) {
                    $('.ajax-msg').find('span#ajax-text-message').html(msg);
                    $('.ajax-msg').addClass('alert-' + type);
                    $('.ajax-msg').show();
                }

                $('.alert button').click(function (e) {
                    e.preventDefault();
                    $(this).parent().hide().removeClass('alert-danger alert-success');
                    return false;
                });
            </script>
        </div>
@endsection
