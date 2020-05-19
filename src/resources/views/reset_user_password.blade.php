@extends('layouts.app')

@section('content')
<div class="container">
    @if (session('message'))
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card alert alert-{{ session('type') }} alert-dismissible fade show" role="alert">
                <span>{{ session('message') }}</span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card alert ajax-msg alert-dismissible fade show">
                <span id="ajax-text-message"></span>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    {{ __('auth.Reset Password') }}
                    <div class="float-right">
                        <form method="POST" action="{{ route('reset-all-passwords') }}">
                            @csrf
                            <div class="row">
                                <button type="submit" class="btn btn-danger btn-sm btn-top btn-reset-all">
                                    {{ __('app.Reset All Passwords') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('reset-password') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="user" class="col-md-4 col-form-label text-md-right">{{ __
                            ('app.User') }}</label>

                            <div class="col-md-6">
                                <select id="user" name="user" class="form-control">
                                    <option value="" selected disabled style="display:none">
                                        {{ __('app.User select') }}
                                    </option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">
                                            {{ ucwords(str_replace('-', ' ', trim($user->username))) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('auth.Reset Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function(){
            $('.ajax-loader').css('visibility', 'visible');
        },
        complete: function(){
            $('.ajax-loader').css('visibility', 'hidden');
        }
    });

    $('.btn-reset-all').click(function(e){

        e.preventDefault();
        let userId = $('#user').val();

        $.ajax({
            type:'POST',
            url:"{{ route('reset-all-passwords') }}",
            data:{id:userId},
            success:function(data){
                if($.isEmptyObject(data.error)){
                    printAlertMsg(data.success, 'success');
                }else{
                    printAlertMsg(data.error, 'error');
                }
                setTimeout(function () {
                    if ($('.ajax-msg').is(':visible')){
                        $('.ajax-msg').fadeOut();
                    }
                }, 10000)
            }
        });

        function printAlertMsg (msg, type) {
            $('.ajax-msg').find('span#ajax-text-message').html(msg);
            $('.ajax-msg').addClass('alert-'+type);
            $('.ajax-msg').show();
        }
    });
</script>
@endsection
