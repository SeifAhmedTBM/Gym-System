@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header font-weight-bold">
        <h5><i class="fa fa-image"></i> {{ trans('global.campaigns') }}</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <button data-toggle="modal" data-target="#addCampaignModal" class="btn btn-primary float-right mb-2" type="button">
                <i class="fa fa-plus-circle"></i> {{ trans('global.create') }}
            </button>
            <table class="table table-bordered table-outline text-center table-striped">
                <thead class="thead-light">
                    <tr>
                        <th>#</th>
                        <th>{{ trans('cruds.news.fields.image') }}</th>
                        <th>{{ trans('global.text') }}</th>
                        <th>{{ trans('cruds.bonu.fields.created_by') }}</th>
                        <th>{{ trans('global.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($campaigns as $campaign)
                        <tr>
                            <td class="font-weight-bold">{{ $loop->iteration }}</td>
                            <td>
                                @if ($campaign->image != NULL)
                                    <img src="{{ asset('campaigns/'. $campaign->image) }}" width="100" alt="Campaign Image">
                                @else
                                    {{ trans('global.no_data_available') }}
                                @endif
                            </td>
                            <td class="font-weight-bold">
                                {{ $campaign->text != NULL ? $campaign->text : trans('global.no_data_available') }}
                            </td>
                            <td class="font-weight-bold">{{ $campaign->user->name }}</td>
                            <td>
                                <button class="btn btn-sm btn-success">
                                    <i class="fa fa-edit"></i> {{ trans('global.edit') }}
                                </button>
                                <button type="button" data-toggle="modal" data-target="#deleteModal" onclick="deleteModel(this)" data-id="{{ $campaign->id }}" class="btn btn-sm btn-danger">
                                    <i class="fa fa-trash"></i> {{ trans('global.delete') }}
                                </button>
                            </td>
                        </tr>
                    @empty
                    <tr>
                        <td colspan="5">{{ trans('global.no_data_available') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@include('admin.campaigns.modals')
@endsection

@section('scripts')
    <script>
        function deleteModel(button) {
            let campaignId = $(button).data('id');
            let url = "{{ route('admin.marketing.campaigns.destroy', ':id') }}";
            url = url.replace(':id', campaignId);
            $('#deleteForm').attr('action', url);
        }
    </script>
@endsection