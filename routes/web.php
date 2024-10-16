<?php

use App\Models\Membership;
use App\Models\TrainerAttendant;
use App\Models\MembershipAttendance;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Jobs\UpdateExpiredMembershipsJob;
use App\Jobs\UpdateExpiringMembershipsJob;
use App\Models\Service;
use App\Models\Invoice;

Route::get('memberships-changed', function () {
    $memberships = Membership::with(['member', 'trainer', 'service_pricelist', 'service_pricelist.service'])
        ->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('main_service', 1))
        ->where('status', 'expired')
        ->where('is_changed', 0)
        ->get();
    foreach ($memberships as $membership) {
        if ($membership->service_pricelist->service->service_type->main_service) {
            if (Membership::where('member_id', $membership->member_id)->whereHas('service_pricelist.service.service_type', fn ($q) => $q->where('id', $membership->service_pricelist->service->service_type->id))->whereIn('status', ['current', 'pending'])->first()) {
                $membership->update(['is_changed' => 1]);
            }
        }
    }
    dd("DONE");
});


// Route::get('sett-invoice', function() {
//     $invoices = Invoice::where('created_at','>=','2022-01-01')->where('created_at','<=','2022-04-30')->with(['payments'])->withSum('payments','amount')
//                         ->whereHas('membership',function($q){
//                             return $q->whereIn('service_pricelist_id',[6,7,8,9,10,11,12,13,14,15,17,18,19,20,21,22,23,24,25]);
//                         })->get();


//         foreach($invoices as $invoice){
//             $payment_sum = $invoice->payments_sum_amount ?? 0;
//             $invoice->update([
//                 'status'        => 'settlement',
//                 'net_amount'    => $payment_sum
//             ]);
//         }
//         echo 'success';
// });

Route::get('update-services', function () {
    $services = Service::all();
    $days = ['30', '60', '90', '120', '150', '180', '210', '240', '260', '290', '320', '360', '365'];
    foreach ($services as $service) {
        if (in_array($service->expiry, $days)) {
            $service->expiry = ($service->expiry / 30);
            $service->type = 'months';
            $service->save();
        }
    }
});


Route::get('fixSales', 'Admin\HomeController@fixSales')->name('fixSales');



Route::get('update-membership-status', function () {
    $memberships = Membership::all();
    $memberships_data = collect([]);
    foreach ($memberships as $membership) {
        if ($membership->end_date > date('Y-m-d')) {
            $membership->update(['status' => 'current']);
        } else {
            $membership->update(['status' => 'expired']);
        }
        $memberships_data->push([
            'end_date'      => $membership->end_date,
            'status'        => $membership->status
        ]);
    }
    return $memberships_data;
});

Route::get('update-memberships-status', function () {
    $today = now()->format('Y-m-d');
    $expiredCount = Membership::whereHas('service_pricelist.service.service_type', function ($q) {
        $q->where('main_service', true);
    })->whereDate('end_date', $today)
        ->whereNotIn('status',['refunded','expired'])
        ->update(['status' => 'expired']);

    $currentCount = Membership::whereHas('service_pricelist.service.service_type', function ($q) {
        $q->where('main_service', true);
    })->whereDate('start_date', $today)
        ->whereNotIn('status',['refunded','current'])
        ->update(['status' => 'current']);

    return response()->json([
        'message' => 'Membership statuses updated successfully.',
        'current_updated' => $currentCount,
        'expired_updated' => $expiredCount,
    ]);
});

Route::get('update-all-memberships-status', function () {
    $today = now()->format('Y-m-d');
    $currentCount = Membership::whereHas('service_pricelist.service.service_type', function ($q) {
        $q->where('main_service', true);
    })->whereDate('start_date','<=', $today)
        ->whereDate('end_date','>', $today)
        ->whereNotIn('status',['refunded','current'])
        ->update(['status' => 'current']);

    $expiredCount = Membership::whereHas('service_pricelist.service.service_type', function ($q) {
        $q->where('main_service', true);
    })->whereDate('end_date','<=', $today)
        ->whereNotIn('status', ['refunded', 'expired'])
        ->update(['status' => 'expired']);
    return response()->json([
        'message' => 'Membership statuses updated successfully.',
        'expired_updated' => $expiredCount,
        'current_updated' => $currentCount,
    ]);
});


Route::get('update-trainer-attendants', function () {

    $trainer_attendants = TrainerAttendant::all();
    foreach ($trainer_attendants as $trainer_attendant) {
        $membership = $trainer_attendant->member->memberships()->first();
        $trainer_attendant->update(['membership_id' => $membership->id]);
    }
});


Route::get('update-attendances-status', function () {
    $attendances = MembershipAttendance::with('membership')->whereHas('membership')->get();
    foreach ($attendances as $attend) {
        $attend->update(['membership_status' => $attend->membership->status]);
    }
    return "DONE";
});

Route::get('privacy-policy', 'HomeController@privacy')->name('privacy');

Route::get('terms', 'HomeController@terms')->name('terms');

Route::redirect('/', '/login');

Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }
    return redirect()->route('admin.home');
});


Route::get('attendance', 'Admin\AttendanceController@index')->name('attendance_take.index');

Route::get('get_membership_details', 'Admin\AttendanceController@getMembershipDetails')->name('membership.get_details');

Route::post('take_attendance', 'Admin\AttendanceController@takeAttendance')->name('attendance.take');
Route::get('check-freeze/{membership_id}', 'Admin\AttendanceController@checkFreeze')->name('checkFreeze');
Route::get('fixAll', 'Admin\AttendanceController@fixAll')->name('fixAll');

Route::get('duplicates', 'HomeController@duplicates')->name('duplicates');

Auth::routes(['register' => false]);

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {

    Route::get('reports', 'HomeController@sitemap')->name('reports');
    Route::get('/', 'HomeController@index')->name('home');

    Route::get('/migrate', 'HomeController@migrate')->name('migrate');

    Route::get('/operations', 'HomeController@operations')->name('operations');
    Route::resource('member-requests', 'MemberRequestsController');

    Route::put('member-requests/update-status/{member_request}', 'MemberRequestsController@updateStatus')->name('memberRequest.updateStatus');

    Route::post('new-reply', 'MemberRequestsController@storeReply')->name('reply.store');
    Route::get('export-member-requests-sheet', 'MemberRequestsController@exportMemberRequestSheet')->name('member-requests-sheet.export');

    Route::post('remove-cache', 'ReportController@removeCache')->name('cache.remove');

    // Settings
    Route::resource('settings', 'SettingController');
    Route::resource('mobile_settings', 'MobileSettingController');

    // Membership Schedule
    // Route::resource('membership-schedule', 'MembershipScheduleController');
    Route::get('membership-schedule/{id}', 'MembershipScheduleController@index')->name('membership-schedule.index');

    Route::get('showAttendanceDetails/{id}', 'MembershipScheduleController@showAttendanceDetails')->name('showAttendanceDetails.index');

    Route::get('membership-schedule-attendances/{id}', 'MembershipScheduleController@attendances')->name('membership-schedule.attendances');

    Route::post('membership-schedule-swip-attend/{id}', 'MembershipScheduleController@swip_attend')->name('membership-schedule.swip-attend');

    Route::get('membership-schedule/{id}/create', 'MembershipScheduleController@create')->name('membership-schedule.create');
    Route::post('membership-schedule/{id}/store', 'MembershipScheduleController@store')->name('membership-schedule.store');
    Route::delete('membership-schedule/{id}/destroy', 'MembershipScheduleController@destroy')->name('membership-schedule.destroy');
    Route::post('membership-schedule/attend', 'MembershipScheduleController@attend_membership')->name('membership-schedule.attend');

    // Trainer Attendances
    Route::delete('trainer-attendances/destroy', 'TrainerAttendancesController@massDestroy')->name('trainer-attendances.massDestroy');
    Route::resource('trainer-attendances', 'TrainerAttendancesController');

    // Route::delete('trainer_attenndannce/{id}/delete', 'MembershipScheduleController@deleteAttendannce')->name('membership-schedule.deleteAttendannce');

    // Route::delete('pluck-trainer_attenndannce/delete', 'MembershipScheduleController@deletePluckAttendannce')->name('membership-schedule.deletePluckAttendannce');



    // Data Migration
    Route::get('data-migration', 'MigrationController@migration')->name('migration.index');

    Route::group(['prefix' => 'marketing', 'as' => 'marketing.'], function () {
        Route::resource('campaigns', 'Marketing\CampaignsController');
        Route::resource('settings', 'Marketing\SettingsController');
        Route::post('whatsapp-settings', 'Marketing\SettingsController@saveWhatsappSettings')->name('settings.whatsapp');
        Route::post('zoom-settings', 'Marketing\SettingsController@saveZoomSettings')->name('settings.zoom');
        Route::post('sms-settings', 'Marketing\SettingsController@saveSmsSettings')->name('settings.sms');
        Route::post('smtp-settings', 'Marketing\SettingsController@saveSmtpSettings')->name('settings.smtp');
        Route::resource('whatsapp', 'Marketing\WhatsappController');
        Route::resource('sms', 'Marketing\SmsController');
        Route::resource('mails', 'Marketing\MailCampsController');
    });

    Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
        Route::post('master_data', 'MigrationController@importMasterData')->name('master_data');
        Route::post('employees', 'MigrationController@importEmployees')->name('employees');
        Route::post('leads_and_members', 'MigrationController@importLeadsAndMembers')->name('leads_and_members');
    });

    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {

        Route::get('current-memberships', 'ReportController@currentMembershipsReport')->name('current-memberships');
        Route::get('export-current-memberships', 'ReportController@exportCurrentMembershipsReport')->name('export-current-memberships');

        Route::get('sessions-instructed', 'ReportController@sessionsInstructed')->name('sessions-instructed');
        Route::get('athletes-instructed', 'ReportController@athletesInstructed')->name('athletes-instructed');
        Route::get('revenue-details', 'ReportController@revenueDetails')->name('revenue-details');

        Route::get('reminders', 'ReportController@reminders')->name('reminders');

        Route::get('reminders-action-history', 'ReportController@remindersAction')->name('reminders.action');

        Route::get('expired-memberships-attendances', 'ReportController@expiredMembershipAttendanceReport')->name('expired-membership-attendances');

        Route::get('revenue', 'ReportController@revenue')->name('revenue');


        Route::get('sessions-revenue', 'ReportController@sessions_revenue')->name('sessions-revenue');
        Route::get('coaches', 'ReportController@coaches')->name('coaches');

        Route::get('session-attendances/{session_name}', 'ReportController@sessionAttendances')->name('sessionAttendances');

        Route::get('session-attendances', 'ReportController@exportSessionsAttendancesReport')->name('session-attendances.export');

        Route::get('schedule-timeline', 'ReportController@scheduleTimeline')->name('schedule.timeline');

        Route::post('print-schedule', 'ReportController@printSchedule')->name('schedule.print');

        Route::get('services', 'ReportController@services')->name('services');

        Route::get('offers', 'ReportController@offers')->name('offers');

        Route::get('leads-source', 'ReportController@leadsSource')->name('leadsSource');

        Route::get('leads-source-show/{name}', 'ReportController@leadsSourceShow')->name('leads-source.show');

        Route::get('members-source', 'ReportController@membersSource')->name('membersSource');

        Route::get('sessions-attendances', 'ReportController@sessionsAttendancesReport')->name('sessionsAttendancesReport');

        Route::get('sales-report', 'ReportController@salesReport')->name('sales-report');
        Route::get('sales-report/{id}', 'ReportController@viewSalesReport')->name('sales-report.view');
        
        Route::get('sales-manager-report', 'ReportController@sales_manager_report')->name('sales-manager-report');

        Route::get('freelancers-report', 'ReportController@freelancersReport')->name('freelancers-report');

        Route::get('due-payments-report', 'ReportController@duePaymentsReport')->name('due-payments-report');

        Route::get('trainers-report', 'ReportController@trainersReport')->name('trainers-report');
        // Route::get('trainers-report/{trainer_id}', 'ReportController@showTrainerReport')->name('trainers-report.show');
        Route::get('trainer-report/{trainer_id}', 'ReportController@show_trainer_report')->name('show-trainer-report');

        Route::get('daily-report', 'ReportController@dailyReport')->name('daily.report');
        Route::get('daily-report/print', 'ReportController@printDailyReport')->name('print.dailyReport');

        // 
        Route::get('assigned-coaches-report', 'ReportController@assignedCoachesReport')->name('assigned-coaches.report');
        Route::get('monthly-report', 'ReportController@monthlyReport')->name('monthly.report');
        Route::get('monthly-report/print', 'ReportController@printMonthlyReport')->name('print.monthlyReport');
        Route::get('monthly-report/export', 'ReportController@monthlyReportExport')->name('monthlyReport.export');

        Route::get('yearly-finance-report', 'ReportController@yearlyFinanceReport')->name('yearlyFinance.report');

        Route::get('monthly-finance-report', 'ReportController@monthlyFinanceReport')->name('monthlyFinance.report');

        Route::get('expenses-report', 'ReportController@expensesReport')->name('expenses.report');

        Route::get('refund-reasons-report', 'ReportController@refundReasonsReport')->name('refundReasons.report');

        Route::get('external-payment-categories-report', 'ReportController@externalPaymentCategories')->name('external-payment-categories.report');

        Route::get('trainer-commissions-report', 'ReportController@trainerCommissions')->name('trainerCommissions.report');

        Route::get('show-trainer-commissions-report/{id}', 'ReportController@showTrainerCommissions')->name('showTrainerCommissions.report');

        Route::get('show-session-attendances/{trainer_id}/{schedule_id}/{session_date}', 'ReportController@showSessionAttendances')->name('showSessionAttendances.report');

        // Export Reports
        Route::post('export-revenue-details-report', 'ReportController@exportRevenueDetailsReport')->name('revenue-details-report.export');
        Route::post('export-sessions-instructed', 'ReportController@exportSessionsInstructedReport')->name('sessions-instructed-report.export');
        Route::post('export-athletes-instructed', 'ReportController@exportAthletesInstructedReport')->name('athletes-instructed-report.export');

        Route::post('coaches-report-export', 'ReportController@exportCoachesReport')->name('coaches-report.export');
        Route::post('revenues-report-export', 'ReportController@exportRevenueReport')->name('revenues-report.export');
        Route::post('trainers-report-export', 'ReportController@exportTrainersReport')->name('trainers-report.export');
        Route::post('expired-membership-attendances-export', 'ReportController@exportMembershipAttendancesReport')->name('expired-membership-attendances.export');

        Route::get('branch-sales-details/{id}', 'ReportController@sales_details')->name('sales_details');

        Route::get('dayuse-report', 'ReportController@dayuse')->name('dayuse');

        Route::get('main-expired', 'ReportController@main_expired')->name('main-expired');

        Route::get('pt-expired', 'ReportController@pt_expired')->name('pt-expired');

        Route::get('export-main-expired', 'MembershipsController@exportMainExpired')->name('export-main-expired');
        Route::get('export-pt-expired-memberships', 'MembershipsController@exportPtExpired')->name('export-pt-expired');

        Route::get('tax-accountant', 'ReportController@taxAccountant')->name('tax-accountant');
        Route::get('tax-accountant-export', 'ReportController@taxAccountantExport')->name('tax-accountant.export');

        Route::get('customer-invitation-report', 'ReportController@customerInvitation')->name('customer-invitation');

        Route::get('all-duepayments-report', 'ReportController@all_due_payments')->name('all-due-payments');
        Route::get('sales-due-payments', 'ReportController@sales_due_payments')->name('sales_due_payments');
        Route::get('trainer-due-payments', 'ReportController@trainer_due_payments')->name('trainer_due_payments');
        Route::get('/trainers-by-branch', 'ReportController@getTrainersByBranch')->name('trainers.by.branch');
        Route::get('/get-sales-by-branch', 'ReportController@getSalesByBranch')->name('get.sales.by.branch');


        Route::get('daily-task-report', 'ReportController@daily_task_report')->name('daily-task-report');

        Route::get('actions-report', 'ReportController@action_report')->name('actions-report');
        Route::get('actions-report/export', 'ReportController@export_actions_report')->name('actions-report.export');

        // Trainer Payments
        Route::get('trainer-payments/{trainer}', 'HomeController@trainer_payments')->name('index.trainer-payments');
        // Trainer Reminders
        Route::get('trainer-reminders/{trainer}', 'HomeController@trainer_reminders')->name('index.trainer-reminders');

        Route::get('guest-log-report', 'ReportController@guest_log')->name('guest-log-report');
        Route::get('guest-log-report/export', 'ReportController@export_guest_log')->name('guest-log-report.export');

        Route::get('trainers-reminders', 'ReportController@trainer_reminders')->name('trainers-reminders');

        Route::get('trainers-reminder-actions', 'ReportController@trainers_reminder_actions')->name('trainers-reminder-actions');
        Route::get('trainers-reminders-history-actions', 'ReportController@trainers_reminder_history_actions')->name('trainers-reminder-history-actions');

        // Fitness manager report 23/11/2023
        Route::get('fitness-manager-report','ReportController@fitness_manager')->name('fitness-manager-report');
        Route::get('show-fitness-manager-report/{fitness_manager}','ReportController@show_fitness_manager')->name('show-fitness-manager-report');

        // PT Memberships report 27/11/2023
        Route::get('pt-attendances-report','ReportController@pt_attendances')->name('pt-attendances-report');

        // Sales Daily report 28/11/2023
        Route::get('sales-daily-report','ReportController@sales_daily')->name('sales-daily-report');

        // Trainer Daily report 29/11/2023
        Route::get('trainer-daily-report','ReportController@trainer_daily')->name('trainer-daily-report');


        Route::get('previous-month-report' ,'ReportController@previous_month_report')->name('previous-month-reports');
    });
    // Tasks
    Route::resource('tasks', 'TasksController');
    //Route::post('task-filter' , 'TasksController@filter')->name('filter-tasks');
    Route::get('my-tasks', 'TasksController@my_tasks')->name('tasks.my-tasks');
    Route::get('created-tasks', 'TasksController@created_tasks')->name('tasks.created-tasks');
    Route::get('done-tasks/{task}', 'TasksController@done_tasks')->name('tasks.done-tasks');
    Route::get('in-progress-tasks/{task}', 'TasksController@in_progress_tasks')->name('tasks.in-progress-tasks');
    
    Route::put('confirm-task/{task}', 'TasksController@confirm_task')->name('tasks.confirm-task');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');
    Route::get('edit-roles-permissions/{id}', 'RolesController@editRoles')->name('roles.edit-permissions');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Audit Logs
    Route::resource('audit-logs', 'AuditLogsController', ['except' => ['create', 'store', 'edit', 'update', 'destroy']]);

    // User Alerts
    Route::delete('user-alerts/destroy', 'UserAlertsController@massDestroy')->name('user-alerts.massDestroy');
    Route::get('user-alerts/read', 'UserAlertsController@read');
    Route::resource('user-alerts', 'UserAlertsController', ['except' => ['edit', 'update']]);

    // Faq Category
    Route::delete('faq-categories/destroy', 'FaqCategoryController@massDestroy')->name('faq-categories.massDestroy');
    Route::resource('faq-categories', 'FaqCategoryController');

    // Faq Question
    Route::delete('faq-questions/destroy', 'FaqQuestionController@massDestroy')->name('faq-questions.massDestroy');
    Route::resource('faq-questions', 'FaqQuestionController');

    // Action
    Route::delete('actions/destroy', 'ActionController@massDestroy')->name('actions.massDestroy');
    Route::post('actions/parse-csv-import', 'ActionController@parseCsvImport')->name('actions.parseCsvImport');
    Route::post('actions/process-csv-import', 'ActionController@processCsvImport')->name('actions.processCsvImport');
    Route::resource('actions', 'ActionController');

    // Status
    Route::delete('statuses/destroy', 'StatusController@massDestroy')->name('statuses.massDestroy');
    Route::post('statuses/parse-csv-import', 'StatusController@parseCsvImport')->name('statuses.parseCsvImport');
    Route::post('statuses/process-csv-import', 'StatusController@processCsvImport')->name('statuses.processCsvImport');
    Route::resource('statuses', 'StatusController');
    Route::get('status/{id}/{date}', 'StatusController@getStatus')->name('getStatus');

    // Branch
    Route::delete('branches/destroy', 'BranchController@massDestroy')->name('branches.massDestroy');
    Route::resource('branches', 'BranchController');
    Route::get('get-branch-accounts/{id}', 'BranchController@getBranchAccounts')->name('get-branch-accounts');

    // Source
    Route::delete('sources/destroy', 'SourceController@massDestroy')->name('sources.massDestroy');
    Route::post('sources/parse-csv-import', 'SourceController@parseCsvImport')->name('sources.parseCsvImport');
    Route::post('sources/process-csv-import', 'SourceController@processCsvImport')->name('sources.processCsvImport');
    Route::resource('sources', 'SourceController');

    // Address
    Route::delete('addresses/destroy', 'AddressController@massDestroy')->name('addresses.massDestroy');
    Route::post('addresses/parse-csv-import', 'AddressController@parseCsvImport')->name('addresses.parseCsvImport');
    Route::post('addresses/process-csv-import', 'AddressController@processCsvImport')->name('addresses.processCsvImport');
    Route::resource('addresses', 'AddressController');

    // Expenses Category
    Route::delete('expenses-categories/destroy', 'ExpensesCategoryController@massDestroy')->name('expenses-categories.massDestroy');
    Route::post('expenses-categories/parse-csv-import', 'ExpensesCategoryController@parseCsvImport')->name('expenses-categories.parseCsvImport');
    Route::post('expenses-categories/process-csv-import', 'ExpensesCategoryController@processCsvImport')->name('expenses-categories.processCsvImport');
    Route::resource('expenses-categories', 'ExpensesCategoryController');

    Route::get('expenses-export', 'ExpensesController@export')->name('expenses.export');

    // Service Types
    Route::delete('service-types/destroy', 'ServiceTypesController@massDestroy')->name('service-types.massDestroy');
    Route::post('service-types/parse-csv-import', 'ServiceTypesController@parseCsvImport')->name('service-types.parseCsvImport');
    Route::post('service-types/process-csv-import', 'ServiceTypesController@processCsvImport')->name('service-types.processCsvImport');
    Route::resource('service-types', 'ServiceTypesController');

    // Services
    Route::delete('services/destroy', 'ServicesController@massDestroy')->name('services.massDestroy');
    Route::post('services/parse-csv-import', 'ServicesController@parseCsvImport')->name('services.parseCsvImport');
    Route::post('services/process-csv-import', 'ServicesController@processCsvImport')->name('services.processCsvImport');
    Route::resource('services', 'ServicesController');
    Route::post('services/media', 'ServicesController@storeMedia')->name('services.storeMedia');


    // Pricelist
    Route::delete('pricelists/destroy', 'PricelistController@massDestroy')->name('pricelists.massDestroy');
    Route::post('pricelists/parse-csv-import', 'PricelistController@parseCsvImport')->name('pricelists.parseCsvImport');
    Route::post('pricelists/process-csv-import', 'PricelistController@processCsvImport')->name('pricelists.processCsvImport');
    // Route::resource('pricelists', 'PricelistController');

    Route::get('pricelists', 'PricelistController@index')->name('pricelists.index');
    Route::get('pricelists/create/{id}', 'PricelistController@create')->name('pricelists.create');
    Route::post('pricelists/store', 'PricelistController@store')->name('pricelists.store');
    Route::get('pricelists/edit/{pricelist}', 'PricelistController@edit')->name('pricelists.edit');
    Route::get('pricelists/show/{pricelist}', 'PricelistController@show')->name('pricelists.show');
    Route::put('pricelists/update/{pricelist}', 'PricelistController@update')->name('pricelists.update');
    Route::delete('pricelists/destroy/{pricelist}', 'PricelistController@destroy')->name('pricelists.destroy');
    Route::get('service-pricelists/{id}', 'PricelistController@servicePricelists')->name('service.pricelists');

    Route::get('getServicesByPricelist/{id}/{date}', 'PricelistController@getServicesByPricelist')->name('getServiceByPricelist');

    Route::get('get-pricelists-by-service-type/{id}', 'ServicesController@getPricelistByServiceType')->name('getPricelistsByServiceType');

    // Asset Types
    Route::delete('asset-types/destroy', 'AssetTypesController@massDestroy')->name('asset-types.massDestroy');
    Route::post('asset-types/parse-csv-import', 'AssetTypesController@parseCsvImport')->name('asset-types.parseCsvImport');
    Route::post('asset-types/process-csv-import', 'AssetTypesController@processCsvImport')->name('asset-types.processCsvImport');
    Route::resource('asset-types', 'AssetTypesController');

    // Leads
    Route::delete('leads/destroy', 'LeadsController@massDestroy')->name('leads.massDestroy');
    Route::post('leads/media', 'LeadsController@storeMedia')->name('leads.storeMedia');
    Route::post('leads/ckmedia', 'LeadsController@storeCKEditorImages')->name('leads.storeCKEditorImages');
    Route::post('leads/parse-csv-import', 'LeadsController@parseCsvImport')->name('leads.parseCsvImport');
    Route::post('leads/process-csv-import', 'LeadsController@processCsvImport')->name('leads.processCsvImport');
    Route::resource('leads', 'LeadsController');
    Route::post('lead/Import', 'LeadsController@import')->name('lead.import');

    Route::get('leads-export', 'LeadsController@export')->name('leads.export');

    Route::get('add-note/{id}', 'LeadsController@addNote')->name('note.create');
    Route::put('edit-note/{note}', 'LeadsController@updateNote')->name('note.update');
    Route::post('add-new-note/{id}', 'LeadsController@addNewNote')->name('note.store');
    Route::delete('delete-note/{id}', 'LeadsController@destroyNote')->name('note.destroy');

    Route::get('create-pop-message/{id}', 'MembersController@createPopMessage')->name('popMessage.create');
    Route::get('show-pop-message/{id}', 'MembersController@showPopMessage')->name('popMessage.show');
    Route::post('store-pop-message/{id}', 'MembersController@storePopMessage')->name('popMessage.store');
    Route::delete('delete-pop-message/{id}', 'MembersController@destroyPopMessage')->name('popMessage.destroy');
    Route::post('store-pop-message-reply/{id}', 'MembersController@storePopMessageReply')->name('popMessageReply.store');

    Route::patch('block-member/{member}', 'MembersController@block')->name('member.block');

    // Members
    Route::delete('members/destroy', 'MembersController@massDestroy')->name('members.massDestroy');
    Route::post('members/media', 'MembersController@storeMedia')->name('members.storeMedia');
    Route::post('members/ckmedia', 'MembersController@storeCKEditorImages')->name('members.storeCKEditorImages');
    Route::post('members/parse-csv-import', 'MembersController@parseCsvImport')->name('members.parseCsvImport');
    Route::post('members/process-csv-import', 'MembersController@processCsvImport')->name('members.processCsvImport');
    Route::resource('members', 'MembersController');

    Route::get('transfer-to-branch/{member}', 'MembersController@transfer_to_branch')->name('members.transfer-to-branch');
    Route::put('store-transfer-to-branch/{member}', 'MembersController@store_transfer_to_branch')->name('members.store-transfer-to-branch');

    Route::get('member/{id}', 'MembersController@transfer')->name('member.transfer');
    Route::put('member/{id}/storeTransfer', 'MembersController@storeTransfer')->name('member.storeTransfer');

    Route::get('members-export', 'MembersController@export')->name('members.export');
    Route::post('take_attend', 'AttendanceController@takeAttend')->name('take.attend');

    Route::post('take_free_session', 'AttendanceController@freeSession')->name('take.freeSession');

    Route::post('take_manual_attend/{id}', 'AttendanceController@takeManualAttend')->name('take_manual_attend');

    Route::get('member/{id}/add_membership', 'MembersController@addMembership')->name('member.addMembership');
    Route::post('member/{id}/add_membership', 'MembersController@addNewMembership')->name('member.addNewMembership');

    Route::post('member/send-Message/{id}', 'MembersController@sendMessage')->name('member.sendSms');

    Route::get('member/{id}/edit-card-number', 'MembersController@editCardNumber')->name('cardNumber.edit');
    Route::put('member/{id}/update-card-number', 'MembersController@updateCardNumber')->name('cardNumber.update');

    Route::get('invitations', 'MembersController@invitations')->name('invitations.index');
    Route::get('invitations/export', 'MembersController@exportInvitations')->name('invitations.export');

    Route::get('active-members', 'MembersController@activeMembers')->name('members.active');
    Route::get('on-hold-members', 'MembersController@onHoldMembers')->name('members.onhold');

    Route::get('active-members-export', 'MembersController@exportActiveMembers')->name('activeMembers.export');

    Route::get('inactive-members', 'MembersController@inactiveMembers')->name('members.inactive');
    Route::get('onhold-export', 'MembersController@exportOnhold')->name('onhold.export');


    Route::post('search/members', 'LeadsController@searchMember')->name('searchMember');
    // Memberships
    Route::delete('memberships/destroy', 'MembershipsController@massDestroy')->name('memberships.massDestroy');
    Route::post('memberships/parse-csv-import', 'MembershipsController@parseCsvImport')->name('memberships.parseCsvImport');
    Route::post('memberships/process-csv-import', 'MembershipsController@processCsvImport')->name('MembershipsController.processCsvImport');
    Route::resource('memberships', 'MembershipsController');
    Route::get('all-memberships/{type}', 'MembershipsController@allMemberships')->name('all-memberships.index');
    Route::get('assigned-memberships', 'MembershipsController@assigned_memberships')->name('assigned-memberships');

    Route::get('memberships/{membership}/manual-attend', 'MembershipsController@manualAttend')->name('memberships.manual_attend');

    Route::get('memberships/fix', 'MembershipsController@fix')->name('MembershipsController.fix');
    Route::get('memberships/adjust/{id}', 'MembershipsController@adjust')->name('MembershipsController.adjust');

    Route::get('memberships-export', 'MembershipsController@exportMemberships')->name('memberships.export');
    Route::get('export-expiring-expired', 'MembershipsController@export_expiring_expired')->name('export-expiring-expired');

    Route::get('freeze_requests/{id}', 'MembershipsController@freezeRequests')->name('membership.freezeRequests');

    Route::get('renew_membership/{id}', 'MembershipsController@renew')->name('membership.renew');
    Route::post('renew_membership', 'MembershipsController@storeRenew')->name('membership.storeRenew');

    Route::get('upgrade_membership/{id}', 'MembershipsController@upgrade')->name('membership.upgrade');
    Route::put('upgrade_membership/{id}', 'MembershipsController@storeUpgrade')->name('membership.storeUpgrade');

    Route::get('downgrade_membership/{id}', 'MembershipsController@downgrade')->name('membership.downgrade');
    Route::put('downgrade_membership/{id}', 'MembershipsController@storeDowngrade')->name('membership.storeDowngrade');

    Route::get('transfer_membership/{id}', 'MembershipsController@transferMembership')->name('membership.transfer');
    Route::put('transfer_membership/{id}', 'MembershipsController@storeTransferMembership')->name('membership.storeTransfer');

    Route::post('get-member-code', 'MembershipsController@getMember')->name('getMember');
    Route::post('take-freeze-attend', 'AttendanceController@takeFreezeAttend')->name('freeze-attend.take');

    Route::resource('trainer-services', 'TrainerServiceController');
    Route::get('single-trainer-services', 'TrainerServiceController@getServiceData')->name('single-trainer-service.get');

    Route::resource('sports', 'SportController');

    Route::post('referral_member', 'LeadsController@referralMember')->name('referralMember');

    Route::get('expired-memberships', 'MembershipsController@expiredMemberships')->name('membership.expired');
    Route::get('expired-memberships-extra', 'MembershipsController@expiredMembershipsExtra')->name('membership.expiredExtra');
    Route::get('expiring-expired-memberships', 'MembershipsController@expiring_expired')->name('membership.expiring-expired');
    Route::post('reminder-expired-memberships', 'MembershipsController@reminderExpiredMemberships')->name('reminder.expiredMemberships');

    // Route::post('membership-service-option', 'MembershipsController@addMembershipServiceOption')->name('addMembershipServiceOption');

    Route::get('assign-coach-to-membership/{membership}', 'MembershipsController@assign_coach_to_non_pt_mempers')->name('assign-coach-to-membership.memberships');
    Route::post('assign-coach', 'MembershipsController@assign')->name('assign-coach.memberships');
    Route::put('assign-trainer/{membership}', 'MembershipsController@assignTrainer')->name('assign-trainer');

    Route::get('membership-service-option/{membership_id}/{service_option_pricelist_id}', 'MembershipsController@addMembershipServiceOption')->name('addMembershipServiceOption');
    // Locker
    Route::delete('lockers/destroy', 'LockerController@massDestroy')->name('lockers.massDestroy');
    Route::post('lockers/parse-csv-import', 'LockerController@parseCsvImport')->name('lockers.parseCsvImport');
    Route::post('lockers/process-csv-import', 'LockerController@processCsvImport')->name('lockers.processCsvImport');
    Route::resource('lockers', 'LockerController');

    // Membership Attendance
    Route::delete('membership-attendances/destroy', 'MembershipAttendanceController@massDestroy')->name('membership-attendances.massDestroy');
    Route::post('membership-attendances/parse-csv-import', 'MembershipAttendanceController@parseCsvImport')->name('membership-attendances.parseCsvImport');
    Route::post('membership-attendances/process-csv-import', 'MembershipAttendanceController@processCsvImport')->name('membership-attendances.processCsvImport');
    Route::resource('membership-attendances', 'MembershipAttendanceController');
    Route::get('export-membership-attendances', 'MembershipAttendanceController@export')->name('membership_attendances.export');

    // Expenses
    Route::delete('expenses/destroy', 'ExpensesController@massDestroy')->name('expenses.massDestroy');
    Route::post('expenses/parse-csv-import', 'ExpensesController@parseCsvImport')->name('expenses.parseCsvImport');
    Route::post('expenses/process-csv-import', 'ExpensesController@processCsvImport')->name('expenses.processCsvImport');
    Route::resource('expenses', 'ExpensesController');
    Route::get('expenses_categories', 'ExpensesController@expenses_categories')->name('expenses_categories');
    Route::get('expenses_categories_show_by_filter' ,'ExpensesController@expenses_categories_show_by_filter')->name('expenses_categories_show_by_filter');


    // Invoice
    Route::delete('invoices/destroy', 'InvoiceController@massDestroy')->name('invoices.massDestroy');
    Route::post('invoices/parse-csv-import', 'InvoiceController@parseCsvImport')->name('invoices.parseCsvImport');
    Route::post('invoices/process-csv-import', 'InvoiceController@processCsvImport')->name('invoices.processCsvImport');
    Route::resource('invoices', 'InvoiceController');
    Route::get('invoices/settled', 'InvoiceController@settled')->name('invoices.settled');
    Route::post('update-reviewed-status/{invoice}', 'InvoiceController@updateReviewedStatus')->name('invoice-reviewed-status.update');

    Route::post('settlement-invoice/{id}', 'InvoiceController@settlementInvoice')->name('settlement.invoice');

    Route::get('partial-invoices', 'InvoiceController@partial')->name('invoices.partial');
    Route::get('settlement-invoices', 'InvoiceController@settlement')->name('invoices.settlement');

    Route::get('due-payments-invoices/{id}', 'InvoiceController@duePaymentsInvoices')->name('invoice.duePayments');
    Route::get('invoice-payments/{id}', 'InvoiceController@payments')->name('invoice.payments');

    Route::get('fix_invoices', 'InvoiceController@fixInvoices')->name('fixInvoices');

    Route::get('invoices-export', 'InvoiceController@export')->name('invoices.export');
    Route::get('partial-invoices-export', 'InvoiceController@exportPartial')->name('partial-invoices.export');

    Route::get('showSupervisor/{id}/{alt_id}', 'InvoiceController@showSupervisor')->name('invoice.showSupervisor');

    Route::post('print-invoice/{id}', 'InvoiceController@printInvoice')->name('invoice.print');
    Route::post('printInvoice/{id}/{alt_id}', 'InvoiceController@printInvoiceSupervisor')->name('printInvoiceSupervisor');

    Route::post('download-invoice/{id}', 'InvoiceController@downloadInvoice')->name('invoice.download');
    Route::post('send-invoice-whatsapp/{id}', 'InvoiceController@sendInvoice')->name('invoice.send');

    Route::get('refund-invoice/{id}', 'InvoiceController@refund')->name('invoice.refund');
    Route::post('refund-invoice/{id}', 'InvoiceController@storeRefund')->name('invoice.storeRefund');

    Route::get('payment-invoice/{id}', 'InvoiceController@payment')->name('invoice.payment');
    Route::post('payment-invoice/{id}', 'InvoiceController@storePayment')->name('invoice.storePayment');
    Route::get('paymentDuePayments-invoice/{id}', 'InvoiceController@paymentDuePayments')->name('invoice.paymentDuePayments');

    // Payment
    Route::delete('payments/destroy', 'PaymentController@massDestroy')->name('payments.massDestroy');
    Route::post('payments/parse-csv-import', 'PaymentController@parseCsvImport')->name('payments.parseCsvImport');
    Route::post('payments/process-csv-import', 'PaymentController@processCsvImport')->name('payments.processCsvImport');
    Route::resource('payments', 'PaymentController');

    Route::get('payments-export', 'PaymentController@export')->name('payments.export');

    // Employees
    Route::delete('employees/destroy', 'EmployeesController@massDestroy')->name('employees.massDestroy');
    Route::post('employees/parse-csv-import', 'EmployeesController@parseCsvImport')->name('employees.parseCsvImport');
    Route::post('employees/process-csv-import', 'EmployeesController@processCsvImport')->name('employees.processCsvImport');
    Route::resource('employees', 'EmployeesController');
    Route::post('employees/media', 'EmployeesController@storeMedia')->name('employees.storeMedia');

    Route::get('transfer-sales-data', 'EmployeesController@transferSalesData')->name('transfer_sales_data.index');
    Route::post('transfer-sales-data', 'EmployeesController@storeTransferSalesData')->name('transfer_sales_data.store');
    Route::post('transfer-sales-data/import', 'EmployeesController@importSalesData')->name('importSalesData');

    Route::get('add-bonus/{id}', 'EmployeesController@add_bonus')->name('employees.add_bonus');
    Route::get('add-deduction/{id}', 'EmployeesController@add_deduction')->name('employees.add_deduction');
    Route::get('add-loan/{id}', 'EmployeesController@add_loan')->name('employees.add_loan');
    Route::get('add-vacation/{id}', 'EmployeesController@add_vacation')->name('employees.add_vacation');
    Route::get('add-document/{id}', 'EmployeesController@add_document')->name('employees.add_document');

    Route::put('employee/{id}/change-status', 'EmployeesController@change_status')->name('employees.change_status');
    Route::put('employee/{id}/change-mobile-status', 'EmployeesController@change_mobile_status')->name('employees.change_mobile_status');


    // Employee attendances
    Route::get('employee-attendances', 'EmployeesController@employee_attendances')->name('employee_attendances');
    Route::put('employee/sign-in-out', 'EmployeesController@employee_sign_in_out')->name('employee-sign-in-out');
    Route::post('employee-attendance/post', 'EmployeesController@take_employee_attendance')->name('take_employee_attendance');
    Route::get('employee-attendance-export', 'EmployeeAttendanceController@export')->name('employees-attendances.export');

    // Payroll
    Route::get('employees-payroll', 'PayrollController@payroll')->name('payroll.get');
    Route::get('employees-payroll/{payroll}', 'EmployeesController@showSinglePayroll')->name('payroll.show');
    Route::post('print-payroll/{id}', 'EmployeesController@printPayroll')->name('payroll.print');
    Route::put('payroll/status/{id}', 'EmployeesController@payrollStatus')->name('payroll.status');
    Route::put('payroll/confirm-all', 'PayrollController@confirm_all')->name('payroll.confirm_all');
    Route::get('payroll-export', 'PayrollController@export')->name('payroll.export');
    Route::get('fixedComission/{id}', 'EmployeesController@fixedComission')->name('payroll.fixedComission');
    Route::get('percentageComission/{id}', 'EmployeesController@percentageComission')->name('payroll.percentageComission');

    // Bonuses
    Route::delete('bonus/destroy', 'BonusesController@massDestroy')->name('bonus.massDestroy');
    Route::post('bonus/parse-csv-import', 'BonusesController@parseCsvImport')->name('bonus.parseCsvImport');
    Route::post('bonus/process-csv-import', 'BonusesController@processCsvImport')->name('bonus.processCsvImport');
    Route::resource('bonus', 'BonusesController');

    // Deductions
    Route::delete('deductions/destroy', 'DeductionsController@massDestroy')->name('deductions.massDestroy');
    Route::post('deductions/parse-csv-import', 'DeductionsController@parseCsvImport')->name('deductions.parseCsvImport');
    Route::post('deductions/process-csv-import', 'DeductionsController@processCsvImport')->name('deductions.processCsvImport');
    Route::resource('deductions', 'DeductionsController');

    Route::get('master-data', 'SettingController@masterData')->name('master-data.index');

    // Loans
    Route::delete('loans/destroy', 'LoansController@massDestroy')->name('loans.massDestroy');
    Route::post('loans/parse-csv-import', 'LoansController@parseCsvImport')->name('loans.parseCsvImport');
    Route::post('loans/process-csv-import', 'LoansController@processCsvImport')->name('loans.processCsvImport');
    Route::resource('loans', 'LoansController');

    // Vacations
    Route::delete('vacations/destroy', 'VacationsController@massDestroy')->name('vacations.massDestroy');
    Route::post('vacations/parse-csv-import', 'VacationsController@parseCsvImport')->name('vacations.parseCsvImport');
    Route::post('vacations/process-csv-import', 'VacationsController@processCsvImport')->name('vacations.processCsvImport');
    Route::resource('vacations', 'VacationsController');

    // Documents
    Route::delete('documents/destroy', 'DocumentsController@massDestroy')->name('documents.massDestroy');
    Route::post('documents/media', 'DocumentsController@storeMedia')->name('documents.storeMedia');
    Route::post('documents/ckmedia', 'DocumentsController@storeCKEditorImages')->name('documents.storeCKEditorImages');
    Route::post('documents/parse-csv-import', 'DocumentsController@parseCsvImport')->name('documents.parseCsvImport');
    Route::post('documents/process-csv-import', 'DocumentsController@processCsvImport')->name('documents.processCsvImport');
    Route::resource('documents', 'DocumentsController');

    // Employee Settings
    Route::delete('employee-settings/destroy', 'EmployeeSettingsController@massDestroy')->name('employee-settings.massDestroy');
    Route::post('employee-settings/parse-csv-import', 'EmployeeSettingsController@parseCsvImport')->name('employee-settings.parseCsvImport');
    Route::post('employee-settings/process-csv-import', 'EmployeeSettingsController@processCsvImport')->name('employee-settings.processCsvImport');
    Route::resource('employee-settings', 'EmployeeSettingsController');

    // Schedule templates
    Route::resource('schedule-templates', 'ScheduleTemplateController');
    Route::resource('attendance-settings', 'AttendanceSettingController');
    Route::resource('employee-attendance', 'EmployeeAttendanceController');
    Route::get('schedule-templates/{id}/get', 'ScheduleTemplateController@getScheduleById')->name('schedule-templates.get');

    // Hotdeals
    Route::delete('hotdeals/destroy', 'HotdealsController@massDestroy')->name('hotdeals.massDestroy');
    Route::post('hotdeals/media', 'HotdealsController@storeMedia')->name('hotdeals.storeMedia');
    Route::post('hotdeals/ckmedia', 'HotdealsController@storeCKEditorImages')->name('hotdeals.storeCKEditorImages');
    Route::post('hotdeals/parse-csv-import', 'HotdealsController@parseCsvImport')->name('hotdeals.parseCsvImport');
    Route::post('hotdeals/process-csv-import', 'HotdealsController@processCsvImport')->name('hotdeals.processCsvImport');
    Route::resource('hotdeals', 'HotdealsController');

    // Gallery Section
    Route::delete('gallery-sections/destroy', 'GallerySectionController@massDestroy')->name('gallery-sections.massDestroy');
    Route::post('gallery-sections/parse-csv-import', 'GallerySectionController@parseCsvImport')->name('gallery-sections.parseCsvImport');
    Route::post('gallery-sections/process-csv-import', 'GallerySectionController@processCsvImport')->name('gallery-sections.processCsvImport');
    Route::resource('gallery-sections', 'GallerySectionController');

    // Gallery
    Route::delete('galleries/destroy', 'GalleryController@massDestroy')->name('galleries.massDestroy');
    Route::post('galleries/media', 'GalleryController@storeMedia')->name('galleries.storeMedia');
    Route::post('galleries/ckmedia', 'GalleryController@storeCKEditorImages')->name('galleries.storeCKEditorImages');
    Route::post('galleries/parse-csv-import', 'GalleryController@parseCsvImport')->name('galleries.parseCsvImport');
    Route::post('galleries/process-csv-import', 'GalleryController@processCsvImport')->name('galleries.processCsvImport');
    Route::resource('galleries', 'GalleryController');

    // Video Section
    Route::delete('video-sections/destroy', 'VideoSectionController@massDestroy')->name('video-sections.massDestroy');
    Route::post('video-sections/parse-csv-import', 'VideoSectionController@parseCsvImport')->name('video-sections.parseCsvImport');
    Route::post('video-sections/process-csv-import', 'VideoSectionController@processCsvImport')->name('video-sections.processCsvImport');
    Route::resource('video-sections', 'VideoSectionController');

    // Sales Plan
    Route::delete('sales-plans/destroy', 'SalesPlanController@massDestroy')->name('sales-plans.massDestroy');
    Route::post('sales-plans/parse-csv-import', 'SalesPlanController@parseCsvImport')->name('sales-plans.parseCsvImport');
    Route::post('sales-plans/process-csv-import', 'SalesPlanController@processCsvImport')->name('sales-plans.processCsvImport');
    Route::resource('sales-plans', 'SalesPlanController');

    // Video
    Route::delete('videos/destroy', 'VideoController@massDestroy')->name('videos.massDestroy');
    Route::post('videos/media', 'VideoController@storeMedia')->name('videos.storeMedia');
    Route::post('videos/ckmedia', 'VideoController@storeCKEditorImages')->name('videos.storeCKEditorImages');
    Route::post('videos/parse-csv-import', 'VideoController@parseCsvImport')->name('videos.parseCsvImport');
    Route::post('videos/process-csv-import', 'VideoController@processCsvImport')->name('videos.processCsvImport');
    Route::resource('videos', 'VideoController');

    // Sales Tiers
    Route::delete('sales-tiers/destroy', 'SalesTiersController@massDestroy')->name('sales-tiers.massDestroy');
    Route::post('sales-tiers/parse-csv-import', 'SalesTiersController@parseCsvImport')->name('sales-tiers.parseCsvImport');
    Route::post('sales-tiers/process-csv-import', 'SalesTiersController@processCsvImport')->name('sales-tiers.processCsvImport');
    Route::resource('sales-tiers', 'SalesTiersController');
    Route::put('sales-tiers-transfer/{id}', 'SalesTiersController@transferToNextMonth')->name('sales-tiers.transfer');
    Route::get('sales-tierz/{user_type}', 'SalesTiersController@getUsersWithType')->name('users_get_with_type');
    Route::get('sales-tier/{id}', 'SalesTiersController@getSalesTierDetails')->name('sales-tier.get_details');
    // Newssection
    Route::delete('newssections/destroy', 'NewssectionController@massDestroy')->name('newssections.massDestroy');
    Route::post('newssections/parse-csv-import', 'NewssectionController@parseCsvImport')->name('newssections.parseCsvImport');
    Route::post('newssections/process-csv-import', 'NewssectionController@processCsvImport')->name('newssections.processCsvImport');
    Route::resource('newssections', 'NewssectionController');

    // News
    Route::delete('news/destroy', 'NewsController@massDestroy')->name('news.massDestroy');
    Route::post('news/media', 'NewsController@storeMedia')->name('news.storeMedia');
    Route::post('news/ckmedia', 'NewsController@storeCKEditorImages')->name('news.storeCKEditorImages');
    Route::post('news/parse-csv-import', 'NewsController@parseCsvImport')->name('news.parseCsvImport');
    Route::post('news/process-csv-import', 'NewsController@processCsvImport')->name('news.processCsvImport');
    Route::resource('news', 'NewsController');

    // Sales Intensive
    Route::delete('sales-intensives/destroy', 'SalesIntensiveController@massDestroy')->name('sales-intensives.massDestroy');
    Route::post('sales-intensives/parse-csv-import', 'SalesIntensiveController@parseCsvImport')->name('sales-intensives.parseCsvImport');
    Route::post('sales-intensives/process-csv-import', 'SalesIntensiveController@processCsvImport')->name('sales-intensives.processCsvImport');
    Route::resource('sales-intensives', 'SalesIntensiveController');

    // Assets
    Route::delete('assets/destroy', 'AssetsController@massDestroy')->name('assets.massDestroy');
    Route::post('assets/media', 'AssetsController@storeMedia')->name('assets.storeMedia');
    Route::post('assets/ckmedia', 'AssetsController@storeCKEditorImages')->name('assets.storeCKEditorImages');
    Route::post('assets/parse-csv-import', 'AssetsController@parseCsvImport')->name('assets.parseCsvImport');
    Route::post('assets/process-csv-import', 'AssetsController@processCsvImport')->name('assets.processCsvImport');
    Route::resource('assets', 'AssetsController');

    // Maintenance Vendors
    Route::delete('maintenance-vendors/destroy', 'MaintenanceVendorsController@massDestroy')->name('maintenance-vendors.massDestroy');
    Route::post('maintenance-vendors/parse-csv-import', 'MaintenanceVendorsController@parseCsvImport')->name('maintenance-vendors.parseCsvImport');
    Route::post('maintenance-vendors/process-csv-import', 'MaintenanceVendorsController@processCsvImport')->name('maintenance-vendors.processCsvImport');
    Route::resource('maintenance-vendors', 'MaintenanceVendorsController');

    // Member Status
    Route::delete('member-statuses/destroy', 'MemberStatusController@massDestroy')->name('member-statuses.massDestroy');
    Route::post('member-statuses/parse-csv-import', 'MemberStatusController@parseCsvImport')->name('member-statuses.parseCsvImport');
    Route::post('member-statuses/process-csv-import', 'MemberStatusController@processCsvImport')->name('member-statuses.processCsvImport');
    Route::resource('member-statuses', 'MemberStatusController');
    Route::get('memberStatus/{id}/{date}', 'MemberStatusController@getMemberStatus')->name('getMemberStatus');

    // Assets Maintenance
    Route::delete('assets-maintenances/destroy', 'AssetsMaintenanceController@massDestroy')->name('assets-maintenances.massDestroy');
    Route::post('assets-maintenances/parse-csv-import', 'AssetsMaintenanceController@parseCsvImport')->name('assets-maintenances.parseCsvImport');
    Route::post('assets-maintenances/process-csv-import', 'AssetsMaintenanceController@processCsvImport')->name('assets-maintenances.processCsvImport');
    Route::resource('assets-maintenances', 'AssetsMaintenanceController');

    // Reminders
    Route::delete('reminders/destroy', 'RemindersController@massDestroy')->name('reminders.massDestroy');
    Route::post('reminders/parse-csv-import', 'RemindersController@parseCsvImport')->name('reminders.parseCsvImport');
    Route::post('reminders/process-csv-import', 'RemindersController@processCsvImport')->name('reminders.processCsvImport');
    Route::resource('reminders', 'RemindersController');
    Route::post('assign-reminder/{id}', 'RemindersController@assign_reminder')->name('reminder.assign');
    Route::post('take-trainer-reminder/{member}', 'RemindersController@take_trainer_reminder')->name('take-trainer-reminder');

    Route::get('reminders-export', 'RemindersController@export')->name('reminders.export');
    Route::get('upcomming-reminders-export', 'RemindersController@exportUpcomming')->name('upcomming-reminders.export');
    Route::get('overdue-reminders-export', 'RemindersController@exportOverdue')->name('overdue-reminders.export');

    Route::get('reminders-histories', 'RemindersController@remindersHistory')->name('remindersHistory.index');

    Route::delete('delete-reminder-history/{id}', 'RemindersController@destroyReminderhistory')->name('reminderHistory.destroy');
    Route::delete('delete-invitation/{id}', 'AttendanceController@deleteInvitation')->name('invitation.destroy');

    Route::get('upcomming-reminders', 'RemindersController@upcomming')->name('reminders.upcomming');
    Route::get('overdue-reminders', 'RemindersController@overdue')->name('reminders.overdue');

    Route::get('reminders-management', 'RemindersController@remindersManagement')->name('reminders.management');

    Route::post('upload-reminders-management', 'RemindersController@importReminders')->name('importReminders');

    Route::post('take-lead-action/{id}', 'RemindersController@takeLeadAction')->name('reminders.takeLeadAction');
    Route::post('take-member-action/{id}', 'RemindersController@takeMemberAction')->name('reminders.takeMemberAction');
    Route::post('lead-action/{id}', 'RemindersController@leadAction')->name('leadAction');



    // Service Option
    Route::delete('service-options/destroy', 'ServiceOptionController@massDestroy')->name('service-options.massDestroy');
    Route::post('service-options/parse-csv-import', 'ServiceOptionController@parseCsvImport')->name('service-options.parseCsvImport');
    Route::post('service-options/process-csv-import', 'ServiceOptionController@processCsvImport')->name('service-options.processCsvImport');
    Route::resource('service-options', 'ServiceOptionController');

    // Service Options Pricelist
    Route::delete('service-options-pricelists/destroy', 'ServiceOptionsPricelistController@massDestroy')->name('service-options-pricelists.massDestroy');
    Route::post('service-options-pricelists/parse-csv-import', 'ServiceOptionsPricelistController@parseCsvImport')->name('service-options-pricelists.parseCsvImport');
    Route::post('service-options-pricelists/process-csv-import', 'ServiceOptionsPricelistController@processCsvImport')->name('service-options-pricelists.processCsvImport');
    Route::resource('service-options-pricelists', 'ServiceOptionsPricelistController');

    // Freeze Request
    Route::delete('freeze-requests/destroy', 'FreezeRequestController@massDestroy')->name('freeze-requests.massDestroy');
    Route::post('freeze-requests/parse-csv-import', 'FreezeRequestController@parseCsvImport')->name('freeze-requests.parseCsvImport');
    Route::post('freeze-requests/process-csv-import', 'FreezeRequestController@processCsvImport')->name('freeze-requests.processCsvImport');
    Route::resource('freeze-requests', 'FreezeRequestController');
    Route::get('freezes', 'FreezeRequestController@freeze')->name('freeze.index');

    Route::get('freezes', 'FreezeRequestController@freeze')->name('freeze.index');
    Route::get('freezes-export', 'FreezeRequestController@export')->name('freezes.export');

    Route::get('getMembershipDetails/{id}/{date}/{freeze}', 'FreezeRequestController@getMembershipDetails')->name('getMembershipDetails');

    Route::put('freeze-requests/confirm/{id}', 'FreezeRequestController@confirm')->name('freeze-requests.confirm');
    Route::put('freeze-requests/reject/{id}', 'FreezeRequestController@reject')->name('freeze-requests.reject');

    // Refund Reasons
    Route::delete('refund-reasons/destroy', 'RefundReasonsController@massDestroy')->name('refund-reasons.massDestroy');
    Route::post('refund-reasons/parse-csv-import', 'RefundReasonsController@parseCsvImport')->name('refund-reasons.parseCsvImport');
    Route::post('refund-reasons/process-csv-import', 'RefundReasonsController@processCsvImport')->name('refund-reasons.processCsvImport');
    Route::resource('refund-reasons', 'RefundReasonsController');

    // Refund
    Route::delete('refunds/destroy', 'RefundController@massDestroy')->name('refunds.massDestroy');
    Route::post('refunds/parse-csv-import', 'RefundController@parseCsvImport')->name('refunds.parseCsvImport');
    Route::post('refunds/process-csv-import', 'RefundController@processCsvImport')->name('refunds.processCsvImport');
    Route::resource('refunds', 'RefundController');

    Route::get('refunds-export', 'RefundController@export')->name('refunds.export');

    Route::get('refund-requests', 'RefundController@requests')->name('refund.requests');
    Route::put('refund-requests/approve/{id}', 'RefundController@approve')->name('refund.approve');
    Route::put('refund-requests/confirm/{id}', 'RefundController@confirm')->name('refund.confirm');
    Route::put('refund-requests/reject/{id}', 'RefundController@reject')->name('refund.reject');

    // Accounts
    Route::delete('accounts/destroy', 'AccountsController@massDestroy')->name('accounts.massDestroy');
    Route::post('accounts/parse-csv-import', 'AccountsController@parseCsvImport')->name('accounts.parseCsvImport');
    Route::post('accounts/process-csv-import', 'AccountsController@processCsvImport')->name('accounts.processCsvImport');
    Route::resource('accounts', 'AccountsController');
    Route::get('account-statement/{id}', 'AccountsController@statement')->name('account.statement');
    Route::post('getAccountsByAmount', 'AccountsController@getAccountsByAmount')->name('getAccountsByAmount');
    Route::get('update-account-balance/{account}', 'AccountsController@update_account_balance')->name('update-account-balance');

    Route::get('account-transfer/{id}', 'AccountsController@transfer')->name('account.transfer');
    Route::post('store-account-transfer/{id}', 'AccountsController@storeTransfer')->name('account-transfer.store');

    Route::get('transactions', 'AccountsController@transactions')->name('transactions.index');

    // External Payment
    Route::delete('external-payments/destroy', 'ExternalPaymentController@massDestroy')->name('external-payments.massDestroy');
    Route::post('external-payments/parse-csv-import', 'ExternalPaymentController@parseCsvImport')->name('external-payments.parseCsvImport');
    Route::post('external-payments/process-csv-import', 'ExternalPaymentController@processCsvImport')->name('external-payments.processCsvImport');
    Route::resource('external-payments', 'ExternalPaymentController');

    // Withdrawal
    Route::delete('withdrawals/destroy', 'WithdrawalController@massDestroy')->name('withdrawals.massDestroy');
    Route::post('withdrawals/parse-csv-import', 'WithdrawalController@parseCsvImport')->name('withdrawals.parseCsvImport');
    Route::post('withdrawals/process-csv-import', 'WithdrawalController@processCsvImport')->name('withdrawals.processCsvImport');
    Route::resource('withdrawals', 'WithdrawalController');

    // Timeslot
    Route::delete('timeslots/destroy', 'TimeslotController@massDestroy')->name('timeslots.massDestroy');
    Route::resource('timeslots', 'TimeslotController');

    // Session List
    Route::delete('session-lists/destroy', 'SessionListController@massDestroy')->name('session-lists.massDestroy');
    Route::post('session-lists/media', 'SessionListController@storeMedia')->name('session-lists.storeMedia');
    Route::post('session-lists/ckmedia', 'SessionListController@storeCKEditorImages')->name('session-lists.storeCKEditorImages');
    Route::post('session-lists/parse-csv-import', 'SessionListController@parseCsvImport')->name('session-lists.parseCsvImport');
    Route::post('session-lists/process-csv-import', 'SessionListController@processCsvImport')->name('session-lists.processCsvImport');
    Route::resource('session-lists', 'SessionListController');

    // Schedule
    Route::delete('schedules/destroy', 'ScheduleController@massDestroy')->name('schedules.massDestroy');
    Route::post('schedules/parse-csv-import', 'ScheduleController@parseCsvImport')->name('schedules.parseCsvImport');
    Route::post('schedules/process-csv-import', 'ScheduleController@processCsvImport')->name('schedules.processCsvImport');
    Route::resource('schedules', 'ScheduleController');

    // Schedule Main Group
    Route::resource('schedule-main-groups', 'ScheduleMainGroupController');

    // Schedule Main
    Route::resource('schedule-mains', 'ScheduleMainController');

    Route::put('schedule-main-change-status/{scheduleMain}', 'ScheduleMainController@change_status')->name('schedule-mains.change-status');

    // Reasons
    Route::delete('reasons/destroy', 'ReasonsController@massDestroy')->name('reasons.massDestroy');
    Route::post('reasons/media', 'ReasonsController@storeMedia')->name('reasons.storeMedia');
    Route::post('reasons/ckmedia', 'ReasonsController@storeCKEditorImages')->name('reasons.storeCKEditorImages');
    Route::resource('reasons', 'ReasonsController');

    Route::get('sales-dashboard', 'HomeController@sales')->name('sales.dashboard');

    Route::get('help-center', 'HelpcenterController@index')->name('helpcenter.index');

    //Member Suggestion
    Route::resource('member-suggestion', 'MemberSuggestionController');

    //Rules 
    Route::resource('rules', 'RulesController');

    Route::get('/change-invoice', 'InvoiceController@changeInvoice')->name('changeInvoice');
    Route::post('/store-change-invoice', 'InvoiceController@storeChangeInvoice')->name('storeChangeInvoice');

    //Ratings 
    Route::resource('ratings', 'RatingController');

    // Warehouse
    Route::delete('warehouses/destroy', 'WarehouseController@massDestroy')->name('warehouses.massDestroy');
    Route::resource('warehouses', 'WarehouseController');

    // Products
    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');
    Route::post('products/media', 'ProductsController@storeMedia')->name('products.storeMedia');
    Route::post('products/ckmedia', 'ProductsController@storeCKEditorImages')->name('products.storeCKEditorImages');
    Route::resource('products', 'ProductsController');
    Route::get('products/transactions/{id}', 'ProductsController@transactions')->name('product.transactions');


    // Warehouse Products
    Route::delete('warehouse-products/destroy', 'WarehouseProductsController@massDestroy')->name('warehouse-products.massDestroy');
    Route::resource('warehouse-products', 'WarehouseProductsController');
    Route::get('warehouse-product/{id}', 'WarehouseProductsController@getWarehouseProduct')->name('getWarehouseProduct');

    // Master Card
    Route::delete('master-cards/destroy', 'MasterCardController@massDestroy')->name('master-cards.massDestroy');
    Route::resource('master-cards', 'MasterCardController');

    // Invitation
    Route::post('invitation/store', 'AttendanceController@invitation')->name('invitation');

    // external payment category
    Route::resource('external-payment-categories', 'ExternalPaymentCategoryController');

    //control 
    Route::get('hot-keys', 'ControlPanelController@hot_keys')->name('hot-keys');
    Route::get('operations', 'ControlPanelController@operations')->name('operations');
    Route::get('master', 'ControlPanelController@master_data')->name('master-data');
    Route::get('hr', 'ControlPanelController@hr')->name('hr');
    Route::get('mobile', 'ControlPanelController@mobile')->name('mobile');
    Route::get('task-management', 'ControlPanelController@taskManagement')->name('task-management');

    // zoom 
    Route::resource('zoom','Marketing\ZoomController');
    Route::put('zoom-end/{meeting_id}','Marketing\ZoomController@end_meeting')->name('zoom.end');


    //Free Pt Requests
    Route::resource('free-requests', 'FreePtRequestsController');
    Route::post('assign_free_pt_coaches' , 'FreePtRequestsController@assign_free_pt_coache')->name('assign_free_pt_coache');

    //Notification
    Route::resource('notification' , 'NotificationController');
    Route::post('sendNotification' , 'NotificationController@sendNotification')->name('sendNotification');


    //Paymon Controller 
    Route::resource('paymobTransactions', 'PaymobTransactionsController');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
    // Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('profile', 'ChangePasswordController@updateProfile')->name('password.updateProfile');
        Route::post('profile/destroy', 'ChangePasswordController@destroy')->name('password.destroyProfile');
    }
});


// Route::get('send-test-mail', function () {
//     try {
//         Mail::raw('Hi, welcome user!', function ($message) {
//             $message->to('ahmedmuhammady30@gmail.com')
//                 ->subject('test email');
//         });
//         return "SENT SUCCESSFULLY";
//     } catch (\Exception $ex) {
//         dd($ex->getMessage());
//     }
// });
