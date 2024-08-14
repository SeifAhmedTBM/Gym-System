<div class="row form-group">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4>Member / Lead </h4>
            </div>
            <form action="{{ route('admin.searchMember') }}" method="post">
                @csrf
                <div class="card-body">
                    <div class="alert alert-warning text-center">
                        <h5><i class="fa fa-exclamation-circle"></i> You can search by Name , phone or member code</h5>
                    </div>
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg" name="search" placeholder="Enter Member / Lead Name Or member code ..">
                        <button class="btn btn-outline-primary" type="submit">{{ trans('global.search') }}</button>
                      </div>
                    
                    <p class="text-primary py-2"><i class="fa fa-exclamation-circle"></i> Fill field and click enter</p>
                </div>
            </form>
        </div>
    </div>
</div>