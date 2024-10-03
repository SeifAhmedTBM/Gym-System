@foreach ($members as $member)
    <tr>
        <td>{{ $loop->iteration + ($members->currentPage() - 1) * $members->perPage() }}</td>
        <td>
            <a href="{{ route('admin.members.show',$member->id) }}" target="_blank">
                {{ $member->name ?? '-' }} <br>
                {{ $member->phone }}
            </a>
        </td>
        <td>{{ $member->branch->name ?? '-' }}</td>
        <td>{{ $member->sport->name ?? '-' }}</td>
        <td>
            <a href="{{ route('admin.memberships.index',['member_id'=>$member->id]) }}"
               target="_blank">
                {{ $member->memberships_count }}
            </a>
            {{-- @foreach ($member->memberships as $membership)
                <span class="badge badge-info">
                    {{ $membership->status ?? '-' }}
                </span>
            @endforeach --}}
        </td>
    </tr>
@endforeach