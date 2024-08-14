@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="card-header">
        <h5>Invoice</h5>
    </div>
    <div class="card-body container">
        <div class="row">
            <div class="col-md-6 text-left">
                {!! $invoice['left_section'] !!}
            </div>
            <div class="col-md-6 text-right">
                {!! $invoice['right_section'] !!}
            </div>
        </div>
        
        <div class="row">
            <table class="table table-bordered">
                <thead class="tr">
                    <th>Member</th>
                    <th>Subscription</th>
                    <th>Fees</th>
                    <th>Total</th>
                </thead>
                <tbody>
                    <tr>
                        <td>Ahmed</td>
                        <td>3 Months/300 EGP</td>
                        <td>300 EGP</td>
                        <td>300 EGP</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="row">
            <div class="col-md-12">
                {!! $invoice['footer'] !!}
            </div>
        </div>
    </div>
</div>
@endsection