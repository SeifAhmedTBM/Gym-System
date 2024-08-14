@component('mail::message')

{!! $data['body'] !!}

{{ trans('global.thankYouForUsingOurApplication') }},<br>
{{ config('app.name') }}
@endcomponent
