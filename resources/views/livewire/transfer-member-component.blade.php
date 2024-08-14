<div>
    <div class="form-group">
        {!! Form::label('lead', trans('cruds.member.fields.name'), ['class' => 'required font-weight-bold']) !!}
        {!! Form::text('lead', old('lead') , ['class' => 'form-control', 'wire:model.live' => 'searchQuery', 'placeholder' => trans('global.type_here'),'required']) !!}
    </div>
    <div class="getMyData">
        @foreach ($members as $member)
            <div role="button" onclick="currentMember(this)" data-mn="{{ $member->name }}" data-mi="{{ $member->id }}" class="form-group mb-2 rounded py-4 px-3 bg-light text-dark font-weight-bold">
                <span class="text-primary"> <i class="far fa-user"></i> {{ $member->name }} </span> <br>
                <span class="text-danger"> <i class="fas fa-mobile"></i> {{ $member->phone }}</span>
            </div>
        @endforeach
    </div>
    <input type="hidden" name="member_type" id="member_type" value="{{ old('member_type') }}">
    @if (old('member_type') != 'new_member')
    <button id="myCreateNew" class="btn btn-link px-0 shadow-none mb-3 text-decoration-none text-success font-weight-bold" type="button" onclick="createNewLead()">
        <i class="fa fa-plus-circle"></i> {{ trans('global.create_new') }}
    </button>
    @endif
</div>
