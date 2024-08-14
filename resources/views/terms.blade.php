@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row my-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h2>Terms & Conditions</h2>
                    </div>
                    <div class="card-body">
                        {!! $terms->terms !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection