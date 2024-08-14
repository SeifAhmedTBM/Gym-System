<?php

namespace App\Exports;

use App\Models\Setting;
use App\Models\MemberRequest;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportMemberRequestSheet implements FromCollection , WithHeadings
{

    use Exportable;

    public $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Member Name',
            'Member Code',
            'Member Phone',
            'Member Gender',
            'Subject',
            'Comment',
            'Status',
            'Created By',
            'Created At'
        ];
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        if($this->data != NULL) {
            $member_requests = MemberRequest::index($this->data)->get();
        }else {
            $member_requests = MemberRequest::all();
        }

        $member_requests = $member_requests->map(function ($member_request) {
            return [
                'member_name'        => $member_request->member->name,
                'member_code'        => ( Setting::first()->member_prefix ?? '' ) . $member_request->member->member_code,
                'member_phone'       => $member_request->member->phone,
                'member_gender'      => ucfirst($member_request->member->gender),
                'subject'            => $member_request->subject,
                'comment'            => $member_request->comment,
                'status'             => ucfirst($member_request->status),
                'created_by'         => $member_request->user->name,
                'created_at'         => $member_request->created_at->toFormattedDateString() . ' , ' . $member_request->created_at->format('g:i A')
            ];
        });

        return $member_requests;
    }
}
