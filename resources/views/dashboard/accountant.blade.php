
<h4><i class="fa fa-money"></i> {{ trans('cruds.finance.title') }}</h4>
<div class="row py-2">
    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.invoices.index', ['created_at' => ['from' => date('Y-m-d')]]) }}" class="text-decoration-none text-white">
            <div class="card">
                <div class="card-body bg-primary text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $daily_income }}</h5>
                    <h5><i class="fa fa-money"></i>
                        {{ trans('global.daily_income') }}</h5>
                </div>
            </div>
        </a>
    </div>
    <!-- /.col-->

    <div class="col-sm-6 col-lg-4">
        <a href="{{ route('admin.expenses.index', ['created_at' => ['from' => date('Y-m-d')]]) }}" class="text-decoration-none text-white">
            <div class="card">
                <div class="card-body bg-danger text-white text-center">
                    <h5 class="fs-4 fw-semibold">{{ $daily_expenses }}</h5>
                    <h5><i class="fa fa-money"></i>
                        {{ trans('global.daily_expenses') }}</h5>
                </div>
            </div>
        </a>
    </div>
    <!-- /.col-->

    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <div class="card-body bg-success text-white text-center">
                <h5 class="fs-4 fw-semibold">{{ $daily_net }}</h5>
                <h5><i class="fa fa-money"></i>
                    {{ trans('global.daily_total') }}</h5>
            </div>
        </div>
    </div>
    <!-- /.col-->
</div>

<div class="row">
    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <div class="card-body bg-primary text-white text-center">
                <h5 class="fs-4 fw-semibold">{{ $monthly_income }} EGP </h5>
                <h5><i class="fa fa-dollar"></i>
                    {{ trans('global.monthly_income') }} </h5>
            </div>
        </div>
    </div>
    <!-- /.col-->

    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <div class="card-body bg-danger text-white text-center">
                <h5 class="fs-4 fw-semibold">{{ $monthly_expenses }}</h5>
                <h5><i class="fa fa-dollar"></i>
                    {{ trans('global.monthly_expenses') }}</h5>
            </div>
        </div>
    </div>
    <!-- /.col-->

    <div class="col-sm-6 col-lg-4">
        <div class="card ">
            <div class="card-body bg-success text-white text-center">
                <h5 class="fs-4 fw-semibold">{{ $monthly_net }}</h5>
                <h5><i class="fa fa-dollar"></i>
                    {{ trans('global.monthly_total') }}</h5>
            </div>
        </div>
    </div>
    <!-- /.col-->
</div>

<hr>

