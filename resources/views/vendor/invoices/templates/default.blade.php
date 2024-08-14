<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    {{-- <style type="text/css" media="screen">
            html {
                font-family: sans-serif;
                line-height: 1.15;
                margin: 0;
                direction: rtl;
            }

            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 400;
                line-height: 1.5;
                color: #212529;
                text-align: left;
                background-color: #fff;
                font-size: 5px !important;
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
                margin-bottom:20px !important;
                display:block;
            }

            table {
                border-collapse: collapse;
            }

            th {
                text-align: inherit;
            }

            h4, .h4 {
                margin-bottom: 0.5rem;
                font-weight: 500;
                line-height: 1.2;
            }

            h4, .h4 {
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
            body, h1, h2, h3, h4, h5, h6, table, th, tr, td, p, div {
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
            .flex-container {
                display: flex;
            }

            .flex-child {
                flex: 1;
                border: 2px solid yellow;
            }
        </style> --}}
</head>

<body>
    {{-- Header --}}
    <div>
        @if ($invoice['logo'])
            <img src="{{ $invoice['logo'] }}" alt="logo" width="50">
        @endif
    </div>

    <table class="table" style="margin-bottom: 10px !important; text-align:center">
        <tbody>
            <tr>
                <td class="px-0">
                    {!! $invoice['seller']->custom_fields !!}
                </td>
                <td style="margin-right:40px;">
                    @if ($invoice['buyer']->address)
                        <p class="buyer-address">
                            {{ __('invoices::invoice.address') }}: {{ $invoice['buyer']->address }}
                        </p>
                    @endif

                    @if ($invoice['buyer']->code)
                        <p class="buyer-code">
                            {{ __('invoices::invoice.code') }}: {{ $invoice['buyer']->code }}
                        </p>
                    @endif

                    @if ($invoice['buyer']->vat)
                        <p class="buyer-vat">
                            {{ __('invoices::invoice.vat') }}: {{ $invoice['buyer']->vat }}
                        </p>
                    @endif

                    @if ($invoice['buyer']->phone)
                        <p class="buyer-phone">
                            {{ __('invoices::invoice.phone') }}: {{ $invoice['buyer']->phone }}
                        </p>
                    @endif

                    {!! $invoice['buyer']->custom_fields !!}
                </td>
            </tr>
        </tbody>
    </table>

    <br> <br> <br>
    <table class="table mt-5" border="1" style="padding:5px;margin-bottom:10px;text-align:center">
        <thead>
            <tr>
                <th>Invoice Number</th>
                <th>Invoice Date</th>
                <th>Member Code</th>
                <th>Member Name</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>

                <td class="font-size:5px !important;">
                    #{{ $invoice['invoice']->id }}
                </td>
                <td class="font-size:5px !important;">
                    {{ date('Y-m-d h:i A', strtotime($invoice['invoice']->created_at)) }}
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->member->member_code }}
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->member->name }}
                </td>

                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->status }}
                </td>

            </tr>
        </tbody>
    </table>
    <br> <br>
    <table class="table mt-5" border="1" style="padding:5px;margin-bottom:10px;text-align:center">
        <thead>
            <tr>
                <th>Service</th>
                <th>Start - End </th>
                <th>Sessions Count</th>
                <th>Trainer </th>
                <th>Sales By</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->service_pricelist->name }}
                </td>
                <td class="font-size:5px !important;">

                    {{ $invoice['invoice']->membership->start_date }}

                    <br>

                    {{ $invoice['invoice']->membership->end_date }}

                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->service_pricelist->session_count ?? '0' }}
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->trainer->name ?? '-' }}
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->membership->sales_by->name ?? '-' }}
                </td>

            </tr>
        </tbody>
    </table>
    <br><br>
    <table class="table mt-5" border="1" style="padding:5px;margin-bottom:10px;text-align:center">
        <thead>
            <tr>
                <th>Price</th>
                <th>Dicsount </th>
                <th>Net Amount</th>
                <th>Paid Amount </th>
                <th>Rest</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->service_fee }} LE
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->discount }} LE
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->net_amount }} LE
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->payments->sum('amount') }} LE
                </td>
                <td class="font-size:5px !important;">
                    {{ $invoice['invoice']->net_amount - $invoice['invoice']->payments->sum('amount') }} LE
                </td>
            </tr>
        </tbody>
    </table>
    <br><br>
    <table class="table table-items" border="1" style="padding:5px;margin-bottom:10px;text-align:center">
        <thead>
            <tr>
                <th scope="col" class="text-right border-0">Date</th>
                <th scope="col" class="text-right border-0">Amount</th>
                <th scope="col" class="text-right border-0">Payment method</th>
                <th scope="col" class="text-right border-0">Created by</th>
            </tr>
        </thead>
        <tbody>
            {{-- Items --}}
            @foreach ($invoice['invoice']->payments as $index => $payment)
                <tr>
                    <td class="pl-0">
                        {{ date('Y-m-d h:i A', strtotime($payment->created_at)) ?? '-' }}
                    </td>
                    <td class="text-right">
                        {{ $payment->amount ?? '-' }}
                    </td>
                    <td class="text-right pr-0">
                        {{ $payment->account->name ?? '-' }}
                    </td>
                    <td class="text-right pr-0">
                        {{ $payment->created_by->name ?? '-' }}
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <br><br>
    {{-- @if ($invoice['invoice']->membership->service_pricelist->serviceOptionsPricelist)
        <table class="table mt-5" border="1" style="padding:5px;margin-bottom:10px;text-align:center">
            <thead>
                <tr>
                    @foreach ($invoice['invoice']->membership->service_pricelist->serviceOptionsPricelist as $option)
                        <th>{{ $option->service_option->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                <tr>
                    @foreach ($invoice['invoice']->membership->service_pricelist->serviceOptionsPricelist as $option)
                        <td class="font-size:5px !important;">
                            {{ $option->service_option ? $option->count ?? '0' : '-' }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
        <br>
    @endif --}}

    <br><br>
    <table class="table table-items" style="padding:5px;margin-bottom:5px;text-align:right;">
        <tbody>
            <tr>
                <td class="px-0">
                    @if ($invoice['notes'])
                        {!! $invoice['notes'] !!}
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
