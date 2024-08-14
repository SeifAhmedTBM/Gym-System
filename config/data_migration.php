<?php

return [
    'master_data'       => [
        'title'         => 'cruds.masterData.title',
        'children'      => [
            'master_data' => [
                'title'         => 'cruds.masterData.title',
                'name'          => 'MasterData',
                'is_required'   => false
            ],
            'serviceTypes' => [
                'title'         => 'cruds.serviceType.title',
                'name'          => 'ServiceType',
                'is_required'   => false
            ],
            'services' => [
                'title'         => 'cruds.service.title',
                'name'          => 'Service',
                'is_required'   => false
            ],
            'pricelists' => [
                'title'         => 'cruds.pricelist.title',
                'name'          => 'Pricelist',
                'is_required'   => false
            ],
            'memberStatus' => [
                'title'         => 'cruds.memberStatus.title',
                'name'          => 'MemberStatus',
                'is_required'   => false
            ],
            'expenses'      => [
                'title'         => 'cruds.expense.title',
                'name'          => 'Expenses',
                'is_required'   => false
            ]
        ]
    ],
    'employees'       => [
        'title'         => 'cruds.hrManagement.title',
        'notes'         => 'global.employees_migration_note',
        'available_roles' => true,
        'children'      => [
            'employees' => [
                'title'         => 'cruds.employee.title',
                'name'          => 'Employee',
                'is_required'   => true
            ],
        ]
    ],
    'leads_and_members'       => [
        'title'         => 'global.leads_and_members',
        'notes'         => 'global.make_sure_of_pricelist',
        'available_pricelists' => true,
        'children'      => [
            'leads' => [
                'title'         => 'cruds.lead.title',
                'name'          => 'Lead',
                'is_required'   => false
            ],
            'members' => [
                'title'         => 'cruds.member.title',
                'name'          => 'Member',
                'is_required'   => false
            ],
            'memberships' => [
                'title'         => 'cruds.membership.title',
                'name'          => 'Membership',
                'is_required'   => false
            ],
            'reminders' => [
                'title'         => 'cruds.reminder.title',
                'name'          => 'Reminder',
                'is_required'   => false
            ],
        ]
    ],
];