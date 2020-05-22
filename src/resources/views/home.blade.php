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
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        {{ __('app.Dashboard') }}
                        <div class="float-right">
                            <a href="javascript:void(0);" id="refresh-list" class="btn btn-secondary btn-sm"
                               role="button"
                               aria-pressed="true">
                                {{ __('app.Refresh declarations list') }}
                            </a>
                        </div>
                        @if (Auth::user()->username !== env('ADMIN_USER'))
                            <div class="float-right">
                                <form>
                                    @csrf
                                    <div class="input-group input-group-sm" id="search-declaration">
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
                        @endif
                    </div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (!empty($declarations))
                            <table class="table table-striped table-bordered" id="declaratii">
                                <thead>
                                <tr>
                                    <th class="text-center">{{ __('app.Code') }}</th>
                                    <th class="text-center">{{ __('app.Name') }}</th>
                                    <th class="text-center">{{ __('app.Dsp status') }}</th>
                                    <th class="text-center">{{ __('app.Border') }}</th>
                                    <th class="text-center">{{ __('app.Url') }}</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($declarations as $declaration)
                                    <tr>
                                        <td>{{ $declaration['code'] }}</td>
                                        <td>{{ $declaration['name'] }}</td>
                                        <td>{{ $declaration['dsp_status'] }}</td>
                                        <td>{{ $declaration['checkpoint'] }}</td>
                                        <td><a href="{{ $declaration['url'] }}">{{ __('app.View Details') }}</a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
