<div class="dropdown">
    <a class="btn btn-primary dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown"
        aria-expanded="false">
        {{ trans('global.action') }}
    </a>

    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        @if (request()->route()->getName() == 'admin.tasks.my-tasks')
            @if ($row->status != 'done')
                @can('task_action')
                    <a href="{{ route('admin.tasks.done-tasks', $row->id) }}" class="dropdown-item">
                        <i class="fas fa-exchange-alt"></i> &nbsp; Done
                    </a>
                @endcan
                @can('task_action')
                    <a href="{{ route('admin.tasks.in-progress-tasks', $row->id) }}" class="dropdown-item">
                        <i class="fas fa-exchange-alt"></i> &nbsp; In Progress
                    </a>
                @endcan
            @endif
        @endif

        @if (request()->route()->getName() == 'admin.tasks.index')
            @if ($row->status == 'done' && $row->supervisor_id == auth()->id() || $row->status == 'done' && $row->supervisor_id == NULL || $row->status == 'done' && auth()->user()->roles[0]->title == 'Super Admin' || $row->status == 'done' && auth()->user()->roles[0]->title == 'Admin')
                <form action="{{ route('admin.tasks.confirm-task', $row->id) }}" method="POST"
                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <button type="submit" class="dropdown-item">
                            <i class="fa fa-check-circle"></i> &nbsp; {{ trans('global.confirm') }}
                    </button>
                </form>
            @endif
        @endif

        @if (request()->route()->getName() == 'admin.leads.index')
            @can('transfer_to_member')
                <a href="{{ route('admin.member.transfer', $row->id) }}" class="dropdown-item">
                    <i class="fas fa-exchange-alt"></i> &nbsp; {{ trans('global.transfer_to_member') }}
                </a>
            @endcan


            {{-- @can('take_action') --}}
            {{-- <button type="button" data-toggle="modal" data-target="#leadAction"
                        onclick="leadAction({{ $row->id }})" class="dropdown-item"><i class="fa fa-phone"></i>
                        &nbsp; {{ trans('global.add_reminder') }}
                </button> --}}
            {{-- @endcan --}}

            {{-- @can('lead_add_note') --}}
            <a href="{{ route('admin.note.create', $row->id) }}" class="dropdown-item">
                <i class="fas fa-plus"></i> &nbsp; {{ trans('cruds.lead.fields.notes') }}
            </a>
            {{-- @endcan --}}
        @endif

        @if (request()->route()->getName() == 'admin.services.index')
            <a href="{{ route('admin.service.pricelists', $row->id) }}" class="dropdown-item">
                <i class="fas fa-file"></i> &nbsp; {{ trans('cruds.pricelist.title') }}
            </a>
        @endif

        @if (request()->route()->getName() == 'admin.members.index')
            @can('add_membership')
                <a href="{{ route('admin.member.addMembership', $row->id) }}" class="dropdown-item"><i
                        class="fa fa-plus-circle"></i> &nbsp; {{ trans('global.add') }}
                    {{ trans('cruds.membership.title_singular') }}</a>
            @endcan

            {{-- @can('member_add_note') --}}
            <a href="{{ route('admin.note.create', $row->id) }}" class="dropdown-item">
                <i class="fas fa-plus"></i> &nbsp; {{ trans('cruds.lead.fields.notes') }}
            </a>
            {{-- @endcan --}}

            @can('take_action')
                <button type="button" data-toggle="modal" data-target="#takeMemberAction"
                    onclick="takeMemberAction({{ $row->id }})" class="dropdown-item"><i class="fa fa-phone"></i>
                    &nbsp; {{ trans('global.add_reminder') }}</button>
            @endcan

            <li>
                <a href="javascript:;" data-toggle="modal" data-target="#transfer_to_branch" class="dropdown-item" onclick="transfer_to_branch({{ $row->id }})">
                    <i class="fa fa-exchange"></i> Transfer to Branch
                </a>
            </li>

            @can('take_action')
                <button type="button" data-toggle="modal" data-target="#sendMessage"
                    onclick="sendMessage({{ $row->id }})" class="dropdown-item"><i class="fa fa-paper-plane"></i>
                    &nbsp; {{ trans('global.send_sms') }}</button>
            @endcan
            @can('edit_card_number')
                <a class="dropdown-item" href="{{ route('admin.cardNumber.edit', $row->id) }}">
                    <i class="fa fa-edit"></i> &nbsp;
                    {{ trans('global.edit') . ' ' . trans('cruds.member.fields.card_number') }}
                </a>
            @endcan
        @endif

        @if (
            (request()->route()->getName() == 'admin.invoices.index' &&
                $row->status == 'partial') ||
                request()->route()->getName() == 'admin.invoices.partial')
            <a href="{{ route('admin.invoice.payment', $row->id) }}" class="dropdown-item">
                <i class="fa fa-plus-circle"></i> &nbsp; {{ trans('cruds.payment.title_singular') }}</a>
        @endif

        @if (request()->route()->getName() == 'admin.sales-tiers.index')
            <a href="javascript:void(0)" data-toggle="modal"
                data-get="{{ route('admin.sales-tier.get_details', $row->id) }}"
                data-target="#transferToNextMonthModal"
                data-route="{{ route('admin.sales-tiers.transfer', $row->id) }}" onclick="getSalesTierName(this)"
                class="dropdown-item">
                <i class="fa fa-exchange"></i> &nbsp; {{ trans('global.transfer_to_next_month') }}
            </a>
        @endif

            @isset($St)
                @can($viewGate)
                    <a class="dropdown-item" href="/admin/services?St={{$row->id}}">
                        <i class="fa fa-arrow-left"></i> &nbsp; services
                    </a>
                @endcan
            @endisset

        @isset($viewGate)
            @can($viewGate)
                <a class="dropdown-item" href="{{ route('admin.' . $crudRoutePart . '.show', $row->id) }}">
                    <i class="fa fa-eye"></i> &nbsp; {{ trans('global.view') }}
                </a>
            @endcan
        @endisset



        @if (request()->route()->getName() == 'admin.members.index')
            <a class="dropdown-item" data-member-id="{{ $row->id }}" data-toggle="modal"
                data-target="#memberRequestModal" onclick="createMemberRequest(this)" href="javascript:void(0)">
                <i class="fas fa-hand-paper"></i> &nbsp; {{ trans('global.member_request') }}
            </a>
        @endif

        @isset($editGate)
            @can($editGate)
                @switch(request()->route()->getName())
                    @case('admin.freeze-requests.index')
                        @if ($row->status == 'pending')
                            <a class="dropdown-item" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
                                <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                            </a>
                        @endif
                    @break

                    @case('admin.memberships.index')
                        @if ($row->status != 'refunded')
                            <a class="dropdown-item" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
                                <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                            </a>
                        @endif
                    @break

                    @case('admin.refunds.index')
                        {{-- @if ($row->status == 'pending') --}}
                        <a class="dropdown-item" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
                            <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                        </a>
                        {{-- @endif --}}
                    @break

                    @default
                        <a class="dropdown-item" href="{{ route('admin.' . $crudRoutePart . '.edit', $row->id) }}">
                            <i class="fa fa-edit"></i> &nbsp; {{ trans('global.edit') }}
                        </a>
                @endswitch
            @endcan
        @endisset

        @isset($deleteGate)
            @can($deleteGate)
                @switch(request()->route()->getName())
                    @case('admin.payments.index')
                        @if ($row->account->balance >= $row->amount)
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    {{-- @case('admin.external-payments.index')
                            @if ($row->account->balance >= $row->amount)
                                <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                    onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                    </button>
                                </form>
                            @endif
                        @break --}}
                    @case('admin.accounts.index')
                        @if (count($row->transactions) <= 0)
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.freeze-requests.index')
                        @if ($row->status == 'pending')
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.pricelists.index')
                        @if (!$row->has('memberships'))
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.services.index')
                        @if ($row->service_pricelist->count() <= 0)
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.service-types.index')
                        @if (!$row->has('services'))
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.memberships.index')
                        @if ($row->status != 'refunded')
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @case('admin.refunds.index')
                        @if ($row->status == 'pending')
                            <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                                </button>
                            </form>
                        @endif
                    @break

                    @default
                        <form action="{{ route('admin.' . $crudRoutePart . '.destroy', $row->id) }}" method="POST"
                            onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                            <input type="hidden" name="_method" value="DELETE">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="dropdown-item">
                                <i class="fa fa-trash"></i> &nbsp; {{ trans('global.delete') }}
                            </button>
                        </form>
                @endswitch
            @endcan
        @endisset

        @if (request()->route()->getName() == 'admin.memberships.index')
            <a href="{{ route('admin.memberships.manual_attend', $row->id) }}" class="dropdown-item">
                <i class="fas fa-fingerprint"></i>
                &nbsp; {{ trans('global.manual_attend') }}
            </a>

            @if ($row->status == 'current' || $row->status == 'expiring' || $row->status == 'pending')
                @can('freeze_request_create')
                    <a href="{{ route('admin.membership.freezeRequests', $row->id) }}" class="dropdown-item">
                        <i class="fa fa-minus-circle"></i>
                        &nbsp; {{ trans('cruds.freezeRequest.title') }}
                    </a>
                @endcan

                @php
                    $a = date('Y-m-d', strtotime($row->start_date . '+ ' . $row->service_pricelist->upgrade_from . 'Days'));
                    $b = date('Y-m-d', strtotime($row->start_date . '+ ' . $row->service_pricelist->upgrade_to . 'Days'));
                @endphp

                @if ((date('Y-m-d') >= $a && date('Y-m-d') < $b) || (auth()->user()->roles[0]->title = 'Admin'))
                    @can('upgrade_membership')
                        <a href="{{ route('admin.membership.upgrade', $row->id) }}" class="dropdown-item">
                            <i class="fa fa-arrow-up"></i> &nbsp; {{ trans('cruds.membership.fields.upgrade') }}
                        </a>
                    @endcan

                    @can('downgrade_membership')
                        <a href="{{ route('admin.membership.downgrade', $row->id) }}" class="dropdown-item">
                            <i class="fa fa-arrow-down"></i>
                            &nbsp; {{ trans('cruds.membership.fields.downgrade') }}
                        </a>
                    @endcan
                @endif

                @can('transfer_membership')
                    <a href="{{ route('admin.membership.transfer', $row->id) }}" class="dropdown-item">
                        <i class="fa fa-exchange"></i>
                        {{ trans('global.transfer_membership') }}
                    </a>
                @endcan
            @endif

            @if ($row->status == 'expiring' || $row->status == 'expired')
                @can('renew_membership')
                    <a href="{{ route('admin.membership.renew', $row->id) }}" class="dropdown-item">
                        <i class="fa fa-plus-circle"></i> &nbsp; {{ trans('cruds.membership.fields.renew') }}
                    </a>
                @endcan
            @endif


            @if ($row->invoice && $row->invoice->status !== 'refund')
                @can('refund_create')
                    <a href="{{ route('admin.invoice.refund', $row->invoice->id) }}" class="dropdown-item"> <i
                            class="fas fa-recycle"></i>
                        &nbsp; {{ trans('cruds.refund.title') }}
                    </a>
                @endcan
            @endif

        @endif

        @if (request()->route()->getName() == 'admin.invoices.index')

            <a href="{{ route('admin.payments.index') }}?invoice_id={{ $row->id }}" class="dropdown-item">
                <i class="fa fa-money"></i>&nbsp;
                {{ trans('global.show') . ' ' . trans('cruds.payment.title') }}
            </a>

            {{-- <form action="{{ route('admin.invoice.download', $row->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fa fa-download"></i> &nbsp; {{ trans('global.downloadFile') }}
                    </button>
                </form> --}}



            {{-- <form action="{{ route('admin.invoice.send', $row->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fab fa-whatsapp"></i> &nbsp; {{ trans('global.whatsapp') }}
                    </button>
                </form> --}}

            @if ($row->status !== 'refund')
                <a href="{{ route('admin.invoice.refund', $row->id) }}" class="dropdown-item"><i
                        class="fas fa-exchange-alt"></i>
                    &nbsp; {{ trans('cruds.refund.title') }}</a>
            @endif

            @if (config('domains')[config('app.url')]['is_reviewed_invoices'] == true)
                <a href="javascript:void(0)" onclick="getInvoiceDetails(this)" data-toggle="modal"
                    data-target="#updateInvoiceReviewedStatusModal"
                    data-url="{{ route('admin.invoice-reviewed-status.update', $row->id) }}" class="dropdown-item"><i
                        class="fas fa-check-circle"></i>
                    &nbsp; {{ $row->is_reviewed ? trans('global.not_reviewed') : trans('global.is_reviewed') }}</a>
            @endif

            @if ($row->status == 'partial')
                <a href="javascript:void(0)" onclick="setSettlementInvoice(this)" data-toggle="modal"
                    data-target="#settlement_invoice" data-url="{{ route('admin.settlement.invoice', $row->id) }}"
                    class="dropdown-item"><i class="fas fa-check-circle"></i> &nbsp;
                    {{ trans('global.settlement') }}</a>
            @endif
        @endif

        @if (request()->route()->getName() == 'admin.accounts.index')
            <a href="{{ route('admin.account.statement', $row->id) }}" class="dropdown-item"><i
                    class="fa fa-file"></i>
                &nbsp; {{ trans('cruds.account.fields.statement') }}</a>

            <a href="{{ route('admin.account.transfer', $row->id) }}" class="dropdown-item">
                <i class="fa fa-recycle"></i>
                &nbsp; {{ trans('global.transfer') }}
            </a>
        @endif

        @if (request()->route()->getName() == 'admin.refunds.index')
            @if ($row->status == 'approved')
                @can('approve_reject_refund')
                    <form action="{{ route('admin.refund.confirm', $row->id) }}" method="POST"
                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-check"></i> &nbsp; {{ trans('global.confirm') }}
                        </button>
                    </form>
                @endcan
            @endif

            @if ($row->status == 'pending')
                @can('approve_reject_refund')
                    <form action="{{ route('admin.refund.approve', $row->id) }}" method="POST"
                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-check"></i> &nbsp; {{ trans('global.approve') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.refund.reject', $row->id) }}" method="POST"
                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-times"></i> &nbsp;{{ trans('global.reject') }}
                        </button>
                    </form>
                @endcan
            @endif
        @endif

        @if (request()->route()->getName() == 'admin.freeze-requests.index')
            @can('approve_reject_freeze')
                @if ($row->status == 'pending')
                    <form action="{{ route('admin.freeze-requests.confirm', $row->id) }}" method="POST"
                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-check"></i> &nbsp; {{ trans('global.confirm') }}
                        </button>
                    </form>

                    <form action="{{ route('admin.freeze-requests.reject', $row->id) }}" method="POST"
                        onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            <i class="fa fa-times"></i> &nbsp;{{ trans('global.reject') }}
                        </button>
                    </form>
                @endif
            @endcan
        @endif

        @if (request()->route()->getName() == 'admin.membership-attendances.index')
            <a class="dropdown-item" data-target="#editSigninAndSignoutModal" data-toggle="modal"
                href="javascript:void(0)" onclick="editSigninAndSignout(this)" data-locker="{{ $row->locker }}"
                data-sign-in="{{ $row->sign_in }}" data-sign-out="{{ $row->sign_out }}"
                data-update="{{ route('admin.membership-attendances.update', $row->id) }}"
                data-get-url="{{ route('admin.membership-attendances.edit', $row->id) }}">
                <i class="fa fa-edit"></i> &nbsp;
                {{ trans('global.edit_sign_in_and_out') }}
            </a>
        @endif

        @if (request()->route()->getName() == 'admin.employees.index')
            <form action="{{ route('admin.employees.change_status', $row->id) }}" method="POST"
                onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                <input type="hidden" name="_method" value="PUT">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="submit" class="dropdown-item">
                    {!! $row->status == 'active'
                        ? '<i class="fa fa-times-circle"></i> &nbsp; Inactive '
                        : '<i class="fa fa-check-circle"></i> &nbsp; Active ' !!}
                </button>
            </form>
            @if($row->user->roles[0]->title == 'Trainer')
                    <form action="{{ route('admin.employees.change_mobile_status', $row->id) }}" method="POST"
                          onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item">
                            {!! $row->mobile_visibility
                                ? '<i class="fa fa-times-circle"></i> &nbsp;Mobile: Inactive '
                                : '<i class="fa fa-check-circle"></i> &nbsp;Mobile: Active ' !!}
                        </button>
                    </form>
            @endif
        @endif
    </div>
</div>
