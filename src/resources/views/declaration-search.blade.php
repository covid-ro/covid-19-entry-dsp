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
            <div class="col-md-6">
                <form action="{{ route('search-declaration') }}" method="POST">
                    @csrf
                    <div class="input-group input-group-lg" id="search-declaration">
                        <input id="code"
                               name="code"
                               type="text"
                               class="form-control"
                               placeholder="{{ __('app.Declaration Code') }}"
                               aria-label="Declaration code"
                        />
                        <div class="input-group-append">
                            <button class="btn btn-outline-dark btn-top" type="submit">{{ __('app.Search') }}</button>
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
        </div>

        <div class="col-md-12">
            Declaration PDF content will be here, inside an iframe
        </div>
@endsection
