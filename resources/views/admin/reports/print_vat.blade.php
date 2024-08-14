<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style type="text/css" media="screen">

        html {
            font-family: sans-serif;
            line-height: 1.15;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            text-align: left;
            background-color: #fff;
            font-size: 10px;
            margin: 36pt;
        }

        h4 {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        p {
            margin-top: 0;
            margin-bottom: 1rem;
        }

        strong {
            font-weight: bolder;
        }

        img {
            vertical-align: middle;
            border-style: none;
        }

        table {
            border-collapse: collapse;
        }

        th {
            text-align: inherit;
        }

        h4,
        .h4 {
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }

        h4,
        .h4 {
            font-size: 1.5rem;
        }

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
        }

        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: top;
        }

        .table.table-items td {
            border-top: 1px solid #dee2e6;
        }

        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
        }

        .mt-5 {
            margin-top: 3rem !important;
        }

        .pr-0,
        .px-0 {
            padding-right: 0 !important;
        }

        .pl-0,
        .px-0 {
            padding-left: 0 !important;
        }

        .text-right {
            text-align: right !important;
        }

        .text-center {
            text-align: center !important;
        }

        .text-uppercase {
            text-transform: uppercase !important;
        }

        * {
            font-family: "DejaVu Sans";
        }

        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        table,
        th,
        tr,
        td,
        p,
        div {
            line-height: 1.1;
        }

        .party-header {
            font-size: 1.5rem;
            font-weight: 400;
        }

        .total-amount {
            font-size: 12px;
            font-weight: 700;
        }

        .border-0 {
            border: none !important;
        }

        .cool-gray {
            color: #6B7280;
        }

        /* header { 
            position: fixed; 
            left: 50px; 
            top: 15px;
            right: 10px;
            height: 250px; 
            text-align: center; 
        } */

        @page {
                margin: 0cm 0cm;
            }

            /** Define now the real margins of every page in the PDF **/
            body {
                margin-top: 6cm;
                margin-left: 1cm;
                margin-right: 1cm;
                margin-bottom: 5cm;
            }

            /** Define the header rules **/
            header {
                position: fixed;
                top: 1cm;
                left:1cm;
                right: 0cm;
                height: 3cm;
            }
    </style>
</head>

<body>
    <header>
        @if (\App\Models\Setting::first()->menu_logo)
            <img src="{{ 'images/' . \App\Models\Setting::first()->menu_logo }}" alt="logo" class="logo" height="60">
        @endif

        <h4 class="text-uppercase text-center">
            @if (request('date') == date('Y-m',strtotime(request('date'))))

                <strong>{{ trans('global.monthly_report') . ' - ' . $date }}</strong>

            @elseif(request('date') == date('Y-m-d',strtotime(request('date'))))

                <strong>{{ trans('global.daily_report') . ' - ' . $date }}</strong>
                
            @endif
        </h4>
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th class="border-0"></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th class="px-0">
                        <p>
                            <strong>{{ trans('global.total_income') }} </strong> : {{ number_format($total_income) }}
                        </p>
                    </th>
                    <th class="px-0">
                        <p>
                            <strong>{{ trans('global.total_outcome') }}</strong> : {{ number_format($total_outcome) }}
                        </p>
                    </th>
                    <th class="px-0">
                        <p>
                            <strong>{{ trans('global.net_income') }}</strong> : {{ number_format($net_income) }}
                        </p>
                    </th>
                </tr>
    
                {{-- <tr>
                    <td class="px-0">
                        <p>
                            <strong>{{ trans('cruds.membership.fields.renew') }} </strong> : {{ $renewals }}
                        </p>
                    </td>
                    <td class="px-0">
                        <p>
                            <strong>New Members</strong> : {{ $new_members }}
                        </p>
                    </td>
                </tr> --}}
            </tbody>
        </table>
    </header>

    {{-- <h5 class="text-uppercase text-left">
        {{ trans('cruds.payment.title') }}
    </h5> --}}
    
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>{{ trans('cruds.payment.fields.invoice') }}</th>
                <th>{{ trans('global.payments.price') }}</th>
                <th>{{ trans('global.payments.vat') }}</th>
                <th>{{ trans('global.payments.net') }}</th>
                <th>{{ trans('cruds.account.title') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <b class="d-block">
                            @if($payment->invoice->membership->member)
                            ({{ $payment->invoice->membership->member->name }} )
                            @else
                            ({{ $payment->invoice->membership->id }} )
                            @endif
                        </b>
                          
                        <div class="badge badge-success">
                            <b class="d-block">
                                {{ $payment->invoice->membership->service_pricelist->service->name . ' @ ' . $payment->invoice->membership->service_pricelist->amount . ' @ ' }}

                                {{ $payment->invoice->membership->service_pricelist->session_count != 0 ? $payment->invoice->membership->service_pricelist->session_count . ' Session/s ' : '' }}
                            </b>

                        </div>
                    </td>
                    <td>{{ round(($payment->amount/1.14),2) }}</td>
                    <td>{{ round((($payment->amount/1.14)*0.14),2) }}</td>
                    <td>{{ $payment->amount }}</td>
                    <td>{{ $payment->account->name ?? '' }}</td>
                </tr>
            @empty
                <td colspan="6" class="text-center">{{ trans('global.no_data_available') }}</td>
            @endforelse
            <hr>
            
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td><strong>Total</strong></td>
                <td><strong>{{ number_format(round(($net_income/1.14),2)) }}</strong></td>
                <td><strong>{{ number_format(round((($net_income/1.14)*0.14),2)) }}</strong></td>
                <td><strong>{{ number_format($net_income) }}</strong></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
  
</body>

</html>
