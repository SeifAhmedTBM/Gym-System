<!-- Modal -->
<div class="modal fade" id="zoomModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">Zoom</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.marketing.settings.zoom')]) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label for="zoom_account_id">ZOOM ACCOUNT ID</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'zoom')->first() ? json_decode($marketing->where('service', 'zoom')->first()->settings)->ZOOM_ACCOUNT_ID : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="zoom_account_id" id="zoom_account_id" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zoom_client_id">ZOOM CLIENT ID</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'zoom')->first() ? json_decode($marketing->where('service', 'zoom')->first()->settings)->ZOOM_CLIENT_ID : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="zoom_client_id" id="zoom_client_id" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zoom_secret_id">ZOOM SECRET ID</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'zoom')->first() ? json_decode($marketing->where('service', 'zoom')->first()->settings)->ZOOM_SECRET_ID : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="zoom_secret_id" id="zoom_secret_id" class="form-control">
                </div>
                <div class="form-group">
                    <label for="zoom_cache_token">ZOOM_CACHE_TOKEN</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'zoom')->first() ? json_decode($marketing->where('service', 'zoom')->first()->settings)->ZOOM_CACHE_TOKEN : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="zoom_cache_token" id="zoom_cache_token" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ trans('global.save') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">{{ trans('global.whatsapp') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.marketing.settings.whatsapp')]) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label for="wassenger_token">{{ trans('global.wassenger_token') }}</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'whatsapp')->first() ? json_decode($marketing->where('service', 'whatsapp')->first()->settings)->wassenger_token : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="wassenger_token" id="wassenger_token" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ trans('global.save') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="smsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">{{ trans('global.sms') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.marketing.settings.sms')]) !!}
            <div class="modal-body">
                <div class="form-group">
                    {{-- <label for="phone">{{ trans('global.phone') }}</label> --}}
                    <label for="username">Username</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'sms')->first() ? json_decode($marketing->where('service', 'sms')->first()->settings)->username : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="username" id="username" class="form-control">
                </div>
                <div class="form-group">
                    {{-- <label for="phone">{{ trans('global.phone') }}</label> --}}
                    <label for="username">Password</label>
                    <input type="text" value="{{ $marketing->where('service', 'sms')->first() ? json_decode($marketing->where('service', 'sms')->first()->settings)->password : '' }}" placeholder="{{ trans('global.type_here') }}" name="password" id="password" class="form-control">
                </div>
                <div class="form-group">
                    <label for="account_sid">Account ( SID )</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'sms')->first() ? json_decode($marketing->where('service', 'sms')->first()->settings)->account_sid : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="account_sid" id="account_sid" class="form-control">
                </div>
                {{-- <div class="form-group">
                    <label for="auth_token">Auth Token</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'sms')->first() ? json_decode($marketing->where('service', 'sms')->first()->settings)->auth_token : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="auth_token" id="auth_token" class="form-control">
                </div> --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ trans('global.save') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

<!-- EMAIL CAMPAIGNS SETTINGS -->
<div class="modal fade" id="emailCampaignsModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold" id="exampleModalLabel">{{ trans('global.email_campaigns') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {!! Form::open(['method' => 'POST', 'url' => route('admin.marketing.settings.smtp')]) !!}
            <div class="modal-body">
                <div class="form-group">
                    <label for="MAIL_HOST">Host</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_HOST : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_HOST" id="MAIL_HOST" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_PORT">Port</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_PORT : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_PORT" id="MAIL_PORT" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_USERNAME">Username</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_USERNAME : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_USERNAME" id="MAIL_USERNAME" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_PASSWORD">Password</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_PASSWORD : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_PASSWORD" id="MAIL_PASSWORD" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_ENCRYPTION">Encryption ( TLS / SSL )</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_ENCRYPTION : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_ENCRYPTION" id="MAIL_ENCRYPTION" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_FROM_ADDRESS">From Address</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_FROM_ADDRESS : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_FROM_ADDRESS" id="MAIL_FROM_ADDRESS" class="form-control">
                </div>

                <div class="form-group">
                    <label for="MAIL_FROM_NAME">From Name</label>
                    <input type="text"
                    value="{{ $marketing->where('service', 'smtp')->first() ? json_decode($marketing->where('service', 'smtp')->first()->settings)->MAIL_FROM_NAME : '' }}"
                    placeholder="{{ trans('global.type_here') }}" name="MAIL_FROM_NAME" id="MAIL_FROM_NAME" class="form-control">
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('global.cancel') }}</button>
                <button type="submit" class="btn btn-success">{{ trans('global.save') }}</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>