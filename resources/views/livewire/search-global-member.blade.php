<div class="form-inline position-relative"> 
    {{-- d-md-down-none --}}
    {{-- style="width:20vw" --}}
    <div class="input-group">
        <input class="form-control mr-sm-6 w-auto" wire:model.live="searchQuery" type="search"  placeholder="Search" name="search" aria-label="Search">
    </div>
    @if ($members->isNotEmpty())
    <div style="position: absolute;top:75px;" class="form-group pt-3 bg-white px-3 pt-xs-0 px-xs-0 w-auto" >
        <ul class="list-unstyled w-auto" style="overflow-y: scroll;max-height:70vh">
            @foreach ($members as $member)
            <li class="mt-2 py-3 px-4 bg-light rounded">
                <a href="{{ route('admin.members.show', $member->id) }}" class="text-decoration-none text-dark font-weight-bold">
                    <div class="row">
                        <div class="col-md-2 pt-3 text-center">
                        <i class="far fa-user fa-lg mr-3 bg-secondary rounded-pill text-white" style="width:35px;height:35px;line-height:35px;"></i>
                        </div>
                        <div class="col-md-10 pl-4">
                        @if($member->member_code)
                        #{{ $member->branch ? $member->branch->member_prefix : '' }}{{ $member->member_code }}<br>
                        @endif
                        {{ $member->name }}<br>
                        {{ $member->phone }}
                        </div>
                    </div>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
