<?php

namespace App\Exports;

use App\Models\Invitation;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class InvitationsExport implements FromCollection, WithHeadings
{
    use Exportable;
    
    public function collection()
    {
        $invitations = Invitation::index(request()->all())
                                ->with([
                                    'lead',
                                    'member',
                                    'membership' => fn($q) => $q->with('service_pricelist')
                                    ])
                                ->latest()
                                ->get();
                                
        $invitations = $invitations->map(function($invitation){
                return [
                    'id'                        => $invitation->id,
                    'lead_name'                 => $invitation->lead->name ?? '-',
                    'lead_phone'                => $invitation->lead->phone ?? '-',
                    'member_code'               => $invitation->member->member_code ?? '-',
                    'member_name'               => $invitation->member->name ?? '-',
                    'member_phone'              => $invitation->member->phone ?? '-',
                    'service_pricelist'         => $invitation->membership->service_pricelist->name ?? '-',
                    'created_at'                => $invitation->created_at
                ];
        });

        return $invitations;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Lead Name',
            'Lead Phone',
            'Member Code',
            'Member Name',
            'Member Phone',
            'Membership',
            'Created At'
        ];
    }
}
