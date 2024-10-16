<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function() {
    Route::post('login', 'AuthController@login');
    Route::post('sendOtp', 'AuthController@sendOtp');
    Route::post('logout', 'AuthController@logout');
    Route::get('profile', 'AuthController@profile');
    Route::post('profile', 'AuthController@updateProfile');
    Route::post('updateImage', 'AuthController@updateImage');
    Route::get('getMemberhips', 'AuthController@getMemberships');
    Route::get('trainers', 'AuthController@trainers');
    Route::get('currentTrainer', 'AuthController@currentTrainer');
    Route::get('contact','AuthController@contact');
    Route::apiResource('leads', 'V1\Admin\LeadsApiController');
    Route::get('privacy','AuthController@privacy');
    Route::get('terms','AuthController@terms');
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::post('memberNumber', 'LeadsApiController@getMemberNumber')->name('getmemberNumber');
    Route::get('checkGate', 'LeadsApiController@checkGate')->name('checkGate');
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    Route::post('memberNumber', 'LeadsApiController@getMemberNumber')->name('getmemberNumber');
});

Route::group(['prefix' => 'v1', 'as' => 'api.', 'namespace' => 'Api\V1\Admin'], function () {
    // Action
    Route::apiResource('actions', 'ActionApiController');

    // Status
    Route::apiResource('statuses', 'StatusApiController');

    // Source
    Route::apiResource('sources', 'SourceApiController');

    // Address
    Route::apiResource('addresses', 'AddressApiController');

    // Expenses Category
    Route::apiResource('expenses-categories', 'ExpensesCategoryApiController');

    // Service Types
    Route::apiResource('service-types', 'ServiceTypesApiController');

    // Services

    Route::get('info/privacy/', 'InformationApiController@privacy');
    Route::get('info/about-us/', 'InformationApiController@about_us');
    Route::get('info/rules/', 'InformationApiController@rules');
    Route::get('info/terms-conditions/', 'InformationApiController@terms_conditions');
    Route::get('info/contact-us/', 'InformationApiController@contact_us');

    Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
        Route::apiResource('', 'ServicesApiController');
        Route::get('pt/pricelist', 'PTServicesApiController@pricelist');
        Route::get('pt/trainers', 'PTServicesApiController@trainers');
        Route::get('/pt', 'PTServicesApiController@trainers_pricelist');
        Route::get('classes/pricelist', 'ClassesServicesApiController@pricelist');
        Route::get('classes/', 'ClassesServicesApiController@classes');
        Route::get('classes/current', 'ClassesServicesApiController@my_classes');
        Route::post('attendance', 'ServicesApiController@takeAttend');

        Route::get('memberships/current', 'MembershipsServicesApiController@my_membership');
        Route::get('memberships', 'MembershipsServicesApiController@memberships');
        Route::post('subscription', 'SubscriptionApiController@subscribe');
        Route::post('subscription/guest', 'SubscriptionApiController@guest_subscribe');
        Route::post('subscribtion/validation' ,'SubscriptionApiController@validate_user');
    });



    // Pricelist
    Route::apiResource('pricelists', 'PricelistApiController');

    // Asset Types
    Route::apiResource('asset-types', 'AssetTypesApiController');

    // Leads
    Route::post('leads/media', 'LeadsApiController@storeMedia')->name('leads.storeMedia');

    // Memberships
    Route::apiResource('memberships', 'MembershipsApiController');

    Route::middleware('auth:sanctum')->get('member_ship_statistics' , 'MembershipsApiController@member_ship_statistics');

    Route::middleware('auth:sanctum')->get('get_pt_memberships' , 'MembershipsApiController@get_pt_memberships');

    // Locker
    Route::apiResource('lockers', 'LockerApiController');

    // Membership Attendance
    Route::apiResource('membership-attendances', 'MembershipAttendanceApiController');

    // Expenses
    Route::apiResource('expenses', 'ExpensesApiController');

    // Invoice
    Route::apiResource('invoices', 'InvoiceApiController');

    // Payment
    Route::apiResource('payments', 'PaymentApiController');

    // Employees
    Route::apiResource('employees', 'EmployeesApiController');

    // Bonuses
    Route::apiResource('bonus', 'BonusesApiController');

    // Deductions
    Route::apiResource('deductions', 'DeductionsApiController');

    // Loans
    Route::apiResource('loans', 'LoansApiController');

    // Vacations
    Route::apiResource('vacations', 'VacationsApiController');

    // Documents
    Route::post('documents/media', 'DocumentsApiController@storeMedia')->name('documents.storeMedia');
    Route::apiResource('documents', 'DocumentsApiController');

    // Employee Settings
    Route::apiResource('employee-settings', 'EmployeeSettingsApiController');

    // Hotdeals
    Route::post('hotdeals/media', 'HotdealsApiController@storeMedia')->name('hotdeals.storeMedia');
    Route::apiResource('hotdeals', 'HotdealsApiController');
    Route::post('all-hotdeals', 'HotdealsApiController@index');

    // Gallery Section
    Route::apiResource('gallery-sections', 'GallerySectionApiController');

    // Gallery
    Route::post('galleries/media', 'GalleryApiController@storeMedia')->name('galleries.storeMedia');
    Route::apiResource('galleries', 'GalleryApiController');

    // Video Section
    Route::apiResource('video-sections', 'VideoSectionApiController');

    // Sales Plan
    Route::apiResource('sales-plans', 'SalesPlanApiController');

    // Video
    Route::post('videos/media', 'VideoApiController@storeMedia')->name('videos.storeMedia');
    Route::apiResource('videos', 'VideoApiController');

    // Sales Tiers
    Route::apiResource('sales-tiers', 'SalesTiersApiController');

    // Newssection
    Route::apiResource('news-sections', 'NewssectionApiController');

    // News
    Route::post('news/media', 'NewsApiController@storeMedia')->name('news.storeMedia');
    Route::apiResource('news', 'NewsApiController');

    // Sales Intensive
    Route::apiResource('sales-intensives', 'SalesIntensiveApiController');

    // Assets
    Route::post('assets/media', 'AssetsApiController@storeMedia')->name('assets.storeMedia');
    Route::apiResource('assets', 'AssetsApiController');

    // Maintenance Vendors
    Route::apiResource('maintenance-vendors', 'MaintenanceVendorsApiController');

    // Member Status
    Route::apiResource('member-statuses', 'MemberStatusApiController');

    // Assets Maintenance
    Route::apiResource('assets-maintenances', 'AssetsMaintenanceApiController');

    // Reminders
    Route::apiResource('reminders', 'RemindersApiController');

    // Service Option
    Route::apiResource('service-options', 'ServiceOptionApiController');

    // Service Options Pricelist
    Route::apiResource('service-options-pricelists', 'ServiceOptionsPricelistApiController');

    // Freeze Request
    Route::middleware('auth:sanctum')->apiResource('freeze-requests', 'FreezeRequestApiController');
    Route::middleware('auth:sanctum')->post('/create_freeze_request', 'FreezeRequestApiController@store');
    // Refund Reasons
    Route::apiResource('refund-reasons', 'RefundReasonsApiController');

    // Refund
    Route::apiResource('refunds', 'RefundApiController');

    // Accounts
    Route::apiResource('accounts', 'AccountsApiController');

    // External Payment
    Route::apiResource('external-payments', 'ExternalPaymentApiController');

    
    // Withdrawal
    Route::apiResource('withdrawals', 'WithdrawalApiController');

    // Timeslot
    Route::apiResource('timeslots', 'TimeslotApiController');

    // Session List
    Route::post('session-lists/media', 'SessionListApiController@storeMedia')->name('session-lists.storeMedia');
    Route::apiResource('session-lists', 'SessionListApiController');

    // Schedule
    Route::middleware('auth:sanctum')->apiResource('schedules', 'ScheduleApiController');
    Route::middleware('auth:sanctum')->post('attend_session' , 'ScheduleApiController@attend_session');
    


    // Ratings
    Route::apiResource('rate', 'RatingsApiController')->middleware('auth:sanctum');
 
    // Reasons
    Route::post('reasons/media', 'ReasonsApiController@storeMedia')->name('reasons.storeMedia');
    Route::apiResource('reasons', 'ReasonsApiController');
    
    //Member suggestion
    Route::apiResource('member-suggestion', 'MemberSuggestionApiController');

    //Rules
    Route::apiResource('rules', 'RulesApiController');

    Route::get('getNotifications', 'LeadsApiController@getNotifications')->name('getNotifications');
    Route::get('getReferal', 'LeadsApiController@getReferal')->name('getReferal');

    // Master Card
    Route::apiResource('master-cards', 'MasterCardApiController');


    // Free Private Trainer Requests
    

    Route::middleware('auth:sanctum')->get('requestPrivateTrainer' , 'FreePtRequestsController@Request_free_pt');
    Route::middleware('auth:sanctum')->get('available_free_pt' , 'FreePtRequestsController@free_pt');


    Route::middleware('auth:sanctum')->post('takeManualAttend' ,'AttendanceAPIController@takeManualAttend');

    Route::middleware('auth:sanctum')->apiResource('notifications' ,'NotificationController');
    Route::middleware('auth:sanctum')->get('clearUserNotifications' , 'NotificationController@clearUSerNotifications');
    
    Route::middleware('auth:sanctum')->post('takePtAttend', 'AttendanceAPIController@takePtAttend');
});
 