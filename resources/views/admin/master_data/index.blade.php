@extends('layouts.admin')
@section('content')
    <div class="row">
        @can('status_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.statuses.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.status.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-smile fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan
        @can('source_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.sources.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.source.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-cogs fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('address_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.addresses.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.address.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-map-marker fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('expenses_category_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.expenses-categories.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.expensesCategory.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-list fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('service_type_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.service-types.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.serviceType.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-server fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('service_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.services.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.service.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-dumbbell fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('service_option_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.service-options.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.serviceOption.title_singular') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-cog fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan


        @can('pricelist_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.pricelists.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.pricelist.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-money fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('asset_type_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.asset-types.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.assetType.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-list fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('asset_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.assets.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.asset.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-bicycle fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('maintenance_vendor_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.maintenance-vendors.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.maintenanceVendor.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-user-ninja fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan


        @can('master_card_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.master-cards.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.masterCard.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa-fw fas fa-address-card fa-5x">

                            </i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('refund_reason_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.refund-reasons.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.refundReason.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-question fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('timeslot_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.timeslots.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.timeslot.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-clock fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('session_list_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.session-lists.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.sessionList.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-stream fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('schedule_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.schedules.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.schedule.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-table fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @can('sales_tier_access')
            <div class="col-md-3">
                <a class="text-decoration-none text-dark" href="{{ route('admin.sales-tiers.index') }}">
                    <div class="card text-center shadow-sm">
                        <div class="card-header font-weight-bold">
                            <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.salesTier.title') }}</h5>
                        </div>
                        <div class="card-body text-center">
                            <i class="fas fa-ellipsis-h fa-5x"></i>
                        </div>
                    </div>
                </a>
            </div>
        @endcan

        @if (config('domains')[config('app.url')]['trainer_services_option'] == true)
            @can('trainer_services_access')
                <div class="col-md-3">
                    <a class="text-decoration-none text-dark" href="{{ route('admin.trainer-services.index') }}">
                        <div class="card text-center shadow-sm">
                            <div class="card-header font-weight-bold">
                                <h5 class="p-0 m-0 font-weight-bold">{{ trans('global.trainer_services') }}</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-5x"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
        @endif


        @if (config('domains')[config('app.url')]['sports_option'] == true)
            @can('sports_access')
                <div class="col-md-3">
                    <a class="text-decoration-none text-dark" href="{{ route('admin.sports.index') }}">
                        <div class="card text-center shadow-sm">
                            <div class="card-header font-weight-bold">
                                <h5 class="p-0 m-0 font-weight-bold">{{ trans('global.sports') }}</h5>
                            </div>
                            <div class="card-body text-center">
                                <i class="fas fa-list fa-5x"></i>
                            </div>
                        </div>
                    </a>
                </div>
            @endcan
        @endif

        {{-- <div class="col-md-3">
        <a class="text-decoration-none text-dark" href="{{ route('admin.member-statuses.index') }}">
            <div class="card text-center shadow-sm">
                <div class="card-header font-weight-bold">
                    <h5 class="p-0 m-0 font-weight-bold">{{ trans('cruds.memberStatus.title') }}</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fa fa-users fa-5x"></i> 
                </div>
            </div>
        </a>
    </div> --}}

    </div>
@endsection
