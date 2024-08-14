@extends('layouts.admin')
@section('content')
<a href="{{ route('admin.marketing.whatsapp.index') }}" class="btn mb-2 btn-danger">
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
                    <td width="150" class="font-weight-bold">{{ trans('global.numbers') }}</td>
                    <td>
                        @foreach (explode(',', $whatsapp->numbers) as $number)
                        <span class="badge py-2 px-2 badge-primary" style="font-size:12px;">
                            ( +20 ) {{ $number }}
                        </span>
                        @endforeach 
                    </td>
                </tr>
                @if(!is_null($whatsapp->image_id))
                <tr>
                    <td class="font-weight-bold">{{ trans('global.featured_image') }}</td>
                    <td>
                        <img src="{{ $whatsapp->image_id }}" width="100" alt="Whatsapp Campaign Image">
                        <div class="btns mt-3">
                            <a href="{{ $whatsapp->image_id }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fa fa-eye"></i> {{ trans('global.view') }}
                            </a>
                            <a href="{{ $whatsapp->image_id }}" download="" class="btn btn-sm btn-danger">
                                <i class="fa fa-download"></i> {{ trans('global.downloadFile') }}
                            </a>
                        </div>
                    </td>
                </tr>
                @endif
                <tr>
                    <td class="font-weight-bold">{{ trans('global.message') }}</td>
                    <td>
                        {!! $whatsapp->message !!}
                    </td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.sent_by') }}</td>
                    <td>{{ $whatsapp->user->name }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">{{ trans('global.created_at') }}</td>
                    <td>{{ $whatsapp->created_at->toFormattedDateString() }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
