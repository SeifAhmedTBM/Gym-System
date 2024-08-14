<div>
    <div class="form-group">
        <div class="row">
            <div class="col-md-6">
                <label for="search_lead">{{ trans('global.search_leads') }}</label>
                <input wire:model.live="lead_name" type="text" name="search_lead" id="search_lead" class="form-control">
            </div>
            <input type="hidden" name="lead_id" id="lead_id">
        </div>
    </div>
    @foreach ($leads as $lead)
    <div class="row leadsDiv form-group">
        <div class="col-md-6">
            <div onclick="selectLead(this, '{{ $lead->name }}')" data-id="{{ $lead->id }}" class="bg-light p-3 rounded my-1" role="button">
                <span class="d-block">
                    <span class="text-primary font-weight-bold"><i class="fa fa-user-circle fa-lg"></i> {{ $lead->name }}</span> &nbsp; , &nbsp;
                    <span class="text-dark font-weight-bold">
                        <i class="fa fa-mobile fa-lg"></i> {{ $lead->phone }}
                    </span>
                </span>
            </div>
        </div>
    </div>
    @endforeach
</div>
