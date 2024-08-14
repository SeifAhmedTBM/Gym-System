@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.marketing.mails.index') }}" class="btn mb-2 btn-danger">
    <i class="fa fa-arrow-circle-left"></i> {{ trans('global.back') }}
</a>
<div class="card">
    <div class="card-header">
        <h5><i class="fa fa-eye"></i> {{ trans('global.show') }} {{ trans('global.campaign') }}</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <tbody>
                <tr>
                    <td width="150" class="font-weight-bold">{{ trans('global.users') }}</td>
                    <td>
                        @foreach (explode(',', $mailCamp->emails) as $email)
                        <span class="badge py-2 px-2 badge-primary" style="font-size:12px;">
                            {{ $email }}
                        </span>
                        @endforeach 
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.subject') }}</td>
                    <td>
                        {{ $mailCamp->subject }}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.message') }}</td>
                    <td>
                        {!! $mailCamp->message !!}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.sent_by') }}</td>
                    <td>{{ $mailCamp->user->name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.created_at') }}</td>
                    <td>{{ $mailCamp->created_at->toFormattedDateString() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
