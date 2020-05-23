@extends('layouts.app')

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
            <div class="col-md-6">
                <form>
                    @csrf
                    <div class="input-group input-group-lg" id="search-declaration">
                        <input id="code" name="code" type="text" class="form-control"
                               placeholder="{{ __('app.Declaration Code') }}"
                               aria-label="Declaration code"/>
                        <div class="input-group-append">
                            <button class="btn btn-outline-dark btn-top" type="button">
                                {{ __('app.Search') }}</button>
                        </div>
                    </div>
                </form>
            </div>

            @if (Auth::user()->username === env('ADMIN_USER'))
            <div class="col-md-12">
                <div class="card">
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
                        @if (!empty($declarations))
                                {{ $declarations->links() }}
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
                                        <td><a href="{{ $declaration['url'] }}">{{ __('app.View Details') }}</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                                {{ $declarations->links() }}
                        @endif

                    </div>
                </div>
            </div>
            @else
            {{-- TODO dsp user simplu--}}
            @endif
        </div>
        <script type="text/javascript">
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#search-declaration button').click(function(e){
                e.preventDefault();
                let code = $('#code').val();
                $.ajax({
                    type:'POST',
                    url:"{{ route('search-declaration') }}",
                    data:{code:code},
                    success:function(data){
                        if($.isEmptyObject(data.error)){
                            console.log(data.success);
                            // window.location.href = "/declaratie/" + data.success;
                        }else{
                            printAlertMsg(data.error, 'danger');
                            setTimeout(function () {
                                $('.ajax-msg').removeClass('alert-danger alert-success');
                                if ($('.ajax-msg').is(':visible')){
                                    $('.ajax-msg').fadeOut();
                                }
                            }, 5000)
                        }
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
        </script>
    </div>
@endsection
