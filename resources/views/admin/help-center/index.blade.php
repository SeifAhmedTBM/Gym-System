@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        @foreach ($faqCategories as $index => $category)
                            <a class="nav-link {{ $index == 0 ? 'active' : '' }}" id="tab{{ $category->id }}-tab"
                                data-toggle="pill" href="#tab{{ $category->id }}" role="tab"
                                aria-controls="tab{{ $category->id }}" aria-selected="true">{!! $category->category !!}</a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="v-pills-tabConent">
                        @foreach ($faqCategories as $index => $category)
                            <div class="tab-pane fade {{ $index == 0 ? 'show active' : '' }}" id="tab{{ $category->id }}" role="tabpanel" ">
                                <div id="accordion">
                                    @foreach ($category->questions as $index => $question)
                                        <div class="card">
                                            <div class="card-header bg-primary text-white p-0" id="headingOne">
                                                <a class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $question->id }}" aria-expanded="true"
                                                    aria-controls="collapseOne">
                                                    {!! $question->question !!}
                                                </a>
                                            </div>
                                    
                                            <div id="collapse{{ $question->id }}" class="collapse {{ $index == 0 ? 'show' : '' }}" aria-labelledby="headingOne" data-parent="#accordion">
                                                <div class="card-body">
                                                    {!! $question->answer !!}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
