@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <h5>{{ trans('cruds.member_suggestion.title') }}</h5>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover zero-configuration">
                        <tr>
                            <thead>
                                <th>#</th>
                                <th>{{ trans('cruds.member.title_singular') }}</th>
                                <th>{{ trans('global.description') }}</th>
                            </thead>
                        </tr>
                        <tr>
                            <tbody>
                                @foreach ($suggestions as $suggestion)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <b class="d-block">{{ $suggestion->member->name ?? '' }}</b>
                                            <span class="d-block">{{ $suggestion->member->phone ?? '' }}</span>
                                            <span class="d-block">{{ $suggestion->member->user->email ?? '' }}</span>
                                        </td>
                                        <td>{{ $suggestion->description ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection